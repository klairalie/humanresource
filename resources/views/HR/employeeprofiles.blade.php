<x-guest-layout>
    <div class="max-w-6xl mx-auto p-10 bg-white rounded-2xl shadow-2xl mb-20">
       <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">Add Employee Profile</h2>

        <form action="{{ route('submit.employeeprofiles')}}" method="POST" enctype="multipart/form-data" class="space-y-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-black">
                <!-- LEFT COLUMN -->
                <div class="space-y-5">
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <!-- Position Dropdown -->
                    <div>
                        <label for="position" class="block text-sm font-semibold text-gray-700">Position</label>
                        <select name="position" id="position" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">-- Select Position --</option>
                            @foreach($salaries as $salary)
                                <option value="{{ $salary->position }}" data-salary="{{ $salary->basic_salary }}">
                                    {{ $salary->position }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Basic Salary Auto-fill -->
                    <div>
                        <label for="basic_salary" class="block text-sm font-semibold text-gray-700">Basic Salary</label>
                        <input type="text" name="basic_salary" id="basic_salary" readonly
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-600 shadow-sm" />
                    </div>

                    <div>
                        <label for="contact_info" class="block text-sm font-semibold text-gray-700">Contact Info</label>
                        <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="hire_date" class="block text-sm font-semibold text-gray-700">Hire Date</label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                        <input type="text" name="status" id="status" value="{{ old('status') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="emergency_contact" class="block text-sm font-semibold text-gray-700">Emergency Contact</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none" />
                    </div>

                    <!-- Fingerprint Scans -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Fingerprint Scans (3 Attempts)</label>
                        <div class="flex gap-5 mt-3">
                            <canvas id="fingerprintPreview1" width="130" height="130" class="border-2 border-dashed border-purple-400 rounded-xl shadow-md bg-purple-50"></canvas>
                            <canvas id="fingerprintPreview2" width="130" height="130" class="border-2 border-dashed border-purple-400 rounded-xl shadow-md bg-purple-50"></canvas>
                            <canvas id="fingerprintPreview3" width="130" height="130" class="border-2 border-dashed border-purple-400 rounded-xl shadow-md bg-purple-50"></canvas>
                        </div>

                        <!-- Hidden inputs for scans -->
                        <input type="hidden" name="fingerprintScan1" id="fingerprintScan1">
                        <input type="hidden" name="fingerprintScan2" id="fingerprintScan2">
                        <input type="hidden" name="fingerprintScan3" id="fingerprintScan3">

                        <p id="fingerprintStatus" class="text-sm text-gray-500 mt-3">Please scan fingerprint (1 of 3)</p>

                        <button type="button" id="scanFingerprint"
                            class="mt-4 bg-purple-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-purple-700 transition">
                            Scan Fingerprint
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit + Cancel Buttons -->
<div class="flex justify-center gap-4">
    <button type="submit"
        class="bg-amber-600 text-white px-8 py-3 rounded-lg text-lg font-medium shadow-md hover:bg-amber-700 transition">
        Submit Employee
    </button>

    <a href="{{ route('show.employeeprofiles') }}"
        class="bg-gray-500 text-white px-8 py-3 rounded-lg text-lg font-medium shadow-md hover:bg-gray-600 transition">
        Cancel
    </a>
</div>


            @if($errors->any())
                <div class="mt-6">
                    <ul class="bg-red-100 text-red-700 p-4 rounded-lg border border-red-300 space-y-2">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</x-guest-layout>

<script>
    // Autofill salary based on position
    document.getElementById('position').addEventListener('change', function () {
        let salary = this.options[this.selectedIndex].getAttribute('data-salary');
        document.getElementById('basic_salary').value = salary ? salary : '';
    });

    // Fingerprint scan simulation
    document.addEventListener('DOMContentLoaded', function () {
        let scanCount = 0;
        document.getElementById('scanFingerprint').addEventListener('click', function () {
            if (scanCount < 3) {
                const canvas = document.getElementById(`fingerprintPreview${scanCount + 1}`);
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#D1D5DB';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#4F46E5';
                ctx.beginPath();
                ctx.arc(canvas.width / 2, canvas.height / 2, 50, 0, Math.PI * 2);
                ctx.fill();
                document.getElementById(`fingerprintScan${scanCount + 1}`).value = canvas.toDataURL();
                scanCount++;
                document.getElementById('fingerprintStatus').innerText = scanCount < 3
                    ? `Please scan fingerprint (${scanCount + 1} of 3)`
                    : 'All fingerprint scans completed.';
            } else {
                alert('All fingerprint scans completed.');
            }
        });
    });
</script>
