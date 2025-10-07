<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use App\Exports\ServicesExport;

class DashboardController extends Controller
{
    /**
     * Main Dashboard
     */
public function dashboard(Request $request)
{
    $employeeCount = Employeeprofiles::count();
    $newEmployees  = Employeeprofiles::orderBy('created_at', 'desc')->take(5)->get();

    $selectedDate = $request->input('date')
        ? Carbon::parse($request->input('date'))->startOfDay()
        : Carbon::today();

    $attendanceCount = Attendance::whereDate('date', $selectedDate)
        ->where(function ($q) {
            $q->whereBetween('time_in', ['06:00:00', '17:00:00'])
              ->orWhereBetween('time_out', ['17:00:00', '18:00:00']);
        })
        ->distinct('employeeprofiles_id')
        ->count('employeeprofiles_id');

    $attendances = Attendance::with('employeeprofiles')
        ->whereDate('date', $selectedDate)
        ->get();

    $formattedDate = $selectedDate->format('l, F d Y');
    $failedCount   = DB::table('failed_jobs')->count();

    // 🔮 Forecasting: attendance + overall service (total_score)
    $monthsAhead = 3;
    try {
        $forecastData = $this->buildForecast($monthsAhead);
    } catch (\Exception $e) {
        Log::error('Forecast build failed: ' . $e->getMessage());
        $fallbackMonths = collect(range($monthsAhead - 1, 0))
            ->map(fn($i) => now()->subMonths($i)->format('M Y'))->toArray();
        $forecastData = [
            "attendance" => ["months" => $fallbackMonths, "values" => array_fill(0, $monthsAhead, 0)],
            "services"   => ["months" => $fallbackMonths, "values" => array_fill(0, $monthsAhead, 0)],
            "error"      => $e->getMessage()
        ];
    }

    // 🧠 Forecasting Insight Logic
    $companyRecommendation = null;
    $companyIcon = 'info';
    $companyIconColor = 'text-gray-600';

    if (!empty($forecastData['attendance']['values']) && !empty($forecastData['services']['values'])) {
        $attVals = $forecastData['attendance']['values'];
        $svcVals = $forecastData['services']['values'];

        $attActualAvg = array_sum(array_slice($attVals, 0, -3)) / max(1, count($attVals) - 3);
        $attForecastAvg = array_sum(array_slice($attVals, -3)) / 3;

        $svcActualAvg = array_sum(array_slice($svcVals, 0, -3)) / max(1, count($svcVals) - 3);
        $svcForecastAvg = array_sum(array_slice($svcVals, -3)) / 3;

        $attendanceTrend = $attForecastAvg - $attActualAvg;
        $serviceTrend = $svcForecastAvg - $svcActualAvg;

        if ($attendanceTrend > 0.5 && $serviceTrend > 0.5) {
            $companyRecommendation = "Both attendance and service performance are forecasted to improve — maintain current HR and training strategies.";
            $companyIcon = 'trending-up';
            $companyIconColor = 'text-green-600';
        } elseif ($attendanceTrend > 0.5 && $serviceTrend < -0.5) {
            $companyRecommendation = "Attendance is rising but service quality may drop. Focus on skill enhancement and workload balance.";
            $companyIcon = 'activity';
            $companyIconColor = 'text-yellow-500';
        } elseif ($attendanceTrend < -0.5 && $serviceTrend > 0.5) {
            $companyRecommendation = "Service quality improving but attendance may decline. Consider employee well-being or schedule adjustments.";
            $companyIcon = 'users';
            $companyIconColor = 'text-yellow-600';
        } elseif ($attendanceTrend < -0.5 && $serviceTrend < -0.5) {
            $companyRecommendation = "Both attendance and performance may decline. Immediate intervention is recommended.";
            $companyIcon = 'trending-down';
            $companyIconColor = 'text-red-600';
        } else {
            $companyRecommendation = "No major forecast changes. Continue monitoring attendance and service metrics monthly.";
            $companyIcon = 'bar-chart';
            $companyIconColor = 'text-blue-600';
        }
    } else {
        $companyRecommendation = "Not enough data to generate a meaningful forecast insight.";
        $companyIcon = 'alert-circle';
        $companyIconColor = 'text-gray-500';
    }

    // ✅ Service Summary (per employee)
    $serviceSummary = DB::table('evaluation_summaries as es')
        ->join('employeeprofiles as e', 'es.evaluatee_id', '=', 'e.employeeprofiles_id')
        ->select(
            'e.first_name',
            'e.last_name',
            DB::raw('ROUND(AVG(es.total_score), 2) as avg_score'),
            DB::raw('COUNT(es.evaluation_summary_id) as eval_count')
        )
        ->whereNotNull('es.total_score')
        ->groupBy('e.employeeprofiles_id', 'e.first_name', 'e.last_name')
        ->orderByDesc('avg_score')
        ->get();

    $serviceLabels = $serviceSummary->map(fn($r) => "{$r->first_name} {$r->last_name}")->toArray();
    $serviceAverages = $serviceSummary->pluck('avg_score')->toArray();

    // 📊 Attendance summary chart
    $attendanceSummary = Attendance::selectRaw('employeeprofiles_id, COUNT(*) as total_days')
        ->groupBy('employeeprofiles_id')
        ->with('employeeprofiles')
        ->get();

    $labels = $attendanceSummary->map(fn($r) =>
        $r->employeeprofiles
            ? "{$r->employeeprofiles->first_name} {$r->employeeprofiles->last_name}"
            : 'ID:' . $r->employeeprofiles_id
    )->toArray();

    $totals = $attendanceSummary->pluck('total_days')->toArray();

    // 🔎 Attendance insights (3 months trend)
    $analysisMonths = 3;
    $monthsList = collect(range($analysisMonths - 1, 0))
        ->map(fn($i) => Carbon::now()->subMonths($i)->format('Y-m'))
        ->toArray();

    $startDate = Carbon::now()->subMonths($analysisMonths - 1)->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();

    $raw = DB::table('attendances')
        ->selectRaw("employeeprofiles_id, DATE_FORMAT(`date`, '%Y-%m') as ym,
                     SUM(CASE WHEN `status` = 'Present' THEN 1 ELSE 0 END) as present_days,
                     SUM(CASE WHEN `status` = 'Absent' THEN 1 ELSE 0 END) as absent_days")
        ->whereBetween('date', [$startDate, $endDate])
        ->groupBy('employeeprofiles_id', 'ym')
        ->get();

    $employees = Employeeprofiles::select('employeeprofiles_id', 'first_name', 'last_name')
        ->get()->keyBy('employeeprofiles_id');

    $employeeRecommendations = [];
    foreach ($employees as $id => $ep) {
        $employeeRecommendations[$id] = [
            'id' => $id,
            'name' => "{$ep->first_name} {$ep->last_name}",
            'months' => $monthsList,
            'present' => array_fill(0, count($monthsList), 0),
            'absent' => array_fill(0, count($monthsList), 0),
            'recommendation' => 'No significant trend detected. Continue monitoring.'
        ];
    }

    foreach ($raw as $r) {
        if (!isset($employeeRecommendations[$r->employeeprofiles_id])) continue;
        $emp = &$employeeRecommendations[$r->employeeprofiles_id];
        $idx = array_search($r->ym, $emp['months']);
        if ($idx !== false) {
            $emp['present'][$idx] = (int) $r->present_days;
            $emp['absent'][$idx] = (int) $r->absent_days;
        }
    }

    foreach ($employeeRecommendations as &$emp) {
        $a = $emp['absent'];
        $p = $emp['present'];
        $rec = 'Attendance is stable. Continue monitoring.';

        // 🩶 Fix: check for new employees (no attendance data)
        if (array_sum($a) === 0 && array_sum($p) === 0) {
            $rec = 'No attendance data available yet.';
        } 
        elseif (count($a) === $analysisMonths) {
            $increasing = true;
            for ($i = 1; $i < $analysisMonths; $i++) {
                if (!($a[$i] > $a[$i - 1])) {
                    $increasing = false;
                    break;
                }
            }
            $allZero = array_sum($a) === 0;
            $recentAbsSum = $a[$analysisMonths - 1] + ($analysisMonths >= 2 ? $a[$analysisMonths - 2] : 0);
            if ($allZero) {
                $rec = 'Excellent attendance — consider recognition.';
            } elseif ($increasing && $recentAbsSum >= 2) {
                $rec = 'Absenteeism increasing — HR should intervene.';
            } elseif ($recentAbsSum >= 5) {
                $rec = 'High absences — HR should investigate.';
            }
        }
        $emp['recommendation'] = $rec;
    }
    unset($emp);

    $recentActions = ActivityLog::with('applicant')
        ->whereDate('created_at', Carbon::today())
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

    return view('HR.dashboard', [
        "employeeCount" => $employeeCount,
        "attendanceCount" => $attendanceCount,
        "formattedDate" => $formattedDate,
        "attendances" => $attendances,
        "failedCount" => $failedCount,
        "forecastData" => $forecastData,
        "companyRecommendation" => $companyRecommendation,
        "companyIcon" => $companyIcon,
        "companyIconColor" => $companyIconColor,
        "recentActions" => $recentActions,
        "labels" => $labels,
        "totals" => $totals,
        "employeeRecommendations" => $employeeRecommendations,
        "analysisMonths" => $analysisMonths,
        "analysisMonthsList" => $monthsList,
        "serviceSummary" => $serviceSummary,
        "serviceLabels" => $serviceLabels,
        "serviceAverages" => $serviceAverages,
        "selectedMetric" => $request->input('metric', 'overall'),
    ]);
}


    /** ⬇ Export attendance */
    public function exportAttendance()
    {
        return Excel::download(new AttendanceExport, 'attendance.xlsx');
    }

    /** ⬇ Export services */
    public function exportServices()
    {
        return Excel::download(new ServicesExport, 'services_summary.xlsx');
    }

    /** ⚙ Run Python forecasting safely */
    protected function buildForecast($monthsAhead)
    {
        $pythonPath = 'python'; // use 'python3' if Linux/macOS
        $scriptPath = base_path('analytics/forecasting.py');

        if (!file_exists($scriptPath)) {
            throw new \Exception("forecasting.py not found at {$scriptPath}");
        }

        $process = new Process([$pythonPath, $scriptPath, (string) $monthsAhead]);
        $process->run();

        if (!$process->isSuccessful()) {
            $err = trim($process->getErrorOutput() ?: $process->getOutput());
            Log::error("Python script error: " . $err);
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $decoded = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON from forecasting.py: ' . json_last_error_msg());
        }

        if (!isset($decoded['attendance']) || !isset($decoded['services'])) {
            throw new \Exception('forecasting.py output missing expected keys.');
        }

        return $decoded;
    }


     public function showEditProfile()
    {
        return view('HR.editprofile');
    }

    /**
     * Settings page
     */
    public function showSettings()
    {
        return view('HR.settingsconfig');
    }

}
