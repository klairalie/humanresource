<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Page</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <button @click="openScanModal('time_in')"
                class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                Time In
            </button>
            <button @click="openScanModal('time_out')"
                class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Time Out
            </button>
        </div>

        <!-- RFID Scan Modal -->
        <template x-if="showScanModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-xl">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="scan-line" class="w-6 h-6"></i>
                        Scan RFID Card
                    </h2>

                    <label class="block text-left font-semibold">RFID Card Number</label>
                    <input type="text" id="card_Idnumber" x-model="cardNumber"
                           class="w-full px-4 py-2 border rounded-lg bg-gray-100"
                           placeholder="Scan your card here"
                           @input.debounce.500ms="autoFetchEmployee" autofocus>

                    <div id="employee-info" x-show="employeeFound" class="mt-4 text-left text-sm">
                        <p><strong>Name:</strong> <span x-text="employee.first_name + ' ' + employee.last_name"></span></p>
                        <p><strong>Position:</strong> <span x-text="employee.position"></span></p>
                        <p><strong>Email:</strong> <span x-text="employee.email"></span></p>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" @click="closeScanModal()"
                            class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- OTP Modal -->
        <template x-if="showOtpModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-xl">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="key-round" class="w-6 h-6"></i>
                        OTP Verification
                    </h2>
                    <p class="mb-4">
                        An OTP has been sent to <span class="font-semibold" x-text="employee.email"></span>. Please enter it below:
                    </p>

                    <form method="POST" action="{{ route('attendance.verifyOtp') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="employee_id" :value="employee.employee_id">
                        <input type="hidden" name="action_type" :value="actionType">

                        <label class="block text-left font-semibold">Enter OTP</label>
                        <input type="text" name="otp" placeholder="6-digit code"
                               class="w-full px-4 py-2 border rounded-lg" required>

                        <div class="flex justify-between mt-6">
                            <button type="button" @click="closeOtpModal()"
                                class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex items-center gap-1">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold flex items-center gap-1">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                                Verify
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

<script>
function attendanceModal() {
    return {
        showScanModal: false,
        showOtpModal: false,
        actionType: '',
        cardNumber: '',
        employee: {},
        employeeFound: false,

        openScanModal(action) {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const currentTime = hours * 100 + minutes;

            if (action === 'time_in' && (currentTime < 600 || currentTime > 1200)) {
                alert("⏰ Time In is only allowed between 6:00 AM and 12:00 AM.");
                return;
            }

            if (action === 'time_out' && (currentTime < 1700 || currentTime > 1900)) {
                alert("⏰ Time Out is only allowed between 5:00 PM and 7:00 PM.");
                return;
            }

            this.actionType = action;
            this.showScanModal = true;
            this.$nextTick(() => document.getElementById("card_Idnumber").focus());
        },

        closeScanModal() {
            this.showScanModal = false;
            this.cardNumber = '';
            this.employeeFound = false;
        },
        closeOtpModal() {
            this.showOtpModal = false;
        },

        fetchEmployee() {}, // disabled old Confirm button logic

        autoFetchEmployee() {
            if (this.cardNumber.trim().length < 5) return; // wait until full card number is read

            fetch(`/api/get-employee/${this.cardNumber}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.employee = data;
                        this.employeeFound = true;

                        // SweetAlert confirmation and OTP transition
                        Swal.fire({
                            title: '<strong>Employee Detected</strong>',
                            html: `
                                <div class="text-left text-sm mt-3 leading-relaxed">
                                    <p><b>First Name:</b> ${this.employee.first_name}</p>
                                    <p><b>Last Name:</b> ${this.employee.last_name}</p>
                                    <p><b>Position:</b> ${this.employee.position}</p>
                                    <p><b>Email:</b> ${this.employee.email}</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Send OTP',
                            confirmButtonColor: '#2563eb',
                            customClass: {
                                popup: 'rounded-2xl p-6'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.showScanModal = false;
                                this.showOtpModal = true;
                            }
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Card Not Found or Card Done Scanning',
                            text: data.message || 'No employee linked with this RFID card.',
                        });
                        this.employeeFound = false;
                    }
                })
                .catch(err => {
                    console.error("Error fetching employee:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Could not fetch employee details. Please try again later.',
                    });
                });
        }
    }
}

document.addEventListener("alpine:init", () => {
    lucide.createIcons();
});
</script>

</body>
</html>
