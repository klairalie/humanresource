<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-white p-10 rounded-xl shadow-xl border border-gray-200 mb-20">
        <h1 class="text-3xl font-bold text-black mb-8 border-b pb-4">Edit Employee Profile</h1>

        @if ($errors->any())
            <script>
                @foreach ($errors->all() as $error)
                    alert("{{ $error }}");
                @endforeach
            </script>
        @endif

        <form action="{{ route('update.profile', $employee->employeeprofiles_id) }}" method="POST"
              class="grid grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <!-- First Name -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Last Name -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address', $employee->address) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Position -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Position</label>
                <select name="position" id="position"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
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

            <!-- Basic Salary (auto-fetched via relationship) -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Basic Salary</label>
                <input type="text" id="basic_salary"
                       value="{{ $employee->salary?->basic_salary ?? '' }}"
                       readonly
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 focus:ring-amber-500 text-black">
            </div>

            <!-- Live update on position change -->
            <script>
                document.getElementById('position').addEventListener('change', function() {
                    const salary = this.options[this.selectedIndex].dataset.salary;
                    document.getElementById('basic_salary').value = salary || '';
                });
            </script>

            <!-- Contact Number -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $employee->contact_number) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Status</label>
                <input type="text" name="status" value="{{ old('status', $employee->status) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Emergency Contact -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Emergency Contact</label>
                <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Hire Date -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Hire Date</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Card ID Number -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Card ID Number</label>
                <input type="text" name="card_Idnumber" id="card_Idnumber"
                       value="{{ old('card_Idnumber', $employee->card_Idnumber) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Confirm Card ID -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Confirm Card ID Number</label>
                <input type="text" id="confirm_card_Idnumber"
                       value="{{ old('card_Idnumber', $employee->card_Idnumber) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6 col-span-2">
                <a href="{{ route('show.employeeprofiles') }}"
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
        document.addEventListener('DOMContentLoaded', function () {
            const cardInput = document.getElementById('card_Idnumber');
            const confirmCardInput = document.getElementById('confirm_card_Idnumber');

            confirmCardInput.addEventListener('input', function () {
                if (cardInput.value !== confirmCardInput.value) {
                    confirmCardInput.setCustomValidity("Card ID Numbers do not match!");
                } else {
                    confirmCardInput.setCustomValidity("");
                }
            });
        });
    </script>
</x-guest-layout>
