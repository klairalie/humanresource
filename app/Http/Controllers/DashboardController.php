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
use Maatwebsite\Excel\Facades\Excel; // only if you installed Laravel-Excel
use App\Exports\AttendanceExport;     // only if you created the export class

class DashboardController extends Controller
{
    /**
     * Main dashboard
     */
    public function dashboard(Request $request)
    {
        // --- unchanged metrics / counts (kept as you had them) ---
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

        $attendances   = Attendance::with('employeeprofiles')->whereDate('date', $selectedDate)->get();
        $formattedDate = $selectedDate->format('l, F d Y');
        $failedCount   = DB::table('failed_jobs')->count();

        // metric selection for the UI (employee | company)
        $metric = $request->input('metric', 'company');

        // --- RUN PYTHON FORECAST (company-level attendance & services) ---
        $monthsAhead = 3;
        try {
            $forecastData = $this->buildForecast($monthsAhead);
        } catch (\Exception $e) {
            // dynamic fallback: last N months, zeros + error message
            $lastMonths = collect(range($monthsAhead - 1, 0))->map(function ($i) {
                return now()->subMonths($i)->format('M Y');
            })->values()->toArray();

            $forecastData = [
                "attendance" => ["months" => $lastMonths, "values" => array_fill(0, $monthsAhead, 0)],
                "services"   => ["months" => $lastMonths, "values" => array_fill(0, $monthsAhead, 0)],
                "error"      => $e->getMessage()
            ];

            // Log the error for debugging
            Log::error('Forecast build failed: '.$e->getMessage());
        }

        // --- Employee-level attendance analysis (last N months) ---
        $analysisMonths = 3; // last 3 months
        $monthsList = collect(range($analysisMonths - 1, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m'); // format YYYY-MM (for grouping)
        })->values()->toArray(); // oldest -> newest

        // Get attendance counts grouped by employee and month
        // We will compute present_days (status='Present') and absent_days (status='Absent') per month
        $startDate = Carbon::now()->subMonths($analysisMonths - 1)->startOfMonth()->toDateString();
        $endDate = Carbon::now()->endOfMonth()->toDateString();

        $raw = DB::table('attendances')
            ->selectRaw("employeeprofiles_id, DATE_FORMAT(`date`, '%Y-%m') as ym,
                         SUM(CASE WHEN `status` = 'Present' THEN 1 ELSE 0 END) as present_days,
                         SUM(CASE WHEN `status` = 'Absent' THEN 1 ELSE 0 END) as absent_days")
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('employeeprofiles_id', 'ym')
            ->get();

        // Reorganize into per-employee structure
        $employees = Employeeprofiles::select('employeeprofiles_id', 'first_name', 'last_name')->get()->keyBy('employeeprofiles_id');

        $employeeRecommendations = [];

        // initialize structure
        foreach ($employees as $id => $ep) {
            $employeeRecommendations[$id] = [
                'id' => $id,
                'name' => trim("{$ep->first_name} {$ep->last_name}"),
                'months' => [], // ['YYYY-MM', ...]
                'present' => [], // ints ordered like monthsList (oldest->newest)
                'absent' => [],
                'recommendation' => 'No significant trend detected. Continue monitoring.'
            ];
        }

        // fill month values default 0
        foreach ($employeeRecommendations as &$row) {
            foreach ($monthsList as $m) {
                $row['months'][] = $m;
                $row['present'][] = 0;
                $row['absent'][] = 0;
            }
        }
        unset($row);

        // fill from raw query
        foreach ($raw as $r) {
            $empId = $r->employeeprofiles_id;
            $ym = $r->ym;
            if (!isset($employeeRecommendations[$empId])) {
                // employee not found in profiles table — create minimal entry
                $employeeRecommendations[$empId] = [
                    'id' => $empId,
                    'name' => 'ID:'.$empId,
                    'months' => $monthsList,
                    'present' => array_fill(0, $analysisMonths, 0),
                    'absent' => array_fill(0, $analysisMonths, 0),
                    'recommendation' => 'No significant trend detected. Continue monitoring.'
                ];
            }

            $idx = array_search($ym, $employeeRecommendations[$empId]['months']);
            if ($idx !== false) {
                $employeeRecommendations[$empId]['present'][$idx] = (int)$r->present_days;
                $employeeRecommendations[$empId]['absent'][$idx] = (int)$r->absent_days;
            }
        }

        // Analyze trends and create recommendations for each employee
        foreach ($employeeRecommendations as &$emp) {
            // make sure arrays have length analysisMonths
            $p = $emp['present'];
            $a = $emp['absent'];

            // simple rules:
            // - if absences strictly increasing across months (a0 < a1 < a2) -> Investigate
            // - if absences all zero -> Recognize
            // - else -> Monitor
            $rec = 'Attendance is stable. Continue monitoring.';
            if (count($a) === $analysisMonths) {
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
                    $rec = 'Excellent attendance for the past months — consider recognition or reward.';
                } elseif ($increasing && $recentAbsSum >= 2) { // threshold: at least 2 recent absences and increasing
                    $rec = 'Absenteeism is increasing. Recommend HR conduct a 1-on-1, check wellbeing/workload, and document follow-up.';
                } elseif ($recentAbsSum >= 5) {
                    // many recent absences (absolute threshold)
                    $rec = 'High number of recent absences. Recommend HR investigate and consider intervention.';
                } else {
                    $rec = 'No strong negative trend; keep monitoring attendance.';
                }
            }

            $emp['recommendation'] = $rec;
        }
        unset($emp);

        // --- Company level aggregation & recommendation ---
        // Sum absences per month across all employees (for the last N months)
        $companyMonthTotals = DB::table('attendances')
            ->selectRaw("DATE_FORMAT(`date`,'%Y-%m') as ym,
                         SUM(CASE WHEN `status` = 'Present' THEN 1 ELSE 0 END) as present_days,
                         SUM(CASE WHEN `status` = 'Absent' THEN 1 ELSE 0 END) as absent_days")
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $companyAbs = [];
        foreach ($monthsList as $m) {
            $companyAbs[] = isset($companyMonthTotals[$m]) ? (int)$companyMonthTotals[$m]->absent_days : 0;
        }

        // company recommendation rules (similar)
        $companyRec = 'Company attendance is stable. Continue monitoring.';
        $increasingCompany = true;
        for ($i = 1; $i < count($companyAbs); $i++) {
            if (!($companyAbs[$i] > $companyAbs[$i - 1])) {
                $increasingCompany = false;
                break;
            }
        }
        if (array_sum($companyAbs) === 0) {
            $companyRec = 'Company-wide attendance is excellent. Consider recognition programs to maintain morale.';
        } elseif ($increasingCompany && array_sum($companyAbs) >= 3) {
            $companyRec = 'Company-wide absenteeism is increasing. Recommend HR review policies, check for seasonal causes, and perform targeted interventions.';
        } elseif (array_sum($companyAbs) >= 10) {
            $companyRec = 'Significant absenteeism company-wide. Consider immediate HR action (policy review / wellness checks).';
        }

        // --- Descriptive attendance summary for the Attendance Summary card ---
        $attendanceSummary = Attendance::selectRaw('employeeprofiles_id, COUNT(*) as total_days')
            ->groupBy('employeeprofiles_id')
            ->with('employeeprofiles')
            ->get();

        $labels = $attendanceSummary->map(function ($row) {
            $profile = $row->employeeprofiles ?? null;
            if ($profile) {
                return trim("{$profile->first_name} {$profile->last_name}");
            }
            return 'ID:'.$row->employeeprofiles_id;
        })->toArray();

        $totals = $attendanceSummary->pluck('total_days')->toArray();

        // Recent actions (kept as before)
        $recentActions = ActivityLog::with('applicant')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Return view with all expected variables
        return view('HR.dashboard', [
            "employeeCount"   => $employeeCount,
            "attendanceCount" => $attendanceCount,
            "formattedDate"   => $formattedDate,
            "attendances"     => $attendances,
            "failedCount"     => $failedCount,
            "selectedDate"    => $selectedDate->toDateString(),
            "forecastData"    => $forecastData,
            "recentActions"   => $recentActions,
            "selectedMetric"  => $metric,
            "labels"          => $labels,
            "totals"          => $totals,
            "employeeRecommendations" => $employeeRecommendations,
            "companyRecommendation" => $companyRec,
            "analysisMonths" => $analysisMonths,
            "analysisMonthsList" => $monthsList,
        ]);
    }

    /**
     * Export attendance (Excel) — requires maatwebsite/excel and AttendanceExport prepared
     */
    public function exportAttendance()
    {
        return Excel::download(new AttendanceExport, 'attendance.xlsx');
    }

    public function showEditProfile() {
        return view('HR.editprofile');
    }

    public function showSettings() {
        return view('HR.settingsconfig');
    }
/**
 * Build attendance and services forecast by calling forecasting.py
 *
 * @param int $monthsAhead
 * @return array
 * @throws \Exception
 */
protected function buildForecast($monthsAhead)
{
    $pythonPath = 'python'; // or 'python3' if your system uses that

    // FIX: point to analytics/forecasting.py
    $scriptPath = base_path('analytics/forecasting.py');

    if (!file_exists($scriptPath)) {
        throw new \Exception("forecasting.py not found at {$scriptPath}");
    }

    $process = new Process([$pythonPath, $scriptPath, (string)$monthsAhead]);
    $process->run();

    if (!$process->isSuccessful()) {
        $err = trim($process->getErrorOutput() ?: $process->getOutput());
        Log::error("Python script error output: " . $err);
        throw new ProcessFailedException($process);
    }

    $output = $process->getOutput();
    $decoded = json_decode($output, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $rawErr = trim($process->getErrorOutput());
        throw new \Exception(
            'Invalid JSON from forecasting.py: ' . json_last_error_msg() . 
            '. Raw output: ' . $rawErr . ' ' . $output
        );
    }

    if (!isset($decoded['attendance']) || !isset($decoded['services'])) {
        throw new \Exception('forecasting.py output missing expected keys (attendance/services).');
    }

    return $decoded;
}

public function recentActivities()
{
    $recentActions = ActivityLog::orderBy('created_at', 'desc')
        ->take(3)
        ->get()
        ->map(function($act) {
            return [
                'action' => $act->action ?? '—',
                'time'   => $act->created_at->diffForHumans(),
            ];
        });

    return response()->json($recentActions);
}

}
