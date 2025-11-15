const video = document.getElementById('video');
const canvas = document.getElementById('overlay');
const statusDiv = document.getElementById('status-div');
const registerButton = document.getElementById('register-btn');

const ctx = canvas.getContext("2d");

// States
let blinkCount = 0;
let validationComplete = false;
let storedDescriptor = null;

const maxDistance = 0.6;
let cameraActive = false;

// Update status
function updateStatus(msg, type = "info") {
    statusDiv.textContent = msg;
    statusDiv.style.color =
        type === "error" ? "#ff6b6b" :
        type === "success" ? "#51cf66" : "white";
}

// Check if registered
function checkIfFaceRegistered() {
    return localStorage.getItem("faceDescriptor") !== null;
}

/* ============================================================
   CAMERA WILL NOT START UNTIL USER CLICKS REGISTER FACE
   ============================================================ */
async function enableCamera() {
    if (cameraActive) return;

    updateStatus("Starting camera...");

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480 }
        });

        video.srcObject = stream;
        cameraActive = true;

        video.onloadedmetadata = () => {
            video.play();

            updateStatus("Camera ready. Look at the camera.");

            if (checkIfFaceRegistered()) {
                updateStatus("Registered face found. Validate with blink.");
                initializeFaceValidation();
            } else {
                updateStatus("Click again to register your face.");
            }
        };
    } catch (err) {
        updateStatus("Camera access denied.", "error");
        console.error(err);
    }
}

/* ============================================================
   FIRST CLICK → start camera
   SECOND CLICK → register face
   ============================================================ */
registerButton.addEventListener("click", () => {
    if (!cameraActive) {
        enableCamera();   // Camera starts on first click
    } else {
        startFaceRegistration(); // Proceed with registration
    }
});

/* ============================================================
   FACE REGISTRATION
   ============================================================ */
async function startFaceRegistration() {
    updateStatus("Look at the camera. Registering...", "info");

    const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptors();

    if (detections.length !== 1) {
        updateStatus("Please ensure only one face is visible.", "error");
        return;
    }

    storedDescriptor = detections[0].descriptor;
    localStorage.setItem("faceDescriptor", JSON.stringify(Array.from(storedDescriptor)));

    updateStatus("Face registered successfully!", "success");

    setTimeout(() => initializeFaceValidation(), 800);
}

/* ============================================================
   VALIDATION
   ============================================================ */
function initializeFaceValidation() {
    if (!checkIfFaceRegistered()) {
        updateStatus("No registered face. Please register.", "error");
        return;
    }

    storedDescriptor = new Float32Array(JSON.parse(localStorage.getItem("faceDescriptor")));
    updateStatus("Ready for validation. Please blink twice!", "info");

    blinkCount = 0;
    validationComplete = false;

    detectLoop();
}

async function detectLoop() {
    if (validationComplete) return;

    const displaySize = { width: 640, height: 480 };

    const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptors();

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (detections.length === 0) {
        updateStatus("No face detected", "error");
        requestAnimationFrame(detectLoop);
        return;
    }

    const resized = faceapi.resizeResults(detections, displaySize);

    faceapi.draw.drawDetections(canvas, resized);
    faceapi.draw.drawFaceLandmarks(canvas, resized);

    const detection = detections[0];
    const distance = faceapi.euclideanDistance(detection.descriptor, storedDescriptor);

    if (distance < maxDistance) {
        const leftEAR = computeEAR(detection.landmarks.getLeftEye());
        const rightEAR = computeEAR(detection.landmarks.getRightEye());
        const avgEAR = (leftEAR + rightEAR) / 2;

        const BLINK_THRESHOLD = 0.25;

        if (avgEAR < BLINK_THRESHOLD) {
            if (!detectLoop.blinking) {
                blinkCount++;
                detectLoop.blinking = true;
                updateStatus(`Blink detected! (${blinkCount}/2)`);
            }
        } else {
            detectLoop.blinking = false;
        }

        if (blinkCount >= 2) {
            validationComplete = true;
            updateStatus("Face validated successfully!", "success");
            return onValidationSuccess();
        }

    } else {
        updateStatus("Face not recognized.", "error");
        blinkCount = 0;
    }

    requestAnimationFrame(detectLoop);
}

detectLoop.blinking = false;

// EAR calculation
function computeEAR(eye) {
    const a = distance(eye[1], eye[5]);
    const b = distance(eye[2], eye[4]);
    const c = distance(eye[0], eye[3]);
    return (a + b) / (2.0 * c);
}

function distance(p1, p2) {
    return Math.hypot(p1.x - p2.x, p1.y - p2.y);
}

function onValidationSuccess() {
    setTimeout(() => alert("Face validation successful!"), 300);
}

/* ============================================================
   MODELS ONLY LOAD — CAMERA WAITS FOR USER
   ============================================================ */
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
]).then(() => {
    updateStatus("Camera inactive. Click Register Face.");
});
