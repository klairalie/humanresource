<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-6 text-black" style="background: transparent;">
        <div class="w-full max-w-md bg-white/90 backdrop-blur-md rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Attendance Form</h1>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('submit.attendanceform') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Employee Name Selection --}}
                {{-- Employee Name Selection --}}
<div>
    <label for="employeeprofiles_id" class="block text-sm font-medium text-gray-700 mb-1">
        Employee
    </label>
    <select id="employeeprofiles_id" name="employeeprofiles_id"
        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
        <option value="" disabled {{ old('employeeprofiles_id') ? '' : 'selected' }}>-- Select Employee --</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->employeeprofiles_id }}"
                {{ old('employeeprofiles_id') == $employee->employeeprofiles_id ? 'selected' : '' }}>
                {{ $employee->first_name }} {{ $employee->last_name }} ID: {{ $employee->employeeprofiles_id}}
            </option>
        @endforeach
    </select>
</div>

{{-- Date --}}
<div>
    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
        Date
    </label>
    <input type="date" id="date" name="date"
        value="{{ old('date') }}"
        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
</div>

{{-- Time In --}}
<div>
    <label for="time_in" class="block text-sm font-medium text-gray-700 mb-1">
        Time In
    </label>
    <input type="time" id="time_in" name="time_in"
        value="{{ old('time_in') }}"
        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
</div>

{{-- Time Out --}}
<div>
    <label for="time_out" class="block text-sm font-medium text-gray-700 mb-1">
        Time Out
    </label>
    <input type="time" id="time_out" name="time_out"
        value="{{ old('time_out') }}"
        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
</div>

{{-- Flag --}}
<div>
    <label for="flag" class="block text-sm font-medium text-gray-700 mb-1">
        Flag
    </label>
    <input type="number" id="flag" name="flag"
        value="{{ old('flag') }}"
        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
</div>

{{-- Status --}}
<div>
    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
        Status
    </label>
    <select id="status" name="status"
        class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
        <option value="" {{ old('status') ? '' : 'selected' }}>-- Select Status --</option>
        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
        <option value="Present" {{ old('status') == 'Present' ? 'selected' : '' }}>Present</option>
        <option value="Absent" {{ old('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
    </select>
</div>


                {{-- Submit --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-900 transition font-medium">
                        Submit Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
