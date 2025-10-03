<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Page</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 text-black">

    <div x-data="attendanceModal()" x-cloak
         class="max-w-7xl mx-auto bg-white p-10 mt-10 rounded-lg shadow-lg border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Attendance Table -->
        <div class="col-span-2">
            <h1 class="text-2xl font-bold mb-4 border-b pb-2 flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-6 h-6"></i>
                My Attendance Records
            </h1>
            <table class="w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Time In</th>
                        <th class="px-4 py-2 border">Time Out</th>
                        <th class="px-4 py-2 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
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
                            <td colspan="4" class="text-center py-4">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Time In / Out Buttons -->
        <div class="flex flex-col justify-center items-center space-y-6">
            <button @click="openScanModal('time_in')"
                class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold shadow-lg transition flex items-center gap-2">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                Time In
            </button>
            <button @click="openScanModal('time_out')"
                class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow-lg transition flex items-center gap-2">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Time Out
            </button>
        </div>

        <!-- RFID Scan Modal -->
        <template x-if="showScanModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-xl">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <i data-lucide="scan-line" class="w-6 h-6"></i>
                        Scan RFID Card
                    </h2>

                    <label class="block text-left font-semibold">RFID Card Number</label>
                    <input type="text" id="card_Idnumber" x-model="cardNumber"
                           class="w-full px-4 py-2 border rounded-lg bg-gray-100" required
                           placeholder="Scan your card here">

                    <div id="employee-info" x-show="employeeFound" class="mt-4 text-left text-sm">
                        <p><strong>Name:</strong> <span x-text="employee.first_name + ' ' + employee.last_name"></span></p>
                        <p><strong>Position:</strong> <span x-text="employee.position"></span></p>
                        <p><strong>Email:</strong> <span x-text="employee.email"></span></p>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" @click="closeScanModal()"
                            class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            Cancel
                        </button>
                        <button type="button" @click="fetchEmployee()"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- OTP Modal -->
        <template x-if="showOtpModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
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

            if (action === 'time_in') {
                if (currentTime < 600 || currentTime > 800) {
                    alert("⏰ Time In is only allowed between 6:00 AM and 8:00 AM.");
                    return;
                }
            }

            if (action === 'time_out') {
                if (currentTime < 1700 || currentTime > 1900) {
                    alert("⏰ Time Out is only allowed between 5:00 PM and 7:00 PM.");
                    return;
                }
            }

            this.actionType = action;
            this.showScanModal = true;
            this.$nextTick(() => {
                document.getElementById("card_Idnumber").focus();
            });
        },

        closeScanModal() {
            this.showScanModal = false;
            this.cardNumber = '';
            this.employeeFound = false;
        },
        closeOtpModal() {
            this.showOtpModal = false;
        },

        fetchEmployee() {
            if (!this.cardNumber) {
                alert("Please scan your RFID card.");
                return;
            }

            fetch(`/api/get-employee/${this.cardNumber}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.employee = data;
                        this.employeeFound = true;
                        this.showScanModal = false;
                        this.showOtpModal = true;
                    } else {
                        alert(data.message);
                        this.employeeFound = false;
                    }
                })
                .catch(err => console.error("Error fetching employee:", err));
        }
    }
}

document.addEventListener("alpine:init", () => {
    lucide.createIcons();
});
</script>

</body>
</html>
