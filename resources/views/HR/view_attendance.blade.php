<x-guest-layout>
    <div class="max-w-7xl mx-auto bg-white p-10 mt-10 rounded-lg shadow-lg border border-gray-200 text-black">
        <h1 class="text-3xl font-bold mb-8 border-b pb-4">Daily Attendance Record</h1>

        <!-- Navigation Buttons -->
        <div class="flex space-x-4 mb-8">
            <a href="{{ route('show.overtime') }}"
               class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-center py-4 rounded-lg font-semibold text-lg shadow transition">
               Employees Overtime
            </a>
            <a href="{{ route('show.leaverequest') }}"
               class="flex-1 bg-gray-700 hover:bg-gray-800 text-white text-center py-4 rounded-lg font-semibold text-lg shadow transition">
               Manage Leave Requests
            </a>
        </div>

        <!-- 🔍 Search & Filter -->
        <form method="GET" action="{{ route('show.attendance') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search by Name -->
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by employee name"
                   class="px-4 py-2 border rounded-lg w-full text-black">

            <!-- Filter by Position -->
            <select name="position" class="px-4 py-2 border rounded-lg w-full text-black">
                <option value="">All Positions</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                        {{ $pos }}
                    </option>
                @endforeach
            </select>

            <!-- Filter by Date -->
            <input type="date" name="date" value="{{ request('date') }}"
                   class="px-4 py-2 border rounded-lg w-full text-black">

            <!-- Submit Button -->
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg px-4 py-2 shadow">
                Apply Filters
            </button>
        </form>

        <!-- Attendance Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 border text-left">Employee Name</th>
                        <th class="px-4 py-3 border text-left">Position</th>
                        <th class="px-4 py-3 border text-left">Date</th>
                        <th class="px-4 py-3 border text-left">Time In</th>
                        <th class="px-4 py-3 border text-left">Time Out</th>
                        <th class="px-4 py-3 border text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 border font-semibold">
                                {{ $attendance->employeeprofiles->first_name }} {{ $attendance->employeeprofiles->last_name }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $attendance->employeeprofiles->position }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}
                            </td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold
                                    @if($attendance->status === 'Present') bg-green-100 text-green-700
                                    @elseif($attendance->status === 'Out') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->

    </div>
    <div class="mt-10 mb-20 mr-3">
        {{ $attendances->links() }}
    </div>

</x-guest-layout>
