<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-gray-500 p-8 rounded-lg shadow-lg border border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 border-b pb-4">Edit Employee Profile</h1>

        <form action="{{ route('update.profile', $employee->employeeprofile_id) }}" method="POST" class="grid grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <!-- First Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

            <!-- Last Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address', $employee->address) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

            <!-- Position -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                <input type="text" name="position" value="{{ old('position', $employee->position) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

            <!-- Contact Info -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Info</label>
                <input type="text" name="contact_info" value="{{ old('contact_info', $employee->contact_info) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <input type="text" name="status" value="{{ old('status', $employee->status) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

            <!-- Emergency Contact -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact</label>
                <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>

              <!-- Hire Date -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Hire Date</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}"
                    class="w-s border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400">
            </div>
            <!-- Empty div to align buttons to the right column -->
            <div></div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ url()->previous() }}"
                   class="px-5 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                   Cancel
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-amber-500 text-white font-semibold rounded-lg hover:bg-amber-600 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
