<x-guest-layout>
<div class="text-gray-900 mb-10 mt-10 p-4" x-data="{ openModal: false }">

    <!-- ==================== TOP STATS GRID ==================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
        <!-- Number of Employees -->
        <a href="{{ route('show.employeeprofiles') }}"
           class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5 p-4 flex justify-between items-center h-24">
            <div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $employeeCount }}</div>
                <div class="text-xs sm:text-sm font-medium text-gray-600 mt-0.5">Number of Employees</div>
            </div>
            <div class="bg-orange-100 text-orange-600 p-2 rounded-lg">
                <i data-lucide="users" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </a>

        <!-- Attendance -->
        <div @click="openModal = true"
             class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5 p-4 flex justify-between items-center h-24 cursor-pointer">
            <div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $attendanceCount }}</div>
                <div class="text-xs sm:text-sm font-medium text-gray-600 mt-0.5">Attendance (In & Out)</div>
                <div class="text-2xs text-gray-500 mt-0.5 italic">
                    Click to view — {{ $formattedDate }}
                </div>
            </div>
            <div class="bg-green-100 text-green-600 p-2 rounded-lg">
                <i data-lucide="calendar-check" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Number of AC Units -->
        <a href="#"
           class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5 p-4 flex justify-between items-center h-24">
            <div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $acUnitCount ?? '—' }}</div>
                <div class="text-xs sm:text-sm font-medium text-gray-600 mt-0.5">Number of AC Units</div>
            </div>
            <div class="bg-sky-100 text-sky-600 p-2 rounded-lg">
                <i data-lucide="wind" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </a>
    </div>

    <!-- ==================== ATTENDANCE MODAL ==================== -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         x-show="openModal" x-cloak>
        <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl p-4 relative animate-fadeIn">
            <button @click="openModal = false"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;
            </button>
            <h2 class="text-lg font-bold mb-2 text-gray-900 border-b border-gray-200 pb-1">
                Attendance Details — {{ $formattedDate }}
            </h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200 max-h-72">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-2 py-2 text-left font-semibold">Employee</th>
                            <th class="px-2 py-2 text-left font-semibold">Time In</th>
                            <th class="px-2 py-2 text-left font-semibold">Time Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-2 py-1 border-t">
                                    {{ $attendance->employeeprofiles->first_name }} {{ $attendance->employeeprofiles->last_name }}
                                </td>
                                <td class="px-2 py-1 border-t">
                                    {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '—' }}
                                </td>
                                <td class="px-2 py-1 border-t">
                                    {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Pending' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN CONTENT SECTIONS ==================== -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Overall Forecast -->
        <div class="bg-white rounded-xl border border-gray-200 shadow p-4 flex flex-col">
            <h3 class="font-bold text-gray-900 mb-2 text-sm sm:text-base">Overall Forecast</h3>
            <div class="flex-1 flex items-center justify-center min-h-[180px]">
                <canvas id="forecastChart"></canvas>
            </div>
            @if(isset($forecastData['error']))
                <div class="mt-2 text-xs text-red-600 bg-red-100 border border-red-300 rounded p-1">
                    ⚠ Forecasting Error: {{ $forecastData['error'] }}
                </div>
            @endif
            <div class="mt-2 bg-gray-50 rounded-lg p-2 border flex items-start gap-2 text-xs">
                <div class="p-1 bg-white rounded-lg shadow">
                    <i data-lucide="trending-up" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div>
                    <div class="font-semibold text-gray-900">Forecast Insight</div>
                    <div class="text-gray-700 mt-0.5 leading-snug">
                       @if(isset($forecastData['forecast_insight']))
            @php
                $insight = $forecastData['forecast_insight'] ?? '';
                // Highlight key numbers
                $insight = preg_replace('/(\d+(\.\d+)?%)/', '<strong>$1</strong>', $insight);
                $insight = preg_replace('/\b(improve|increase|decline|decrease)\b/i', '<strong>$1</strong>', $insight);

                $color = Str::contains($insight, ['improve','increase']) ? 'text-green-600' :
                         (Str::contains($insight, ['decline','decrease']) ? 'text-red-600' : 'text-gray-700');
            @endphp
            <span class="{{ $color }}">{!! $insight !!}</span>
        @else
            No forecast insight available.
        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Insights -->
        <div class="bg-white rounded-xl shadow p-4 border border-gray-200">
            <h3 class="font-bold mb-2 text-sm sm:text-base">Employee Insights (Last {{ $analysisMonths }} months)</h3>
            <div class="max-h-64 overflow-y-auto space-y-2 text-xs sm:text-sm">
                @foreach($employeeRecommendations as $emp)
                    <div class="bg-gray-50 p-2 rounded-lg shadow-sm border">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold">{{ $emp['name'] }}</div>
                                <div class="text-xs text-gray-600">
                                    Months: {{ implode(', ', $emp['months']) }}
                                </div>
                            </div>
                            <div class="text-right text-xs text-gray-700">
                                <div>Present: {{ array_sum($emp['present']) }}</div>
                                <div>Absent: {{ array_sum($emp['absent']) }}</div>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-gray-700">
                            Recommendation: <span class="font-medium">{{ $emp['recommendation'] }}</span>
                        </div>
                    </div>
                @endforeach
                @if(count($employeeRecommendations) === 0)
                    <div class="text-gray-500 text-xs">No data available for this period.</div>
                @endif
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="bg-white rounded-xl shadow p-4 border border-gray-200">
            <h2 class="text-sm sm:text-base font-semibold mb-2">Attendance Summary</h2>
            <div class="min-h-[180px]">
                <canvas id="attendanceChart"></canvas>
            </div>
            <div class="mt-2 flex gap-2 flex-wrap text-xs">
                <a href="{{ route('attendance.export') }}"
                   class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Export</a>
            </div>
        </div>

        <!-- Service Summary -->
        <div class="bg-white rounded-xl shadow p-4 border border-gray-200">
            <h2 class="text-sm sm:text-base font-semibold mb-2">Service Summary</h2>
            <div class="min-h-[180px]">
                <canvas id="serviceChart"></canvas>
            </div>
            <div class="mt-2 text-xs">
                <a href="{{ route('services.export') }}"
                   class="px-2 py-1 bg-orange-600 text-white rounded hover:bg-orange-700">Export</a>
            </div>
        </div>

    

    </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow p-4 border border-gray-200 mt-10 mb-10">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-sm sm:text-base font-semibold">Recent Activities</h2>
                <a href="{{ route('recent-activities.index') }}"
                   class="text-xs sm:text-sm text-blue-600 hover:underline font-medium">View All →</a>
            </div>
            <ul id="recentActivities" class="space-y-1 text-xs sm:text-sm text-gray-700 max-h-64 overflow-y-auto pr-2">
                @forelse($recentActions as $action)
                    <li class="border-b pb-1">
                        <span class="font-medium">{{ $action->description ?? '—' }}</span><br>
                        <span class="text-2xs text-gray-500">
                            By:
                            @if($action->employeeprofiles)
                                {{ $action->employeeprofiles->first_name }} {{ $action->employeeprofiles->last_name }}
                            @elseif($action->applicant)
                                {{ $action->applicant->first_name }} {{ $action->applicant->last_name }}
                            @else
                                System
                            @endif
                            • {{ $action->created_at->diffForHumans() }}
                        </span>
                    </li>
                @empty
                    <li class="text-gray-500 text-xs">No recent activity found.</li>
                @endforelse
            </ul>
        </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* === Attendance Summary Chart === */
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
}

/* === Service Summary Chart === */
const serviceCtx = document.getElementById('serviceChart')?.getContext('2d');
if (serviceCtx) {
    new Chart(serviceCtx, {
        type: 'bar',
        data: {
            labels: @json($serviceLabels),
            datasets: [{
                label: 'Average Total Score',
                data: @json($serviceAverages),
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Average Score' } } },
            plugins: { legend: { display: false } }
        }
    });
}

/* === Forecast Chart === */
const forecastData = @json($forecastData);
const forecastCanvas = document.getElementById('forecastChart');

if (forecastCanvas) {
    const ctx = forecastCanvas.getContext('2d');
    const attMonths = forecastData?.attendance?.months || [];
    const attVals = forecastData?.attendance?.values || [];
    const svcMonths = forecastData?.services?.months || [];
    const svcVals = forecastData?.services?.values || [];

    const allMonths = Array.from(new Set([...attMonths, ...svcMonths])).sort((a, b) => new Date(a) - new Date(b));
    const mapValues = (months, values) => Object.fromEntries(months.map((m, i) => [m, values[i]]));
    const attMap = mapValues(attMonths, attVals);
    const svcMap = mapValues(svcMonths, svcVals);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [
                { label: "Attendance", data: allMonths.map(m => attMap[m] ?? null), borderColor: "rgba(54,162,235,1)", tension: 0.3 },
                { label: "Service", data: allMonths.map(m => svcMap[m] ?? null), borderColor: "rgba(255,159,64,1)", tension: 0.3 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Performance (%)' } } }
        }
    });
}
</script>
</x-guest-layout>
