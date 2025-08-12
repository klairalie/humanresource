<x-guest-layout>
    <div class="max-w-6xl mx-auto bg-white p-20 rounded-lg shadow-lg border border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 border-b pb-4">Daily Attendance Record</h1>

        
        <div class="flex space-x-4 mb-8">
            <a href="{{ route('show.overtime') }}"
               class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-center py-4 rounded-lg font-semibold text-lg shadow transition">
               Employees Overtime
            </a>
            <a href="{{ route('show.leaverequest') }}"
               class="flex-1 bg-gray-700 hover:bg-gray-800 text-white text-center py-4 rounded-lg font-semibold text-lg shadow transition">
               Manage Leave Requests
            </a>

            <a href="{{ route('show.attendanceform') }}"
               class="flex-1 bg-gray-700 hover:bg-gray-800 text-white text-center py-4 rounded-lg font-semibold text-lg shadow transition">
               Create Attendance Form
            </a>
        </div>

        
        <div class="bg-gray-100 p-10 rounded-lg text-center border border-gray-300 mb-30">
            <p class="text-lg text-gray-600">
                Attendance viewing is currently unavailable.
                <br>
                <span class="text-sm text-gray-500">Please connect the biometric fingerprint scanner to enable this feature.</span>
            </p>
        </div>
    </div>
</x-guest-layout>
