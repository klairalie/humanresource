<x-guest-layout>
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
                    <i data-lucide="users" class="w-6 h-6 text-orange-400"></i>
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
                    <i data-lucide="calendar-check" class="w-6 h-6 text-green-500"></i>
                </div>
            </div>

            <!-- Number of Units -->
            <div
                class="bg-gradient-to-b from-orange-200 via-white to-gray-300 rounded-2xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1 p-4 sm:p-6 flex justify-between items-center h-32 sm:h-36">
                <div class="flex flex-col">
                    <div class="text-3xl sm:text-4xl font-bold text-gray-900 flex items-center gap-2 sm:gap-3">
                        {{-- unit count here --}}
                    </div>
                    <div class="text-sm sm:text-base font-medium text-gray-700 mt-1">
                        Number of AC Units
                    </div>
                </div>
                <div class="bg-black text-white p-3 sm:p-4 rounded-lg shadow">
                    <i data-lucide="boxes" class="w-6 h-6 text-gray-300"></i>
                </div>
            </div>
        </div>

        <!-- Attendance Modal -->
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 sm:p-6"
            x-show="openModal" x-cloak>
            <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-4 sm:p-6 relative animate-fadeIn">
                <button @click="openModal = false"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>

                <h2 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 border-b border-gray-200 pb-2">
                    Attendance Details - {{ $formattedDate }}
                </h2>

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

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10 mb-20 auto-rows-[minmax(300px,auto)]">

            <!-- Forecast Card -->
            <div class="bg-gradient-to-r from-orange-300 to-gray-300 rounded-xl shadow-md p-4 sm:p-6 border border-gray-200 flex flex-col h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Performance Overview</h3>
                    <form id="metricForm" method="GET" action="{{ route('show.dashboard') }}">
                        <select name="metric" onchange="document.getElementById('metricForm').submit()"
                            class="border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-800 w-full sm:w-auto">
                            <option value="employee" {{ request('metric') === 'employee' ? 'selected' : '' }}>Employee Performance</option>
                            <option value="company" {{ request('metric', 'company') === 'company' ? 'selected' : '' }}>Company Performance</option>
                        </select>
                    </form>
                </div>

                <div class="flex-1 flex items-center justify-center min-h-[250px]">
                    <canvas id="forecastChart"></canvas>
                </div>

                @if(isset($forecastData['error']))
                    <div class="mt-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-2">
                        ⚠ Forecasting Error: {{ $forecastData['error'] }}
                    </div>
                @endif

                <div class="mt-4 bg-white rounded p-3 border">
                    <div class="font-semibold">Company Insight</div>
                    <div class="text-sm text-gray-700 mt-2">
                        {{ $companyRecommendation ?? 'No recommendation available.' }}
                    </div>
                </div>
            </div>

            <!-- Attendance Summary + Employee Insights -->
            <div class="space-y-6">
                <!-- Attendance Summary Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Attendance Summary</h2>
                    <div class="min-h-[250px]">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                    <div class="mt-4 flex gap-2 flex-wrap">
                        <a href="{{ route('attendance.export') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Export Attendance to Excel</a>
                        <a href="{{ $forecastData['exports']['csv'] ?? '#' }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Download Forecast CSV</a>
                    </div>
                </div>

                <!-- Employee Insights -->
                <div class="bg-gradient-to-r from-orange-300 to-gray-300 rounded-xl shadow-md p-4 border border-gray-200">
                    <h3 class="font-bold mb-3">Employee Insights (last {{ $analysisMonths }} months)</h3>

                    <div class="max-h-72 overflow-y-auto space-y-3">
                        @foreach($employeeRecommendations as $emp)
                            <div class="bg-white p-3 rounded shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold">{{ $emp['name'] }}</div>
                                        <div class="text-xs text-gray-600">
                                            Months: {{ implode(', ', $emp['months']) }}
                                        </div>
                                    </div>
                                    <div class="text-right text-sm">
                                        <div class="text-gray-700">Present: {{ array_sum($emp['present']) }}</div>
                                        <div class="text-gray-700">Absent: {{ array_sum($emp['absent']) }}</div>
                                    </div>
                                </div>

                                <div class="mt-2 text-sm text-gray-700">
                                    Recommendation: <span class="font-medium">{{ $emp['recommendation'] }}</span>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                                    <div class="bg-gray-50 p-2 rounded">
                                        <div class="font-semibold">Present (per mo)</div>
                                        <div>{{ implode(' / ', $emp['present']) }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded">
                                        <div class="font-semibold">Absent (per mo)</div>
                                        <div>{{ implode(' / ', $emp['absent']) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if(count($employeeRecommendations) === 0)
                            <div class="text-gray-600">No employee attendance data found for the selected months.</div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Recent Activities</h2>
                        <button onclick="refreshActivities()" 
                            class="text-sm text-blue-600 hover:underline">
                            Refresh
                        </button>
                    </div>
                    <ul id="recentActivities" class="space-y-3 text-sm text-gray-700">
                        @foreach($recentActions as $action)
                            <li class="border-b pb-2">
                                <span class="font-medium">{{ $action->action ?? '—' }}</span>
                                <br>
                                <span class="text-xs text-gray-500">
                                    {{ $action->created_at->diffForHumans() }}
                                </span>
                            </li>
                        @endforeach
                        @if($recentActions->isEmpty())
                            <li class="text-gray-500">No recent activity today.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

<!-- Scripts -->
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Attendance summary chart
    const attendanceCtx = document.getElementById('attendanceChart')?.getContext('2d');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Attendance Days',
                    data: @json($totals),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Forecast chart
    const forecastData = @json($forecastData);
    const selectedMetric = "{{ $selectedMetric }}";
    const forecastCanvas = document.getElementById('forecastChart');
    if (forecastCanvas) {
        const forecastCtx = forecastCanvas.getContext('2d');
        const hasAttendance = forecastData?.attendance?.values?.length > 0;
        const hasServices = forecastData?.services?.values?.length > 0;

        if (hasAttendance || hasServices) {
            const months = forecastData.attendance?.months || [];
            let datasets = [];

            function splitValues(values, forecastCount = 3) {
                if (!values) return { actual: [], forecast: [] };
                const splitIndex = values.length - forecastCount;
                return {
                    actual: values.slice(0, splitIndex),
                    forecast: values.slice(splitIndex)
                };
            }

            if (selectedMetric === "employee") {
                let att = splitValues(forecastData.attendance?.values);
                datasets.push(
                    {
                        type: "bar",
                        label: "Attendance (Actual)",
                        data: att.actual.concat(new Array(att.forecast.length).fill(null)),
                        backgroundColor: "rgba(54, 162, 235, 0.7)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    },
                    {
                        type: "bar",
                        label: "Attendance (Forecast)",
                        data: new Array(att.actual.length).fill(null).concat(att.forecast),
                        backgroundColor: "rgba(54, 162, 235, 0.3)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                        borderDash: [5, 5]
                    }
                );

                let serv = splitValues(forecastData.services?.values);
                datasets.push(
                    {
                        type: "bar",
                        label: "Services (Actual)",
                        data: serv.actual.concat(new Array(serv.forecast.length).fill(null)),
                        backgroundColor: "rgba(255, 159, 64, 0.7)",
                        borderColor: "rgba(255, 159, 64, 1)",
                        borderWidth: 1
                    },
                    {
                        type: "bar",
                        label: "Services (Forecast)",
                        data: new Array(serv.actual.length).fill(null).concat(serv.forecast),
                        backgroundColor: "rgba(255, 159, 64, 0.3)",
                        borderColor: "rgba(255, 159, 64, 1)",
                        borderWidth: 1,
                        borderDash: [5, 5]
                    }
                );
            } else {
                let attVals = forecastData.attendance?.values || [];
                let servVals = forecastData.services?.values || [];
                let companyVals = attVals.map((v, i) => v + (servVals[i] || 0));

                let comp = splitValues(companyVals);

                datasets.push(
                    {
                        type: "line",
                        label: "Company (Actual)",
                        data: comp.actual.concat(new Array(comp.forecast.length).fill(null)),
                        borderColor: "rgba(75, 192, 192, 1)",
                        backgroundColor: "rgba(75, 192, 192, 0.7)",
                        tension: 0.3,
                        borderWidth: 2,
                        fill: false
                    },
                    {
                        type: "line",
                        label: "Company (Forecast)",
                        data: new Array(comp.actual.length).fill(null).concat(comp.forecast),
                        borderColor: "rgba(75, 192, 192, 0.5)",
                        backgroundColor: "rgba(75, 192, 192, 0.3)",
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3,
                        fill: false
                    }
                );
            }

            new Chart(forecastCtx, {
                type: "bar",
                data: { labels: months, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        } else {
            forecastCanvas.parentElement.innerHTML =
                "<p class='text-gray-700 text-center'>⚠ No forecast data available</p>";
        }
    }
</script>

<script>
function refreshActivities() {
    fetch("{{ route('dashboard.activities') }}")
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById("recentActivities");
            list.innerHTML = "";

            if (data.length === 0) {
                list.innerHTML = "<li class='text-gray-500'>No recent activity today.</li>";
                return;
            }

            data.forEach(act => {
                let li = document.createElement("li");
                li.className = "border-b pb-2";
                li.innerHTML = `
                    <span class="font-medium">${act.action}</span><br>
                    <span class="text-xs text-gray-500">${act.time}</span>
                `;
                list.appendChild(li);
            });
        });
}
setInterval(refreshActivities, 30000);
</script>

</x-guest-layout>
