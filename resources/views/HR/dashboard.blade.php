<x-guest-layout>
    <!-- Main Content -->
    <div class="main-content p-4 sm:p-8 min-h-screen text-black" x-data="{ openModal: false }">

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">

            <!-- Number of Employees -->
            <a href="{{ route('show.employeeprofiles') }}"
                class="bg-gradient-to-b from-orange-200 via-white to-gray-300 rounded-2xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 p-4 sm:p-6 flex justify-between items-center h-32 sm:h-36">
                <div>
                    <div class="text-3xl sm:text-4xl font-bold text-gray-900">
                        {{ $employeeCount }}
                    </div>
                    <div class="text-sm sm:text-base font-medium text-gray-700 mt-1">
                        Number of Employees
                    </div>
                </div>
                <div class="bg-black text-white p-3 sm:p-4 rounded-lg shadow">
                    <i class="fas fa-users text-lg sm:text-xl text-orange-400"></i>
                </div>
            </a>

            <!-- Attendance -->
            <div @click="openModal = true"
                class="bg-gradient-to-b from-orange-200 via-white to-gray-300 rounded-2xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 p-4 sm:p-6 flex justify-between items-center h-32 sm:h-36 cursor-pointer">
                <div>
                    <div class="text-3xl sm:text-4xl font-bold text-gray-900">
                        {{ $attendanceCount }}
                    </div>
                    <div class="text-sm sm:text-base font-medium text-gray-700 mt-1">
                        Attendance (In & Out)<br class="hidden sm:block">{{ $formattedDate }}
                    </div>
                    <div class="text-xs text-gray-700 italic mt-2">
                        Click to view details
                    </div>
                </div>
                <div class="bg-black text-white p-3 sm:p-4 rounded-lg shadow">
                    <i class="fas fa-calendar-check text-lg sm:text-xl text-green-500"></i>
                </div>
            </div>

            <!-- Number of Units -->
            <div
                class="bg-gradient-to-b from-orange-200 via-white to-gray-300 rounded-2xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 p-4 sm:p-6 flex justify-between items-center h-32 sm:h-36">
                <div class="flex flex-col">
                    <div class="text-3xl sm:text-4xl font-bold text-gray-900 flex items-center gap-2 sm:gap-3">
                        <!-- Insert unit count here -->
                    </div>
                    <div class="text-sm sm:text-base font-medium text-gray-700 mt-1">
                        Number of AC Units
                    </div>
                </div>
                <div class="bg-black text-white p-3 sm:p-4 rounded-lg shadow">
                    <i class="fas fa-cubes text-gray-300 text-lg sm:text-xl"></i>
                </div>
            </div>

        </div>

        <!-- Attendance Modal -->
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 sm:p-6"
            x-show="openModal" x-cloak>
            <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-4 sm:p-6 relative animate-fadeIn">

                <!-- Close Button -->
                <button @click="openModal = false"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">
                    &times;
                </button>

                <h2 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 border-b border-gray-200 pb-2">
                    Attendance Details - {{ $formattedDate }}
                </h2>

                <!-- Attendance Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-xs sm:text-sm">
                        <thead class="bg-gray-100 text-gray-800">
                            <tr>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-left font-semibold">Employee Name</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-left font-semibold">Time In</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-left font-semibold">Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 border-t text-gray-900">
                                        {{ $attendance->employeeprofiles->first_name }}
                                        {{ $attendance->employeeprofiles->last_name }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 border-t text-gray-900">
                                        {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '—' }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 border-t text-gray-900">
                                        {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Pending' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10 mb-20">

            <!-- Activity Chart -->
            <div
                class="bg-gradient-to-r from-orange-300 to-gray-300 rounded-xl shadow-md p-4 sm:p-6 border border-gray-200 min-h-[20rem] sm:h-[28rem] flex flex-col">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">
                        Performance Overview
                    </h3>
                    <select
                        class="border border-gray-300 rounded-lg px-2 sm:px-3 py-1 text-sm text-gray-800 focus:ring-orange-400 focus:border-orange-400 w-full sm:w-auto">
                        <option>Employee Performance</option>
                        <option>Company Performance</option>
                    </select>
                </div>
                <div
                    class="flex-1 flex items-center justify-center text-gray-500 text-xs sm:text-sm border border-dashed border-gray-300 rounded-lg">
                    [📊 Activity Chart Will Appear Here]
                </div>
            </div>

            <!-- Recent Actions -->
            <div
                class="bg-gradient-to-r from-orange-300 to-gray-300 rounded-xl shadow-md p-4 sm:p-6 border border-gray-200 min-h-[20rem] sm:h-[28rem] flex flex-col">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Recent Actions</h3>
                    <button
                        class="button bg-gradient-to-r from-orange-400 via-orange-300 to-orange-200 hover:from-orange-300 hover:to-orange-100 text-black px-3 sm:px-4 py-2 rounded-lg shadow transition text-sm">
                        View All
                    </button>
                </div>
                <div class="flex flex-col gap-4 overflow-y-auto pr-1 mt-6 sm:mt-10">
                    <div
                        class="flex items-center gap-3 bg-gradient-to-b from-orange-200 via-white to-gray-300 p-3 rounded-lg hover:bg-gray-100 transition">
                        <div class="bg-orange-500 text-white p-2 sm:p-3 rounded-full shadow">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">New Employee Added</div>
                            <div class="text-gray-500 text-xs">2 mins ago</div>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 bg-gradient-to-b from-orange-200 via-white to-gray-300 p-3 rounded-lg hover:bg-gray-100 transition">
                        <div class="bg-orange-500 text-white p-2 sm:p-3 rounded-full shadow">
                            <i class="fas fa-file-upload"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Document uploaded</div>
                            <div class="text-gray-500 text-xs">15 mins ago</div>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 bg-gradient-to-b from-orange-200 via-white to-gray-300 p-3 rounded-lg hover:bg-gray-100 transition">
                        <div class="bg-orange-500 text-white p-2 sm:p-3 rounded-full shadow">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">System settings updated</div>
                            <div class="text-gray-500 text-xs">30 mins ago</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
