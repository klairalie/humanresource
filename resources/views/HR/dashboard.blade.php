<x-guest-layout>
    <!-- Main Content -->
    <div class="main-content p-6 bg-transparent min-h-screen text-black" x-data="{ openModal: false }">

        <!-- Stats Cards -->
        <div class="stats-container grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">

            <!-- Number of Employees -->
            <a href="{{ route('show.employeeprofiles') }}"
                class="stat-card bg-gradient-to-b from-orange-100 via-white to-gray-200 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition transform duration-300 block p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="stat-value text-3xl font-extrabold text-black leading-tight">
                            {{ $employeeCount }}
                        </div>
                        <div class="stat-label text-sm font-medium text-gray-800 mt-1">
                            Number of Employees
                        </div>
                    </div>
                    <div class="stat-icon bg-orange-400 text-black p-4 rounded-xl shadow-md">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                </div>
            </a>

            <!-- Attendance (Card as Button) -->
            <div
                class="stat-card bg-gradient-to-b from-orange-100 via-white to-gray-200 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition transform duration-300 cursor-pointer p-6"
                @click="openModal = true">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="stat-value text-3xl font-extrabold text-black leading-tight">
                            {{ $attendanceCount }}
                        </div>
                        <div class="stat-label text-sm font-medium text-gray-800 mt-1">
                            Attendance (In & Out)<br>{{ $formattedDate }}
                        </div>
                        <div class="text-xs text-gray-600 italic mt-2">
                            Click to view details
                        </div>
                    </div>
                    <div class="stat-icon bg-green-400 text-black p-4 rounded-xl shadow-md">
                        <i class="fas fa-calendar-check text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Number of Units -->
            <div
                class="stat-card bg-gradient-to-b from-orange-100 via-white to-gray-200 rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition transform duration-300 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="stat-value text-3xl font-extrabold text-black leading-tight">56</div>
                        <div class="stat-label text-sm font-medium text-gray-800 mt-1">Number of Units</div>
                    </div>
                    <div class="stat-icon bg-orange-400 text-black p-4 rounded-xl shadow-md">
                        <i class="fas fa-heartbeat text-lg"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- Attendance Modal -->
        <div
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            x-show="openModal"
            x-cloak>
            <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-6 relative animate-fadeIn">

                <!-- Close Button -->
                <button @click="openModal = false"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold">
                    &times;
                </button>

                <h2 class="text-xl font-bold mb-4 text-black border-b border-gray-300 pb-2">
                    Attendance Details - {{ $formattedDate }}
                </h2>

                <!-- Attendance Table -->
                <div class="overflow-x-auto rounded-lg shadow-sm">
                    <table class="min-w-full border border-gray-300 rounded-lg text-sm">
                        <thead class="bg-gray-200 text-black">
                            <tr>
                                <th class="px-4 py-2 border text-left">Employee Name</th>
                                <th class="px-4 py-2 border text-left">Time In</th>
                                <th class="px-4 py-2 border text-left">Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr class="hover:bg-gray-100 transition">
                                    <td class="px-4 py-2 border text-black">
                                        {{ $attendance->employeeprofiles->first_name }} {{ $attendance->employeeprofiles->last_name }}
                                    </td>
                                    <td class="px-4 py-2 border text-black">
                                        {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '—' }}
                                    </td>
                                    <td class="px-4 py-2 border text-black">
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
        <div class="content-grid flex flex-col lg:flex-row gap-6 mt-8">
            <div class="left-column flex-1">

                <!-- Activity Chart -->
                <div
                    class="chart-container bg-gradient-to-r from-orange-100 via-white to-gray-200 rounded-2xl shadow-lg p-6 mb-6 border-l-4 border-orange-300">
                    <div class="section-header flex justify-between items-center mb-4">
                        <h3 class="section-title text-lg font-bold text-black">Activity Overview of Services and Payroll Computation</h3>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-1 text-sm text-black focus:ring-orange-400 focus:border-orange-400">
                            <option>Total salary</option>
                            <option>Number of Units</option>
                            <option>Types of Services</option>
                        </select>
                    </div>
                    <div class="chart-placeholder text-black text-sm text-center py-10 border border-dashed border-gray-300 rounded-lg">
                        [📊 Activity Chart Will Appear Here]
                    </div>
                </div>

                <!-- Recent Actions -->
                <div
                    class="recent-actions bg-gradient-to-r from-orange-100 via-white to-gray-200 rounded-2xl shadow-lg p-6 border-l-4 border-orange-300">
                    <div class="section-header flex justify-between items-center mb-4">
                        <h3 class="section-title text-lg font-bold text-black">Recent Actions</h3>
                        <button
                            class="button bg-gradient-to-r from-orange-400 via-orange-300 to-orange-200 hover:from-orange-300 hover:to-orange-100 text-black px-4 py-2 rounded-lg shadow transition">
                            View All
                        </button>
                    </div>
                    <div class="actions-list flex flex-col gap-4">
                        <div
                            class="action-item flex items-center gap-3 bg-gradient-to-r from-orange-50 via-white to-gray-100 p-3 rounded-lg hover:from-orange-100 hover:to-gray-200 transition">
                            <div class="action-icon bg-orange-400 text-black p-3 rounded-full shadow">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <div class="action-title font-bold text-black">New Employee Added</div>
                                <div class="action-time text-black text-xs">2 mins ago</div>
                            </div>
                        </div>
                        <div
                            class="action-item flex items-center gap-3 bg-gradient-to-r from-orange-50 via-white to-gray-100 p-3 rounded-lg hover:from-orange-100 hover:to-gray-200 transition">
                            <div class="action-icon bg-orange-400 text-black p-3 rounded-full shadow">
                                <i class="fas fa-file-upload"></i>
                            </div>
                            <div>
                                <div class="action-title font-bold text-black">Document uploaded</div>
                                <div class="action-time text-black text-xs">15 mins ago</div>
                            </div>
                        </div>
                        <div
                            class="action-item flex items-center gap-3 bg-gradient-to-r from-orange-50 via-white to-gray-100 p-3 rounded-lg hover:from-orange-100 hover:to-gray-200 transition">
                            <div class="action-icon bg-orange-400 text-black p-3 rounded-full shadow">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div>
                                <div class="action-title font-bold text-black">System settings updated</div>
                                <div class="action-time text-black text-xs">30 mins ago</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
