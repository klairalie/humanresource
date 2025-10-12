<x-guest-layout>
    <div class="max-w-6xl mx-auto p-10 bg-white rounded-2xl shadow-2xl mb-20">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3">Add Employee Profile</h2>

        <form action="{{ route('submit.employeeprofiles') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-black">
                <!-- LEFT COLUMN -->
                <div class="space-y-5">
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-semibold text-gray-700">Position</label>
                        <select name="position" id="position" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">-- Select Position --</option>
                            @foreach($salaries as $salary)
                                <option value="{{ $salary->position }}" {{ old('position') == $salary->position ? 'selected' : '' }}>
                                    {{ $salary->position }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-semibold text-gray-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    <div>
                        <label for="contact_number" class="block text-sm font-semibold text-gray-700">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="hire_date" class="block text-sm font-semibold text-gray-700">Hire Date</label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700">Status</label>
                        <input type="text" name="status" id="status" value="{{ old('status') }}" required
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="emergency_contact" class="block text-sm font-semibold text-gray-700">Emergency Contact</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label for="card_Idnumber" class="block text-sm font-semibold text-gray-700">RFID Card ID Number</label>
                        <input type="text" name="card_Idnumber" id="card_Idnumber" value="{{ old('card_Idnumber') }}"
                            class="mt-1 w-full border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-purple-500 focus:outline-none">
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
