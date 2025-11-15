

const video = document.getElementById('video');
const statusDiv = document.createElement('div');
statusDiv.style.cssText = 'position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 10px; font-family: Arial; z-index: 100;';
document.body.append(statusDiv);

// Validation state
let blinkCount = 0;
let validationComplete = false;
let storedDescriptor = null;

const maxDistance = 0.6;

// Update status helper
function updateStatus(message, type = "info") {
  statusDiv.textContent = message;
  statusDiv.style.color = type === "error" ? "#ff6b6b" : 
                         type === "success" ? "#51cf66" : "white";
  console.log(message);
}

// Check registration
function checkIfFaceRegistered() {
  return localStorage.getItem('faceDescriptor') !== null;
}

// Create register button
function createRegisterButton() {
  const registerButton = document.createElement('button');
  registerButton.textContent = 'Register Your Face';
  registerButton.style.cssText = `
    position: absolute; 
    top: 150px; 
    left: 10px; 
    padding: 12px 20px; 
    background: #007bff; 
    color: white; 
    border: none; 
    border-radius: 5px; 
    font-size: 16px;
    cursor: pointer;
    z-index: 100;
  `;
  registerButton.onclick = startFaceRegistration;
  document.body.append(registerButton);
}

// Face registration
async function startFaceRegistration() {
  updateStatus("Look at the camera. Registering your face...", "info");

  const detections = await faceapi
    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
    .withFaceLandmarks()
    .withFaceDescriptors();

  if (detections.length !== 1) {
    updateStatus("Please ensure only one face is in the frame.", "error");
    return;
  }

  storedDescriptor = detections[0].descriptor;
  localStorage.setItem('faceDescriptor', JSON.stringify(Array.from(storedDescriptor)));
  updateStatus("✅ Face registered successfully!", "success");

  setTimeout(() => initializeFaceValidation(), 1000);
}

// Face validation (double blink)
function initializeFaceValidation() {
  if (!checkIfFaceRegistered()) {
    updateStatus("No registered face found. Please register first.", "error");
    createRegisterButton();
    return;
  }

  storedDescriptor = new Float32Array(JSON.parse(localStorage.getItem('faceDescriptor')));
  updateStatus("Ready for validation. Please blink twice!", "info");

  const canvas = faceapi.createCanvasFromMedia(video);
  document.body.append(canvas);
  const displaySize = { width: video.width, height: video.height };
  faceapi.matchDimensions(canvas, displaySize);

  blinkCount = 0;
  validationComplete = false;

  async function detectLoop() {
    if (validationComplete) return;

    const detections = await faceapi
      .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
      .withFaceLandmarks()
      .withFaceDescriptors();

    const ctx = canvas.getContext('2d');
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
      const leftEye = detection.landmarks.getLeftEye();
      const rightEye = detection.landmarks.getRightEye();

      const leftEAR = computeEAR(leftEye);
      const rightEAR = computeEAR(rightEye);
      const avgEAR = (leftEAR + rightEAR) / 2;

      const BLINK_THRESHOLD = 0.25;

      if (avgEAR < BLINK_THRESHOLD) {
        if (!detectLoop.blinking) {
          blinkCount++;
          detectLoop.blinking = true;
          updateStatus(`Blink detected! (${blinkCount}/2)`, "info");
        }
      } else {
        detectLoop.blinking = false;
      }

      if (blinkCount >= 2) {
        validationComplete = true;
        updateStatus("✅ Face validated successfully!", "success");
        onValidationSuccess();
      } else {
        updateStatus(`Please blink ${2 - blinkCount} more time(s)...`, "info");
      }

    } else {
      updateStatus("Face not recognized", "error");
      blinkCount = 0;
    }

    requestAnimationFrame(detectLoop);
  }

  detectLoop.blinking = false;
  detectLoop();
}

// Eye Aspect Ratio calculation for blink detection
function computeEAR(eye) {
  const a = distance(eye[1], eye[5]);
  const b = distance(eye[2], eye[4]);
  const c = distance(eye[0], eye[3]);
  return (a + b) / (2.0 * c);
}

// Euclidean distance helper
function distance(p1, p2) {
  return Math.hypot(p1.x - p2.x, p1.y - p2.y);
}

// Validation success callback
function onValidationSuccess() {
  console.log("🎉 Face validation complete!");
  setTimeout(() => {
    alert("Face validation successful! Access granted.");
  }, 1000);
}

// Start webcam
async function startVideo() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
    video.srcObject = stream;

    video.onloadedmetadata = () => {
      video.play();
      if (checkIfFaceRegistered()) {
        initializeFaceValidation();
      } else {
        createRegisterButton();
        updateStatus("Webcam started - Please register your face", "info");
      }
    };
  } catch (err) {
    console.error("❌ Webcam error:", err);
    updateStatus("Webcam access denied", "error");
  }
}

// Load face-api models
Promise.all([
  faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
  faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
  faceapi.nets.faceRecognitionNet.loadFromUri('/models')
]).then(startVideo).catch(err => {
  console.error("❌ Model loading error:", err);
  updateStatus("Failed to load face detection models", "error");
});
