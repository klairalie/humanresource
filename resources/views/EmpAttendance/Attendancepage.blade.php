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

    <!-- face-api.js (using CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
      /* Ensure canvas overlays video correctly inside modal */
      .video-wrapper { position: relative; width: 100%; }
      #modalVideo { display: block; width: 100%; border-radius: 8px; }
      #overlayCanvas { position: absolute; top: 0; left: 0; pointer-events: none; }
      #statusDivCustom { padding: .5rem; border-radius: .375rem; color: white; background: rgba(0,0,0,0.6); margin-top: .5rem; }
    </style>
</head>

<body class="bg-gray-100 text-black relative">

    <div 
        x-data="attendanceModal()" 
        x-cloak 
        class="max-w-5xl mx-auto p-5 mt-10 bg-white rounded-lg shadow-lg border border-gray-200 relative mr-90">

        <!-- Title -->
        <div class="flex items-center justify-between mb-4 border-b pb-2">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-6 h-6"></i>
                My Attendance Records
            </h1>
        </div>

        <!-- Scrollable Table -->
        <div class="overflow-y-auto max-h-[70vh] pr-6">
            <table class="w-4xl border border-gray-200 text-sm">
                <thead class="bg-gray-100 sticky top-0 z-10">
                    <tr class="text-left">
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Time In</th>
                        <th class="px-4 py-2 border">Time Out</th>
                        <th class="px-4 py-2 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 border">{{ $attendance->employeeprofiles?->last_name }}</td>
                            <td class="px-4 py-2 border">{{ $attendance->date }}</td>
                            <td class="px-4 py-2 border">
                                {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-2 border">{{ $attendance->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Fixed Time Buttons -->
        <div class="fixed right-30 top-1/2 -translate-y-1/2 flex flex-col gap-6 z-50">
            <button @click="openModal('time_in')"
                class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                Time In
            </button>
            <button @click="openModal('time_out')"
                class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Time Out
            </button>
        </div>

        <!-- FACE SCAN MODAL -->
        <template x-if="showModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-xl relative">

                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="camera" class="w-6 h-6"></i>
                        Facial Attendance Scan
                    </h2>

                    <!-- Employee selector -->
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Who's clocking?</label>
                        <select x-model="selectedEmployeeId" id="employeeSelect" class="w-full border p-2 rounded">
                            <option value="">-- Select employee --</option>
                        </select>
                    </div>

                    <!-- Video Feed + overlay canvas -->
                    <div class="video-wrapper mb-3">
                        <video id="modalVideo" width="500" height="350" autoplay muted playsinline class="rounded-lg bg-black w-full"></video>
                        <canvas id="overlayCanvas"></canvas>
                    </div>

                    <div id="statusDivCustom">Camera inactive</div>

                    <div class="mt-5 flex justify-end gap-2">
                        <button @click="closeModal"
                            class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-5 h-5"></i> Cancel
                        </button>

                        <button :disabled="processing" @click="startValidationFlow"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> <span x-text="buttonText"></span>
                        </button>
                    </div>

                </div>
            </div>
        </template>

    </div>

<script>
function attendanceModal() {
    return {
        // UI state
        showModal: false,
        actionType: '',
        videoStream: null,
        selectedEmployeeId: '',
        processing: false,
        buttonText: 'Start Validation',

        // face-api & validation state
        storedDescriptor: null, // Float32Array
        detectLoopRunning: false,
        blinkCount: 0,
        blinkedRecently: false,
        lastBlinkTime: 0,
        validationComplete: false,

        // thresholds
        MAX_DISTANCE: 0.6,
        EAR_THRESHOLD: 0.28,
        BLINK_COOLDOWN: 400, // ms

        async openModal(action) {
            this.actionType = action;
            this.showModal = true;
            this.buttonText = 'Start Validation';
            this.selectedEmployeeId = '';
            this.resetValidationState();
            await this.fetchEmployees();
            await this.startCamera();
        },

        closeModal() {
            this.stopCamera();
            this.showModal = false;
            this.resetValidationState();
        },

        async fetchEmployees() {
            try {
                const res = await fetch('/attendance/employees');
                const json = await res.json();
                if (!json.success) {
                    this.updateStatus('Failed to load employees', 'error');
                    return;
                }
                const select = document.getElementById('employeeSelect');
                // clear existing options except placeholder
                select.innerHTML = '<option value="">-- Select employee --</option>';
                json.employees.forEach(e => {
                    // adapt to either id field naming
                    const id = e.employeeprofiles_id ?? e.id ?? e.employeeprofiles_id;
                    const opt = document.createElement('option');
                    opt.value = id;
                    opt.textContent = `${e.full_name ?? (e.first_name + ' ' + e.last_name)} — ${e.position ?? ''}`;
                    select.appendChild(opt);
                });
            } catch (err) {
                console.error(err);
                this.updateStatus('Error fetching employees', 'error');
            }
        },

        updateStatus(msg, type = 'info') {
            const el = document.getElementById('statusDivCustom');
            el.textContent = msg;
            el.style.background = type === 'error' ? 'rgba(255,75,75,0.9)' : (type === 'success' ? 'rgba(40,200,120,0.9)' : 'rgba(0,0,0,0.6)');
        },

        async startCamera() {
            try {
                this.videoStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
                const video = document.getElementById('modalVideo');
                video.srcObject = this.videoStream;
                await video.play();

                // set canvas size to video
                const canvas = document.getElementById('overlayCanvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                canvas.style.width = video.offsetWidth + 'px';
                canvas.style.height = video.offsetHeight + 'px';

                this.updateStatus('Camera ready. Select employee and click Start Validation.');
            } catch (err) {
                console.error(err);
                this.updateStatus('Camera access denied', 'error');
            }
        },

        stopCamera() {
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(t => t.stop());
                this.videoStream = null;
            }
            this.detectLoopRunning = false;
            this.validationComplete = false;
            this.blinkCount = 0;
            this.blinkedRecently = false;
            const canvas = document.getElementById('overlayCanvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0,0,canvas.width,canvas.height);
            }
        },

        async fetchDescriptor(employeeId) {
            const res = await fetch(`/attendance/descriptor/${employeeId}`);
            const json = await res.json();
            if (!json.success) throw new Error(json.message || 'No descriptor');
            return json.descriptor;
        },

        /* EAR helpers */
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

        resetValidationState() {
            this.storedDescriptor = null;
            this.detectLoopRunning = false;
            this.blinkCount = 0;
            this.blinkedRecently = false;
            this.lastBlinkTime = 0;
            this.validationComplete = false;
            this.processing = false;
            this.buttonText = 'Start Validation';
        },

        async startValidationFlow() {
    if (!this.selectedEmployeeId) {
        this.updateStatus('Please select an employee first', 'error');
        return;
    }

    this.processing = true;
    this.buttonText = 'Preparing...';

    try {
        if (!faceapi.nets.tinyFaceDetector.params) {
            this.updateStatus('Loading face models...', 'info');
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models')
            ]);
        }

        // fetch descriptor
        const descriptorArray = await this.fetchDescriptor(this.selectedEmployeeId);
        if (!descriptorArray) {
            throw new Error('Not registered');
        }

        this.storedDescriptor = new Float32Array(descriptorArray);
        this.updateStatus('Descriptor loaded. Look at the camera.', 'info');
        this.buttonText = 'Validation starting in 3...';

        // countdown before first blink
        await this.blinkCountdown(2); // repeat twice for 2 blinks

        this.blinkCount = 0;
        this.detectLoopRunning = true;
        this.runDetectLoop();

    } catch (err) {
        console.error(err);
        const msg = err.message.includes('owner') ? 'Invalid owner' : err.message || 'Failed to start validation';
        this.updateStatus(msg, 'error');
        this.processing = false;
        this.buttonText = 'Start Validation';
    }
},

async blinkCountdown(times) {
    for (let i = 0; i < times; i++) {
        for (let j = 1; j <= 3; j++) {
            this.updateStatus(`Blink ${i+1}: ${j}...`, 'info');
            await new Promise(r => setTimeout(r, 800)); // 0.8s per count
        }
        this.updateStatus(`Blink ${i+1}: Blink!`, 'info');
        await new Promise(r => setTimeout(r, 800)); // wait before next blink
    }
},


        async runDetectLoop() {
            const video = document.getElementById('modalVideo');
            const canvas = document.getElementById('overlayCanvas');
            const ctx = canvas.getContext('2d');

            if (!this.detectLoopRunning) return;

            try {
                const detections = await faceapi
                    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (!detections || detections.length === 0) {
                    this.updateStatus('No face detected', 'error');
                    if (this.detectLoopRunning) requestAnimationFrame(() => this.runDetectLoop());
                    return;
                }

                const displaySize = { width: canvas.width, height: canvas.height };
                const resized = faceapi.resizeResults(detections, displaySize);
                faceapi.draw.drawDetections(canvas, resized);
                faceapi.draw.drawFaceLandmarks(canvas, resized);

                const detection = detections[0];
                const distance = faceapi.euclideanDistance(detection.descriptor, this.storedDescriptor);

                if (distance > this.MAX_DISTANCE) {
                    this.updateStatus('Face not recognized (match failed).', 'error');
                    this.blinkCount = 0; // reset
                    if (this.detectLoopRunning) requestAnimationFrame(() => this.runDetectLoop());
                    return;
                }

                // face matched - compute EAR & blink
                const leftEye = detection.landmarks.getLeftEye().map(p => ({ x: p.x, y: p.y }));
                const rightEye = detection.landmarks.getRightEye().map(p => ({ x: p.x, y: p.y }));
                const leftEAR = this.getEAR(leftEye);
                const rightEAR = this.getEAR(rightEye);
                const ear = (leftEAR + rightEAR) / 2;

                // overlay debug text
                ctx.fillStyle = 'white';
                ctx.font = '16px Arial';
                ctx.fillText(`EAR: ${ear.toFixed(3)}`, 10, 20);
                ctx.fillText(`Blinks: ${this.blinkCount}/2`, 10, 40);
                ctx.fillText(`Match score: ${(1 - distance).toFixed(3)}`, 10, 60);

                const now = Date.now();
                if (ear < this.EAR_THRESHOLD && !this.blinkedRecently && now - this.lastBlinkTime > this.BLINK_COOLDOWN) {
                    this.blinkCount++;
                    this.blinkedRecently = true;
                    this.lastBlinkTime = now;
                    this.updateStatus(`Blink detected! (${this.blinkCount}/2)`, 'info');

                    if (this.blinkCount >= 2) {
                        this.validationComplete = true;
                        this.detectLoopRunning = false;
                        this.updateStatus('Face validated successfully!', 'success');
                        // record attendance
                        await this.recordAttendanceAJAX(this.selectedEmployeeId, this.actionType);
                        // close modal after small delay
                        setTimeout(() => {
                            this.stopCamera();
                            this.showModal = false;
                            this.resetValidationState();
                        }, 700);
                        return;
                    }
                } else if (ear >= this.EAR_THRESHOLD) {
                    this.blinkedRecently = false;
                }

                // reset sequence if too slow
                if (this.blinkCount > 0 && now - this.lastBlinkTime > 6000) {
                    this.blinkCount = 0;
                    this.updateStatus('Blink sequence reset. Blink twice.', 'info');
                }

                if (this.detectLoopRunning) requestAnimationFrame(() => this.runDetectLoop());
            } catch (err) {
                console.error(err);
                this.updateStatus('Detection error', 'error');
                if (this.detectLoopRunning) requestAnimationFrame(() => this.runDetectLoop());
            } finally {
                this.processing = false;
            }
        },

        async recordAttendanceAJAX(employeeId, action) {
            this.updateStatus('Recording attendance...', 'info');
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

                this.updateStatus(json.message || 'Recorded', 'success');
                Swal.fire({
                    icon: 'success',
                    title: action === 'time_in' ? 'Time In Recorded' : 'Time Out Recorded',
                    text: json.message || 'Attendance logged successfully.',
                    confirmButtonColor: '#2563eb'
                }).then(() => {
                    // reload to reflect updated table
                    window.location.reload();
                });
            } catch (err) {
                console.error(err);
                this.updateStatus('Server error', 'error');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Server error while recording attendance.' });
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
