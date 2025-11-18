<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Employee Attendance Page</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Blinking colon animation */
        @keyframes blink { 0%,50%,100%{opacity:1} 25%,75%{opacity:0} }
        .blink { animation: blink 1s infinite; }
        
        /* Scanner line animation */
        @keyframes scan {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }
        .scanner-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #00ffff, transparent);
            box-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff;
            animation: scan 2s ease-in-out infinite;
            z-index: 10;
        }
        
        /* Scanner frame effect */
        .scanner-frame {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 2px solid #00ffff;
            box-shadow: 0 0 20px #00ffff inset, 0 0 30px rgba(0, 255, 255, 0.3);
            border-radius: 12px;
            pointer-events: none;
            z-index: 5;
        }
        
        /* Status indicators */
        .status-scanning { background: rgba(0, 255, 255, 0.1) !important; color: #00ffff !important; }
        .status-success { background: rgba(40, 200, 120, 0.9) !important; color: white !important; }
        .status-error { background: rgba(255, 75, 75, 0.9) !important; color: white !important; }
        .status-info { background: rgba(0, 0, 0, 0.06) !important; color: black !important; }
    </style>
</head>

<body class="bg-gray-900 text-white">

<div x-data="attendanceModal()" x-init="initClock()" x-cloak class="max-w-3xl mx-auto mt-20 bg-gray-800 rounded-3xl shadow-2xl p-8">

    <!-- Title -->
    <h1 class="text-3xl font-bold text-center mb-6 flex items-center justify-center gap-2 text-cyan-400">
        <i data-lucide="calendar-days" class="w-7 h-7"></i>
        My Attendance
    </h1>

    <!-- Stylish Clock -->
    <div class="flex flex-col items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-700 rounded-2xl p-8 shadow-lg">
        <div class="text-6xl font-extrabold font-mono text-cya-400 drop-shadow-[0_0_10px_cyan] tracking-widest" x-html="currentTime"></div>
        <div class="mt-3 text-xl text-purple-400 drop-shadow-md" x-text="currentDate"></div>
    </div>

    @php
        $now = \Carbon\Carbon::now('Asia/Manila');
    @endphp

    <!-- Attendance Buttons -->
    <div class="mt-8 flex justify-center gap-6">
        <button @click="openModal('time_in')"
            class="px-10 py-4 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-semibold shadow-lg transform transition-transform hover:scale-105 flex items-center gap-2">
            <i data-lucide="log-in" class="w-6 h-6"></i> Time In
        </button>
        <button @click="openModal('time_out')"
            class="px-10 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-semibold shadow-lg transform transition-transform hover:scale-105 flex items-center gap-2">
            <i data-lucide="log-out" class="w-6 h-6"></i> Time Out
        </button>
    </div>

    <!-- FACE SCAN MODAL with Scanner Effect -->
    <template x-if="showModal">
        <div class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-50 z-50 p-4">
            <div class="relative w-full max-w-lg rounded-3xl shadow-2xl p-6 bg-white flex flex-col items-center">
                <h2 class="text-2xl font-bold mb-4 flex items-center gap-2 text-black">
                    <i data-lucide="scan" class="w-6 h-6"></i> Facial Recognition Scanner
                </h2>

                <!-- Video feed with scanner overlay -->
                <div class="relative w-full mb-3 rounded-xl overflow-hidden shadow-lg ring-2 ring-cyan-400 bg-gray-100">
                    <video id="modalVideo" autoplay muted playsinline class="w-full h-auto rounded-xl"></video>
                    
                    <!-- Scanner Frame -->
                    <div class="scanner-frame"></div>
                    
                    <!-- Scanner Line -->
                    <div class="scanner-line" x-show="scanLoopRunning || blinkLoopRunning"></div>
                    
                    <!-- Face detection indicator -->
                    <div class="absolute top-3 right-3 flex items-center gap-2" x-show="faceDetected">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-white text-sm font-semibold bg-black bg-opacity-50 px-2 py-1 rounded">Face Detected</span>
                    </div>
                    
                    <!-- Match indicator -->
                    <div class="absolute top-3 left-3 flex items-center gap-2" x-show="matchedEmployee">
                        <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        <span class="text-white text-sm font-semibold bg-black bg-opacity-50 px-2 py-1 rounded" x-text="'Matched: ' + (matchedEmployee?.name || '')"></span>
                    </div>
                    
                    <!-- Blink counter -->
                    <div class="absolute bottom-3 left-3 bg-black bg-opacity-70 text-white px-3 py-2 rounded-lg" 
                         x-show="blinkLoopRunning && blinkCount > 0">
                        <div class="flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            <span x-text="'Blinks: ' + blinkCount + '/' + REQUIRED_BLINKS"></span>
                        </div>
                    </div>
                </div>

                <!-- Status with scanner-style messages -->
                <div id="statusDivCustom" class="w-full text-center p-3 rounded-xl font-semibold shadow-md status-info">
                    Scanner Ready
                </div>

                <!-- Progress indicators -->
                <div class="w-full mt-3 space-y-2" x-show="processing">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Face Detection</span>
                        <span x-text="faceDetected ? '✅' : '⏳'"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Recognition</span>
                        <span x-text="matchedEmployee ? '✅' : '⏳'"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600" x-show="blinkLoopRunning">
                        <span>Liveness Check</span>
                        <span x-text="blinkCount >= REQUIRED_BLINKS ? '✅' : '⏳'"></span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-5 flex justify-end gap-3 w-full">
                    <button @click="closeModal" class="px-5 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-xl flex items-center gap-1 shadow-md">
                        <i data-lucide="x-circle" class="w-5 h-5"></i> Cancel
                    </button>

                    <button @click="manualRetry" :disabled="processing" class="px-5 py-2 bg-cyan-200 hover:bg-cyan-300 text-black rounded-xl flex items-center gap-1 shadow-md">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i> Retry Scan
                    </button>
                </div>

                <div class="absolute inset-0 rounded-3xl pointer-events-none"></div>
            </div>
        </div>
    </template>

</div>

<script>
function attendanceModal() {
    return {
        currentTime: '',
        currentDate: '',

        initClock() {
            const updateClock = () => {
                const now = new Date();
                let h = String(now.getHours()).padStart(2,'0');
                let m = String(now.getMinutes()).padStart(2,'0');
                let s = String(now.getSeconds()).padStart(2,'0');
                this.currentTime = `${h}<span class="blink">:</span>${m}<span class="blink">:</span>${s}`;
                this.currentDate = now.toLocaleDateString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            };
            updateClock();
            setInterval(updateClock, 1000);
        },

        // UI state
        showModal: false,
        actionType: '',
        videoStream: null,
        processing: false,
        faceDetected: false,

        // face-api & matching state
        employeesDescriptors: [],
        MAX_DISTANCE: 0.6,

        // blink / EAR validation params
        EAR_THRESHOLD: 0.28,
        BLINK_COOLDOWN: 400,
        BLINK_SEQUENCE_TIMEOUT: 6000,
        REQUIRED_BLINKS: 2,

        // runtime blink state
        matchedEmployee: null,
        blinkCount: 0,
        blinkedRecently: false,
        lastBlinkTime: 0,
        blinkLoopRunning: false,
        scanLoopRunning: false,

        async openModal(action) {
            this.actionType = action;
            this.showModal = true;
            this.processing = false;
            this.matchedEmployee = null;
            this.faceDetected = false;
            this.updateStatus('🚀 Initializing facial scanner...', 'info');

            try {
                this.updateStatus('📦 Loading recognition models...', 'info');
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);

                await this.fetchAllDescriptors();
                await this.startCamera();

                this.processing = true;
                this.updateStatus('🔍 Scanning for registered employee. Please face the scanner.', 'scanning');
                this.scanLoopRunning = true;
                this.scanLoop();
            } catch (err) {
                console.error(err);
                this.updateStatus('❌ Initialization error: ' + (err.message || ''), 'error');
                this.processing = false;
            }
        },

        closeModal() {
            this.stopCamera();
            this.showModal = false;
            this.processing = false;
            this.employeesDescriptors = [];
            this.resetBlinkState();
            this.matchedEmployee = null;
            this.faceDetected = false;
        },

        manualRetry() {
            if (!this.showModal) return;
            this.updateStatus('🔄 Retrying facial scan...', 'info');
            this.processing = true;
            this.scanLoopRunning = true;
            this.matchedEmployee = null;
            this.resetBlinkState();
            this.scanLoop();
        },

        updateStatus(msg, type = 'info') {
            const el = document.getElementById('statusDivCustom');
            if (!el) return;
            el.textContent = msg;
            el.className = 'w-full text-center p-3 rounded-xl font-semibold shadow-md status-' + 
                (type === 'error' ? 'error' : 
                 type === 'success' ? 'success' : 
                 type === 'scanning' ? 'scanning' : 'info');
        },

        async fetchAllDescriptors() {
            try {
                const res = await fetch('/attendance/descriptors');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Failed loading descriptors');

                this.employeesDescriptors = json.data.map(e => ({
                    id: e.id,
                    name: e.name,
                    position: e.position,
                    descriptor: new Float32Array(e.descriptor)
                }));

                if (this.employeesDescriptors.length === 0) {
                    this.updateStatus('❌ No registered faces in system.', 'error');
                    throw new Error('No descriptors found');
                }

                this.updateStatus('✅ Database loaded. Scanner ready.', 'info');
            } catch (err) {
                console.error(err);
                this.updateStatus('❌ Failed to load employee database', 'error');
                throw err;
            }
        },

        async startCamera() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const cameras = devices.filter(d => d.kind === "videoinput");

                if (cameras.length === 0) {
                    this.updateStatus('❌ No camera detected', 'error');
                    throw new Error('No camera found');
                }

                let externalCam = cameras.find(cam =>
                    cam.label.toLowerCase().includes("usb") ||
                    cam.label.toLowerCase().includes("webcam") ||
                    cam.label.toLowerCase().includes("external")
                );

                let chosenCam = externalCam ?? cameras[0];

                this.videoStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        deviceId: { exact: chosenCam.deviceId },
                        width: 640,
                        height: 480
                    }
                });

                const video = document.getElementById('modalVideo');
                video.srcObject = this.videoStream;
                await video.play();

                this.updateStatus(`📷 Camera active: ${chosenCam.label}`, 'info');
            } catch (err) {
                console.error(err);
                this.updateStatus('❌ Camera access failed', 'error');
                throw err;
            }
        },

        stopCamera() {
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(t => t.stop());
                this.videoStream = null;
            }
            this.scanLoopRunning = false;
            this.blinkLoopRunning = false;
            this.resetBlinkState();
        },

        resetBlinkState() {
            this.blinkCount = 0;
            this.blinkedRecently = false;
            this.lastBlinkTime = 0;
            this.blinkLoopRunning = false;
        },

        getEAR(eye) {
            const a = this.distance(eye[1], eye[5]);
            const b = this.distance(eye[2], eye[4]);
            const c = this.distance(eye[0], eye[3]);
            if (c === 0) return 0;
            return (a + b) / (2.0 * c);
        },

        distance(p1, p2) {
            return Math.hypot(p1.x - p2.x, p1.y - p2.y);
        },

        async scanLoop() {
            const video = document.getElementById('modalVideo');
            if (!video || !this.scanLoopRunning) return;

            try {
                const detections = await faceapi
                    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                // Update face detection status
                this.faceDetected = detections && detections.length > 0;

                if (!this.faceDetected) {
                    this.updateStatus('👤 No face detected. Please position yourself in the scanner.', 'info');
                    if (this.scanLoopRunning) requestAnimationFrame(() => this.scanLoop());
                    return;
                }

                this.updateStatus('✅ Face detected. Analyzing identity...', 'scanning');

                const descriptor = detections[0].descriptor;
                let best = { id: null, name: null, position: null, distance: Infinity };
                let bestEmpDescriptor = null;

                for (const emp of this.employeesDescriptors) {
                    const dist = faceapi.euclideanDistance(descriptor, emp.descriptor);
                    if (dist < best.distance) {
                        best = { id: emp.id, name: emp.name, position: emp.position, distance: dist };
                        bestEmpDescriptor = emp.descriptor;
                    }
                }

                if (best.distance <= this.MAX_DISTANCE) {
                    this.scanLoopRunning = false;
                    this.matchedEmployee = { ...best, descriptor: bestEmpDescriptor };
                    this.updateStatus(`✅ Identity confirmed: ${best.name}. Please blink ${this.REQUIRED_BLINKS} times for verification.`, 'scanning');

                    this.resetBlinkState();
                    this.blinkLoopRunning = true;
                    this.blinkValidationLoop(detections[0]);
                    return;
                } else {
                    this.updateStatus('❌ Face not recognized. Please ensure you are registered.', 'error');
                }

                if (this.scanLoopRunning) requestAnimationFrame(() => this.scanLoop());
            } catch (err) {
                console.error(err);
                this.updateStatus('❌ Scanner error', 'error');
                this.scanLoopRunning = false;
                this.processing = false;
            }
        },

        async blinkValidationLoop(initialDetection = null) {
            const video = document.getElementById('modalVideo');
            this.lastBlinkTime = Date.now();

            const loop = async () => {
                if (!this.blinkLoopRunning) return;

                try {
                    if (!video || video.readyState < 2) {
                        requestAnimationFrame(loop);
                        return;
                    }

                    const detections = await faceapi
                        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    this.faceDetected = detections && detections.length > 0;

                    if (!this.faceDetected) {
                        this.updateStatus('👤 Face lost. Please remain in frame.', 'info');
                        if ((Date.now() - this.lastBlinkTime) > this.BLINK_SEQUENCE_TIMEOUT) {
                            this.updateStatus('⏰ Verification timeout. Restarting scan.', 'error');
                            this.blinkLoopRunning = false;
                            this.matchedEmployee = null;
                            this.scanLoopRunning = true;
                            this.scanLoop();
                            return;
                        }
                        requestAnimationFrame(loop);
                        return;
                    }

                    const detection = detections[0];
                    const distance = faceapi.euclideanDistance(detection.descriptor, this.matchedEmployee.descriptor);

                    if (distance > this.MAX_DISTANCE) {
                        this.updateStatus('❌ Identity changed. Restarting scan.', 'error');
                        this.resetBlinkState();
                        this.matchedEmployee = null;
                        this.blinkLoopRunning = false;
                        this.scanLoopRunning = true;
                        this.scanLoop();
                        return;
                    }

                    const leftEyePts = detection.landmarks.getLeftEye().map(p => ({ x: p.x, y: p.y }));
                    const rightEyePts = detection.landmarks.getRightEye().map(p => ({ x: p.x, y: p.y }));
                    const leftEAR = this.getEAR(leftEyePts);
                    const rightEAR = this.getEAR(rightEyePts);
                    const ear = (leftEAR + rightEAR) / 2;

                    const now = Date.now();

                    if (ear < this.EAR_THRESHOLD && !this.blinkedRecently && (now - this.lastBlinkTime) > this.BLINK_COOLDOWN) {
                        this.blinkCount++;
                        this.blinkedRecently = true;
                        this.lastBlinkTime = now;
                        this.updateStatus(`👁️ Blink detected! (${this.blinkCount}/${this.REQUIRED_BLINKS})`, 'scanning');

                        if (this.blinkCount >= this.REQUIRED_BLINKS) {
                            this.updateStatus('🎉 Verification successful! Recording attendance...', 'success');
                            this.blinkLoopRunning = false;

                            try {
                                await this.recordAttendanceAJAX(this.matchedEmployee.id, this.actionType, this.matchedEmployee.name, this.matchedEmployee.position);
                            } catch (err) {
                                console.error('Error recording attendance:', err);
                                this.updateStatus('❌ Error recording attendance', 'error');
                            }

                            setTimeout(() => {
                                this.stopCamera();
                                this.showModal = false;
                                this.resetBlinkState();
                                this.matchedEmployee = null;
                            }, 700);
                            return;
                        }
                    } else if (ear >= this.EAR_THRESHOLD) {
                        this.blinkedRecently = false;
                    }

                    if (this.blinkCount > 0 && (now - this.lastBlinkTime) > this.BLINK_SEQUENCE_TIMEOUT) {
                        this.blinkCount = 0;
                        this.updateStatus('🔄 Blink sequence reset. Please blink again.', 'info');
                    }

                    if (this.blinkLoopRunning) requestAnimationFrame(loop);
                } catch (err) {
                    console.error(err);
                    this.updateStatus('❌ Scanner error during verification', 'error');
                    this.blinkLoopRunning = false;
                    this.scanLoopRunning = false;
                    this.processing = false;
                }
            };

            requestAnimationFrame(loop);
        },

        async recordAttendanceAJAX(employeeId, action, name = '', position = '') {
            this.updateStatus('💾 Recording attendance...', 'info');
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/attendance/record', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        employee_id: employeeId,
                        action_type: action
                    })
                });

                const json = await res.json();
                if (!json.success) {
                    this.updateStatus(json.message || 'Failed to record', 'error');
                    Swal.fire({ icon: 'error', title: 'Error', text: json.message || 'Failed to record attendance.' });
                    return;
                }

                const uppercaseAction = action === 'time_in' ? 'TIME IN' : 'TIME OUT';
                const displayName = name || (json.employee_name ?? '');
                const displayPosition = position || (json.position ?? '');

                await Swal.fire({
                    icon: 'success',
                    title: `SUCCESSFULLY ${uppercaseAction} EMPLOYEE: ${displayName} — ${displayPosition}`,
                    text: json.message || 'Attendance logged successfully.',
                    confirmButtonColor: '#2563eb'
                });

                window.location.reload();
            } catch (err) {
                console.error(err);
                this.updateStatus('❌ Server error', 'error');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Server error while recording attendance.' });
                throw err;
            }
        }
    }
}

document.addEventListener("alpine:init", () => {
    lucide.createIcons();
});
</script>
</body>
</html>