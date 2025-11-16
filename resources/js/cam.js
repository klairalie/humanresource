/* ========= CONFIG / DOM ========= */
const video = document.getElementById('video');
const canvas = document.getElementById('overlay');
const statusDiv = document.getElementById('status-div');
const registerButton = document.getElementById('register-btn');
const faceDescriptorInput = document.getElementById('face_descriptor');
const ctx = canvas.getContext("2d");

const form = document.querySelector('form');
const saveButton = form ? form.querySelector('button[type="submit"]') : null;

/* ======= STATE ======= */
let cameraActive = false;
let firstScan = null;       // Float32Array
let firstPreviewImg = null; // base64
let secondScan = null;      // Float32Array
let secondPreviewImg = null; // base64
let modelsLoaded = false;

/* Thresholds */
const SCAN_DISTANCE_THRESHOLD = 0.45;
const MAX_FACE_DETECTIONS = 1;

/* ======= HELPERS ======= */
function updateStatus(msg, type = 'info') {
    statusDiv.textContent = msg;
    statusDiv.style.color =
        type === 'error' ? '#ff6b6b' :
        type === 'success' ? '#51cf66' : 'white';
}

function enableSaveButton(enabled) {
    if (!saveButton) return;
    saveButton.disabled = !enabled;
    saveButton.classList.toggle('opacity-50', !enabled);
}

function ensurePreviewArea() {
    if (document.getElementById('descriptor-preview')) return;

    const wrapper = document.createElement('div');
    wrapper.id = 'descriptor-preview';
    wrapper.className = 'mt-4 p-3 bg-gray-50 border border-gray-200 rounded-md text-sm text-black max-w-md mx-auto';

    wrapper.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <strong class="text-black">Face Registration Preview</strong>
            <div id="similarity-meter" class="text-xs text-gray-600">No scans yet</div>
        </div>
        <div id="descriptor-rows" class="space-y-2 text-xs overflow-auto" style="max-height:220px"></div>
        <div class="mt-3 flex gap-2">
            <button id="retake-btn" type="button"
                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Retake Face</button>
        </div>
    `;

    const parent = document.getElementById('camera-wrapper') || document.body;
    parent.insertAdjacentElement('afterend', wrapper);

    document.getElementById('retake-btn').addEventListener('click', resetScans);
}

function descriptorPreviewString(arr) {
    if (!arr) return '';
    const first = Array.from(arr.slice(0, 8)).map(n => Number(n).toFixed(4));
    return `[${first.join(', ')} ...]`;
}

function averageDescriptors(a, b) {
    const out = new Float32Array(a.length);
    for (let i = 0; i < a.length; i++) out[i] = (a[i] + b[i]) / 2;
    return out;
}

function euclideanDistance(a, b) {
    let sum = 0;
    for (let i = 0; i < a.length; i++) {
        const d = a[i] - b[i];
        sum += d * d;
    }
    return Math.sqrt(sum);
}

function drawDetections(detections) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (!detections || detections.length === 0) return;
    const displaySize = { width: canvas.width, height: canvas.height };
    const resized = faceapi.resizeResults(detections, displaySize);
    faceapi.draw.drawDetections(canvas, resized);
    faceapi.draw.drawFaceLandmarks(canvas, resized);
}

function capturePreviewImage() {
    const previewCanvas = document.createElement('canvas');
    previewCanvas.width = video.videoWidth;
    previewCanvas.height = video.videoHeight;
    const previewCtx = previewCanvas.getContext('2d');
    previewCtx.drawImage(video, 0, 0, previewCanvas.width, previewCanvas.height);
    return previewCanvas.toDataURL('image/png');
}

function resetScans() {
    firstScan = null;
    firstPreviewImg = null;
    secondScan = null;
    secondPreviewImg = null;
    faceDescriptorInput.value = '';
    document.getElementById('descriptor-rows').innerHTML = '';
    document.getElementById('similarity-meter').textContent = 'Scan reset. Begin Scan #1.';
    updateStatus('Scan reset. Click Register Face to begin.', 'info');
    enableSaveButton(false);
}

/* ========== CAMERA CONTROL ========== */
async function enableCamera() {
    if (cameraActive) return;
    updateStatus('Starting camera...');
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
        video.srcObject = stream;
        cameraActive = true;
        video.onloadedmetadata = () => {
            video.play();
            updateStatus('Camera ready. Position your face and click Register Face to start Scan #1.', 'info');
        };
    } catch (err) {
        updateStatus('Unable to access camera.', 'error');
        console.error(err);
    }
}

/* ========== MODEL LOADING ========== */
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
]).then(() => {
    modelsLoaded = true;
    updateStatus('Camera inactive. Click Register Face.');
    ensurePreviewArea();
    enableSaveButton(false);
}).catch(err => {
    updateStatus('Failed to load models.', 'error');
    console.error(err);
});

/* ========== CAPTURE DESCRIPTOR ========== */
async function captureSingleDescriptor() {
    if (!cameraActive) {
        updateStatus('Camera is not active.', 'error');
        return null;
    }
    const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptors();

    if (!detections || detections.length === 0) {
        updateStatus('No face detected. Make sure your face is visible and well-lit.', 'error');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return null;
    }

    if (detections.length > MAX_FACE_DETECTIONS) {
        updateStatus('Please ensure only one face is visible.', 'error');
        drawDetections(detections);
        return null;
    }

    drawDetections(detections);
    return Array.from(detections[0].descriptor);
}

/* ========== REGISTER BUTTON ========== */
registerButton.addEventListener('click', async () => {
    if (!modelsLoaded) { updateStatus('Models still loading. Please wait...'); return; }
    if (!cameraActive) { await enableCamera(); return; }

    const rows = document.getElementById('descriptor-rows');

    // ---- SCAN #1 ----
    if (!firstScan) {
        updateStatus('Scanning (Scan #1) — hold still...', 'info');
        const descArray = await captureSingleDescriptor();
        if (!descArray) return;

        firstScan = new Float32Array(descArray);
        firstPreviewImg = capturePreviewImage();

        rows.innerHTML = `<div class="flex items-center gap-2">
            <img src="${firstPreviewImg}" class="w-32 h-32 rounded border"/>
            <div><strong>Scan #1:</strong> ${descriptorPreviewString(firstScan)}</div>
        </div>`;

        updateStatus('Scan #1 captured. Checking for duplicates...', 'info');

        faceDescriptorInput.value = JSON.stringify(Array.from(firstScan));

        // AJAX duplicate check
        fetch('/check-face-duplicate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                face_descriptor: faceDescriptorInput.value,
                employeeprofiles_id: "{{ $employee->employeeprofiles_id }}" 
            })
        })
        .then(res => res.json())
        .then(result => {
            if(result.status === 'duplicate') {
                updateStatus(`⚠️ Duplicate detected! Already registered under: ${result.matched_employee}`, 'error');
                enableSaveButton(false);
                alert(`Duplicate face detected! Already registered under: ${result.matched_employee}`);
                resetScans();
            } else {
                updateStatus('✅ Face is unique. Proceed with Scan #2 for consistency.', 'success');
                enableSaveButton(true);
            }
        })
        .catch(err => {
            console.error(err);
            updateStatus('Error checking face duplication.', 'error');
            enableSaveButton(true);
        });

        return;
    }

    // ---- SCAN #2 ----
    if (firstScan && !secondScan) {
        updateStatus('Scanning (Scan #2) — hold still...', 'info');
        const descArray = await captureSingleDescriptor();
        if (!descArray) return;

        secondScan = new Float32Array(descArray);
        secondPreviewImg = capturePreviewImage();

        const distance = euclideanDistance(firstScan, secondScan);
        const matchText = distance <= SCAN_DISTANCE_THRESHOLD ? '✅ Match' : '❌ Mismatch';

        rows.insertAdjacentHTML('beforeend', `<div class="flex items-center gap-2">
            <img src="${secondPreviewImg}" class="w-32 h-32 rounded border"/>
            <div><strong>Scan #2:</strong> ${descriptorPreviewString(secondScan)} — ${matchText} (distance=${distance.toFixed(4)})</div>
        </div>`);

        document.getElementById('similarity-meter').textContent = `Scan #2 Result: ${matchText}`;

        if (distance > SCAN_DISTANCE_THRESHOLD) {
            updateStatus('Scan #2 did not match Scan #1. Please retake.', 'error');
            resetScans();
        } else {
            updateStatus('✅ Scans consistent. Face descriptor averaged and ready to save.', 'success');
            // Average descriptor for saving
            const avgDescriptor = averageDescriptors(firstScan, secondScan);
            faceDescriptorInput.value = JSON.stringify(Array.from(avgDescriptor));
            enableSaveButton(true);
        }
        return;
    }
});
