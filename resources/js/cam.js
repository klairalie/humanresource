
        /* ========= CONFIG / DOM ========= */
        const video = document.getElementById('video');
        const canvas = document.getElementById('overlay');
        const statusDiv = document.getElementById('status-div');
        const registerButton = document.getElementById('register-btn');
        const retakeButton = document.getElementById('retake-btn');
        const turnOnButton = document.getElementById('turnon-btn');
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
            if (type === 'error') {
                statusDiv.classList.remove('text-white');
                statusDiv.classList.add('text-red-200');
            } else if (type === 'success') {
                statusDiv.classList.remove('text-white');
                statusDiv.classList.add('text-green-200');
            } else {
                statusDiv.classList.remove('text-red-200','text-green-200');
                statusDiv.classList.add('text-white');
            }
        }

        function enableSaveButton(enabled) {
            if (!saveButton) return;
            saveButton.disabled = !enabled;
            saveButton.classList.toggle('opacity-50', !enabled);
            saveButton.classList.toggle('cursor-not-allowed', !enabled);
        }

        function ensurePreviewArea() {
            const container = document.getElementById('descriptor-preview-container');
            if (document.getElementById('descriptor-preview')) return;

            const wrapper = document.createElement('div');
            wrapper.id = 'descriptor-preview';
            wrapper.className = 'mt-4 p-3 bg-white border border-gray-200 rounded-md text-sm text-black max-w-md mx-auto';

            wrapper.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <strong class="text-black">Face Registration Preview</strong>
                    <div id="similarity-meter" class="text-xs text-gray-600">No scans yet</div>
                </div>
                <div id="descriptor-rows" class="space-y-2 text-xs overflow-auto" style="max-height:220px"></div>
            `;

            container.appendChild(wrapper);

            // retake button already exists in DOM; attach listener here
            retakeButton.addEventListener('click', () => {
                resetScans();
                // show Register button again and hide retake
                registerButton.style.display = cameraActive ? 'inline-block' : 'none';
                retakeButton.style.display = 'none';
            });
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
            previewCanvas.width = video.videoWidth || 640;
            previewCanvas.height = video.videoHeight || 480;
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
            const rows = document.getElementById('descriptor-rows');
            if (rows) rows.innerHTML = '';
            const meter = document.getElementById('similarity-meter');
            if (meter) meter.textContent = 'Scan reset. Begin Scan #1.';
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
                    // Show Register button now that camera is active (Option A)
                    registerButton.style.display = 'inline-block';
                };
            } catch (err) {
                updateStatus('Unable to access camera. Please allow camera permissions.', 'error');
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
            updateStatus('Camera inactive. Click Turn On Camera.');
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

        /* ========== UI: button behaviors ========== */
        // Turn On Camera button (explicit)
        turnOnButton.addEventListener('click', async () => {
            if (!modelsLoaded) {
                updateStatus('Models still loading. Please wait...', 'info');
                return;
            }
            await enableCamera();
            // ensure register visible
            registerButton.style.display = 'inline-block';
            // hide retake until second scan
            retakeButton.style.display = 'none';
        });

        // Register button (performs scanning flow)
        registerButton.addEventListener('click', async () => {
            if (!modelsLoaded) { updateStatus('Models still loading. Please wait...'); return; }
            if (!cameraActive) { updateStatus('Please turn on the camera first.', 'error'); return; }

            const rows = document.getElementById('descriptor-rows');

            // ---- SCAN #1 ----
            if (!firstScan) {
                updateStatus('Scanning (Scan #1) — hold still...', 'info');
                const descArray = await captureSingleDescriptor();
                if (!descArray) return;

                firstScan = new Float32Array(descArray);
                firstPreviewImg = capturePreviewImage();

                rows.innerHTML = `<div class="flex items-center gap-2">
                    <img src="${firstPreviewImg}" class="w-24 h-24 rounded border"/>
                    <div><strong>Scan #1:</strong> ${descriptorPreviewString(firstScan)}</div>
                </div>`;

                updateStatus('Scan #1 captured. Checking for duplicates...', 'info');

                faceDescriptorInput.value = JSON.stringify(Array.from(firstScan));

                // Disable save while checking
                enableSaveButton(false);

                // AJAX duplicate check
                try {
                    const res = await fetch('/check-face-duplicate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            face_descriptor: faceDescriptorInput.value,
                            employeeprofiles_id: "{{ $employee->employeeprofiles_id }}"
                        })
                    });
                    const result = await res.json();

                    if(result.status === 'duplicate') {
                        updateStatus(`⚠️ Duplicate detected! Already registered under: ${result.matched_employee}`, 'error');
                        alert(`Duplicate face detected! Already registered under: ${result.matched_employee}`);
                        resetScans(); // resets all scans and disables save
                    } else {
                        updateStatus('✅ Face is unique. Proceed with Scan #2 for consistency.', 'success');
                        // keep Register visible so user can do scan #2
                        enableSaveButton(false); // keep save disabled until average after #2
                    }
                } catch(err) {
                    console.error(err);
                    updateStatus('Error checking face duplication.', 'error');
                    enableSaveButton(false); // prevent save if check fails
                }

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
                    <img src="${secondPreviewImg}" class="w-24 h-24 rounded border"/>
                    <div><strong>Scan #2:</strong> ${descriptorPreviewString(secondScan)} — ${matchText} (distance=${distance.toFixed(4)})</div>
                </div>`);

                document.getElementById('similarity-meter').textContent = `Scan #2 Result: ${matchText}`;

                if (distance > SCAN_DISTANCE_THRESHOLD) {
                    updateStatus('Scan #2 did not match Scan #1. Please retake.', 'error');
                    // show retake so they can try again
                    retakeButton.style.display = 'inline-block';
                    // hide register as per requirement after second scan
                    registerButton.style.display = 'none';
                    enableSaveButton(false);
                    // keep face_descriptor empty
                    faceDescriptorInput.value = '';
                    return;
                } else {
                    updateStatus('✅ Scans consistent. Face descriptor averaged and ready to save.', 'success');
                    // Average descriptor for saving
                    const avgDescriptor = averageDescriptors(firstScan, secondScan);
                    faceDescriptorInput.value = JSON.stringify(Array.from(avgDescriptor));
                    enableSaveButton(true);

                    // After successful second scan, hide Register and show Retake
                    registerButton.style.display = 'none';
                    retakeButton.style.display = 'inline-block';
                }
                return;
            }

            // If both scans present (should not usually happen unless user re-clicks), ignore or instruct to retake
            if (firstScan && secondScan) {
                updateStatus('Two scans already recorded. Click Retake to discard and re-register.', 'info');
            }
        });

        // When the page unloads, stop camera stream (cleanup)
        window.addEventListener('beforeunload', () => {
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(t => t.stop());
            }
        });
