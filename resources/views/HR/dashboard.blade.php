<x-guest-layout>
    <div class="min-h-screen text-gray-900 mb-20" x-data="{ openModal: false }">

        <!-- ==================== TOP STATS GRID ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <!-- Number of Employees -->
            <a href="{{ route('show.employeeprofiles') }}"
               class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 p-6 flex justify-between items-center h-32">
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ $employeeCount }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-1">Number of Employees</div>
                </div>
                <div class="bg-orange-100 text-orange-600 p-3 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </a>

            <!-- Attendance (Modal Trigger) -->
            <div @click="openModal = true"
                 class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 p-6 flex justify-between items-center h-32 cursor-pointer">
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ $attendanceCount }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-1">Attendance (In & Out)</div>
                    <div class="text-xs text-gray-500 mt-1 italic">
                        Click to view details — {{ $formattedDate }}
                    </div>
                </div>
                <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                    <i data-lucide="calendar-check" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Number of AC Units -->
            <a href="#"
               class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 p-6 flex justify-between items-center h-32">
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ $acUnitCount ?? '—' }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-1">Number of AC Units</div>
                </div>
                <div class="bg-sky-100 text-sky-600 p-3 rounded-lg">
                    <i data-lucide="wind" class="w-6 h-6"></i>
                </div>
            </a>
        </div>

        <!-- ==================== ATTENDANCE MODAL ==================== -->
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-6"
             x-show="openModal" x-cloak>
            <div class="bg-white w-full max-w-4xl rounded-xl shadow-2xl p-6 relative animate-fadeIn">
                <button @click="openModal = false"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;
                </button>
                <h2 class="text-xl font-bold mb-4 text-gray-900 border-b border-gray-200 pb-2">
                    Attendance Details — {{ $formattedDate }}
                </h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Employee</th>
                                <th class="px-4 py-3 text-left font-semibold">Time In</th>
                                <th class="px-4 py-3 text-left font-semibold">Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 border-t">
                                        {{ $attendance->employeeprofiles->first_name }} {{ $attendance->employeeprofiles->last_name }}
                                    </td>
                                    <td class="px-4 py-3 border-t">
                                        {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 border-t">
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
        <div class="columns-1 lg:columns-2 gap-6 mt-10 [column-fill:_balance]">

            <!-- Overall Forecast -->
            <div class="mb-8 break-inside-avoid bg-white rounded-xl border border-gray-200 shadow p-6 flex flex-col">
                <h3 class="font-bold text-gray-900 mb-4">Overall Forecast</h3>
                <div class="flex-1 flex items-center justify-center min-h-[320px]">
                    <canvas id="forecastChart"></canvas>
                </div>
                @if(isset($forecastData['error']))
                    <div class="mt-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-2">
                        ⚠ Forecasting Error: {{ $forecastData['error'] }}
                    </div>
                @endif
                <div class="mt-4 bg-gray-50 rounded-lg p-4 border flex items-start gap-3">
                    <div class="p-2 bg-white rounded-lg shadow">
                        <i data-lucide="{{ $companyIcon ?? 'info' }}"
                           class="w-6 h-6 {{ $companyIconColor ?? 'text-gray-600' }}"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Forecast Insight</div>
                        <div class="text-sm text-gray-700 mt-1 leading-relaxed">
                            {{ $companyRecommendation ?? 'No recommendation available.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Insights -->
            <div class="mb-8 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
                <h3 class="font-bold mb-4">Employee Insights (Last {{ $analysisMonths }} months)</h3>
                <div class="max-h-72 overflow-y-auto space-y-3">
                    @foreach($employeeRecommendations as $emp)
                        <div class="bg-gray-50 p-3 rounded-lg shadow-sm border">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-semibold">{{ $emp['name'] }}</div>
                                    <div class="text-xs text-gray-600">
                                        Months: {{ implode(', ', $emp['months']) }}
                                    </div>
                                </div>
                                <div class="text-right text-sm text-gray-700">
                                    <div>Present: {{ array_sum($emp['present']) }}</div>
                                    <div>Absent: {{ array_sum($emp['absent']) }}</div>
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                Recommendation: <span class="font-medium">{{ $emp['recommendation'] }}</span>
                            </div>
                        </div>
                    @endforeach
                    @if(count($employeeRecommendations) === 0)
                        <div class="text-gray-500 text-sm">No data available for this period.</div>
                    @endif
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="mb-8 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
                <h2 class="text-lg font-semibold mb-4">Attendance Summary</h2>
                <div class="min-h-[250px]">
                    <canvas id="attendanceChart"></canvas>
                </div>
                <div class="mt-4 flex gap-3 flex-wrap">
                    <a href="{{ route('attendance.export') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Export Attendance
                    </a>
                </div>
            </div>

            <!-- Service Summary -->
            <div id="serviceSection" class="mb-8 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
                <h2 class="text-lg font-semibold mb-4">Service Summary</h2>
                <div class="min-h-[250px]">
                    <canvas id="serviceChart"></canvas>
                </div>
                <div class="mt-4">
                    <a href="{{ route('services.export') }}"
                       class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                        Export Services
                    </a>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="mb-10 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-semibold">Recent Activities</h2>
                    <a href="{{ route('recent-activities.index') }}"
                       class="text-sm text-blue-600 hover:underline font-medium">
                        View All →
                    </a>
                </div>
                <ul id="recentActivities" class="space-y-3 text-sm text-gray-700 max-h-96 overflow-y-auto pr-2">
                    @forelse($recentActions as $action)
                        <li class="border-b pb-2">
                            <span class="font-medium">{{ $action->description ?? '—' }}</span><br>
                            <span class="text-xs text-gray-500">
                                By: {{ $action->employeeprofiles->name ?? 'Unknown Employee' }} •
                                {{ $action->created_at->diffForHumans() }}
                            </span>
                        </li>
                    @empty
                        <li class="text-gray-500">No recent activity found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>



<!-- Scripts -->
<script src="https://unpkg.com/lucide@latest"></script>
<script> lucide.createIcons(); </script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Attendance Summary Chart
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

    // Service Summary Chart
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

    // Forecast Chart
   const forecastData = @json($forecastData);
const forecastCanvas = document.getElementById('forecastChart');

if (forecastCanvas) {
    const ctx = forecastCanvas.getContext('2d');
    const attMonths = forecastData?.attendance?.months || [];
    const attVals = forecastData?.attendance?.values || [];
    const svcMonths = forecastData?.services?.months || [];
    const svcVals = forecastData?.services?.values || [];

    // 🔧 Determine unified month labels (latest of both)
    const allMonths = Array.from(new Set([...attMonths, ...svcMonths])).sort(
        (a, b) => new Date(a) - new Date(b)
    );

    // 🔧 Map each dataset to unified month list
    const mapValues = (months, values) => {
        const mapping = Object.fromEntries(months.map((m, i) => [m, values[i]]));
        return allMonths.map(m => mapping[m] ?? null);
    };

    // Split into actual + forecast for both datasets
    const splitValues = (values, forecastCount = 3) => {
        const splitIndex = Math.max(0, values.length - forecastCount);
        return {
            actual: values.slice(0, splitIndex),
            forecast: values.slice(splitIndex)
        };
    };

    const attSplit = splitValues(attVals, 3);
    const svcSplit = splitValues(svcVals, 3);

    // Map adjusted values
    const attActualMapped = mapValues(attMonths.slice(0, -3), attSplit.actual);
    const attForecastMapped = mapValues(attMonths.slice(-3), attSplit.forecast);
    const svcActualMapped = mapValues(svcMonths.slice(0, -3), svcSplit.actual);
    const svcForecastMapped = mapValues(svcMonths.slice(-3), svcSplit.forecast);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [
                {
                    label: "Attendance (Actual)",
                    data: attActualMapped,
                    borderColor: "rgba(54,162,235,1)",
                    backgroundColor: "rgba(54,162,235,0.3)",
                    spanGaps: true,
                    tension: 0.3
                },
                {
                    label: "Attendance (Forecast)",
                    data: attForecastMapped,
                    borderColor: "rgba(54,162,235,0.4)",
                    borderDash: [5,5],
                    spanGaps: true,
                    tension: 0.3
                },
                {
                    label: "Service Performance (Actual)",
                    data: svcActualMapped,
                    borderColor: "rgba(255,159,64,1)",
                    backgroundColor: "rgba(255,159,64,0.3)",
                    spanGaps: true,
                    tension: 0.3
                },
                {
                    label: "Service Performance (Forecast)",
                    data: svcForecastMapped,
                    borderColor: "rgba(255,159,64,0.4)",
                    borderDash: [5,5],
                    spanGaps: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const val = ctx.parsed.y ?? 'N/A';
                            return `${ctx.dataset.label}: ${val}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { autoSkip: true, maxRotation: 0 },
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Values' }
                }
            }
        }
    });
}
</script>
</x-guest-layout>
