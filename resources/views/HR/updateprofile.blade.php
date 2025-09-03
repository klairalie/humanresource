<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-white p-10 rounded-xl shadow-xl border border-gray-200 mb-20">
        <h1 class="text-3xl font-bold text-black mb-8 border-b pb-4">Edit Employee Profile</h1>

        <form action="{{ route('update.profile', $employee->employeeprofiles_id) }}" method="POST"
            class="grid grid-cols-2 gap-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- First Name -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Last Name -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address', $employee->address) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Position -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Position</label>
                <select name="position" id="position"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
                    <option value="">-- Select Position --</option>
                    @foreach ($salaries as $salary)
                        <option value="{{ $salary->position }}"
                            {{ $employee->position == $salary->position ? 'selected' : '' }}
                            data-salary="{{ $salary->basic_salary }}">
                            {{ $salary->position }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Basic Salary -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Basic Salary</label>
                <input type="text" id="basic_salary"
                    value="{{ optional($salaries->firstWhere('position', $employee->position))->basic_salary }}"
                    readonly
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <script>
                document.getElementById('position').addEventListener('change', function() {
                    const salary = this.options[this.selectedIndex].dataset.salary;
                    document.getElementById('basic_salary').value = salary || '';
                });
            </script>

            <!-- Contact Info -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Contact Info</label>
                <input type="text" name="contact_info" value="{{ old('contact_info', $employee->contact_info) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Status</label>
                <input type="text" name="status" value="{{ old('status', $employee->status) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Emergency Contact -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Emergency Contact</label>
                <input type="text" name="emergency_contact"
                    value="{{ old('emergency_contact', $employee->emergency_contact) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Hire Date -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Hire Date</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Fingerprint -->
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-black mb-2">Fingerprint</label>
                @if ($employee->fingerprint_data)
                    <img src="data:image/png;base64,{{ $employee->fingerprint_data }}" alt="Fingerprint"
                        class="w-40 h-40 border border-gray-300 rounded-lg shadow-md">
                @else
                    <p class="text-gray-500">No fingerprint registered</p>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6 col-span-2">
                <a href="{{ route('show.employeeprofiles')}}"
                    class="px-6 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition font-semibold">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-amber-500 text-black font-semibold rounded-lg hover:bg-amber-600 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const positionSelect = document.getElementById('position');
            const salaryInput = document.getElementById('basic_salary');

            positionSelect.addEventListener('change', function() {
                const selectedSalary = this.options[this.selectedIndex].getAttribute('data-salary');
                salaryInput.value = selectedSalary ?? '';
            });
        });
    </script>
</x-guest-layout>
