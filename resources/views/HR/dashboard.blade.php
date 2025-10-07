<x-guest-layout>
    <div class="main-content p-6 sm:p-10 min-h-screen text-gray-900" x-data="{ openModal: false }">

<!-- Masonry-Style Dashboard Layout -->
<div class="columns-1 lg:columns-2 gap-6 mt-10 [column-fill:_balance]">

    <!-- Forecast Section (taller item) -->
    <div class="mb-6 break-inside-avoid bg-white rounded-xl border border-gray-200 shadow p-6 flex flex-col">
        <h3 class="font-bold text-gray-900 mb-4">Overall Forecast</h3>

        <div class="flex-1 flex items-center justify-center min-h-[350px]">
            <canvas id="forecastChart"></canvas>
        </div>

        @if(isset($forecastData['error']))
            <div class="mt-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded p-2">
                ⚠ Forecasting Error: {{ $forecastData['error'] }}
            </div>
        @endif

        <div class="mt-4 bg-gray-50 rounded-lg p-4 border flex items-start gap-3">
            <div class="p-2 bg-white rounded-lg shadow">
                <i data-lucide="{{ $companyIcon ?? 'info' }}" class="w-6 h-6 {{ $companyIconColor ?? 'text-gray-600' }}"></i>
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
    <div class="mb-6 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
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
    <div class="mb-6 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
        <h2 class="text-lg font-semibold mb-4">Attendance Summary</h2>
        <div class="min-h-[250px]"><canvas id="attendanceChart"></canvas></div>
        <div class="mt-4 flex gap-3 flex-wrap">
            <a href="{{ route('attendance.export') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Export Attendance</a>
           
        </div>
    </div>

    <!-- Service Summary -->
    <div id="serviceSection" class="mb-6 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
        <h2 class="text-lg font-semibold mb-4">Service Summary</h2>
        <div class="min-h-[250px]"><canvas id="serviceChart"></canvas></div>
        <div class="mt-4">
            <a href="{{ route('services.export') }}" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                Export Services
            </a>
        </div>
    </div>

    

    <!-- Recent Activities -->
    <div class="mb-6 break-inside-avoid bg-white rounded-xl shadow p-6 border border-gray-200">
        <h2 class="text-lg font-semibold mb-4">Recent Activities</h2>
        <ul id="recentActivities" class="space-y-3 text-sm text-gray-700">
            @forelse($recentActions as $action)
                <li class="border-b pb-2">
                    <span class="font-medium">{{ $action->action ?? '—' }}</span><br>
                    <span class="text-xs text-gray-500">{{ $action->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="text-gray-500">No recent activity today.</li>
            @endforelse
        </ul>
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
        const months = forecastData?.attendance?.months || [];
        const attVals = forecastData?.attendance?.values || [];
        const svcVals = forecastData?.services?.values || [];

        function splitValues(values, forecastCount = 3) {
            const splitIndex = Math.max(0, values.length - forecastCount);
            return {
                actual: values.slice(0, splitIndex),
                forecast: values.slice(splitIndex)
            };
        }

        const attSplit = splitValues(attVals, 3);
        const svcSplit = splitValues(svcVals, 3);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: "Attendance (Actual)",
                        data: attSplit.actual.concat(new Array(attSplit.forecast.length).fill(null)),
                        borderColor: "rgba(54,162,235,1)",
                        backgroundColor: "rgba(54,162,235,0.3)",
                        tension: 0.3
                    },
                    {
                        label: "Attendance (Forecast)",
                        data: new Array(attSplit.actual.length).fill(null).concat(attSplit.forecast),
                        borderColor: "rgba(54,162,235,0.4)",
                        borderDash: [5,5],
                        tension: 0.3
                    },
                    {
                        label: "Service Performance (Actual)",
                        data: svcSplit.actual.concat(new Array(svcSplit.forecast.length).fill(null)),
                        borderColor: "rgba(255,159,64,1)",
                        backgroundColor: "rgba(255,159,64,0.3)",
                        tension: 0.3
                    },
                    {
                        label: "Service Performance (Forecast)",
                        data: new Array(svcSplit.actual.length).fill(null).concat(svcSplit.forecast),
                        borderColor: "rgba(255,159,64,0.4)",
                        borderDash: [5,5],
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Values' } } }
            }
        });
    }
</script>
</x-guest-layout>
