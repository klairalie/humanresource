<?php

namespace App\Http\Controllers;

use App\Notifications\ApplicantHiredNotification;
use App\Notifications\ApplicantRejectedNotification;    
use Illuminate\Support\Facades\DB;
use App\Notifications\StatusChangedNotification;
use App\Mail\ApplicantStatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Models\Applicant;
use App\Models\ApplicantSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Assessment;
use App\Notifications\ScreeningPassed;
use App\Notifications\InterviewScheduledNotification;
use App\Models\Interview;
use App\Notifications\ApplicantFailedNotification;
class ApplicantController extends Controller
{
    public function showForm()
    {
        return view('Applicants.Applicationform');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'first_name'        => 'required|string|max:100',
        'last_name'         => 'required|string|max:100',
        'contact_number'    => 'required|string|max:20|unique:applicants,contact_number',
        'email'             => 'required|email|max:150|unique:applicants,email',
        'address'           => 'required|string',
        'date_of_birth'     => 'required|date',
        'emergency_contact' => 'required|string|max:150',
        'position'          => 'required|in:Helper,Assistant Technician,Technician,Human Resource Manager,Administrative Manager,Finance Manager',
        'career_objective'  => 'required|string',
        'work_experience'   => 'required|string',
        'education'         => 'required|string',
        'skills'            => 'required|string',
        'achievements'      => 'required|string',
        'references'        => 'required|string',
        'good_moral_file'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'coe_file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'resume_file'       => 'required|file|mimes:jpg,jpeg,png,doc,docx|max:5120', // ✅ allow docs & larger size
]);

// Handle uploads
if ($request->hasFile('good_moral_file')) {
    $validated['good_moral_file'] = $request->file('good_moral_file')
        ->store('applicants_files', 'public');
}

if ($request->hasFile('coe_file')) {
    $validated['coe_file'] = $request->file('coe_file')
        ->store('applicants_files', 'public');
}

if ($request->hasFile('resume_file')) {
    $validated['resume_file'] = $request->file('resume_file')
        ->store('applicants_files', 'public');
}


    Applicant::create($validated);

    return redirect()->route('show.applicationform')->with('success', 'Application submitted successfully!');
}

public function validateField(Request $request)
{
    $field = $request->get('field');
    $value = $request->get('value');

    $rules = [
        'first_name'        => 'required|string|max:100',
        'last_name'         => 'required|string|max:100',
        'contact_number'    => 'required|string|max:20|unique:applicants,contact_number',
        'email'             => 'required|email|max:150|unique:applicants,email',
        'address'           => 'required|string',
        'date_of_birth'     => 'required|date',
        'emergency_contact' => 'required|string|max:150',
        'position'          => 'required|in:Helper,Assistant Technician,Technician,Human Resource Manager,Administrative Manager,Finance Manager',
        'career_objective'  => 'required|string',
        'work_experience'   => 'required|string',
        'education'         => 'required|string',
        'skills'            => 'required|string',
        'achievements'      => 'required|string',
        'references'        => 'required|string',
    ];

    $validator = Validator::make(
        [$field => $value],
        [$field => $rules[$field] ?? 'nullable']
    );

    if ($validator->fails()) {
        return response()->json(['valid' => false, 'message' => $validator->errors()->first($field)]);
    }
    return response()->json(['valid' => true]);
}



 public function index()
{
    // use correct relationship names from Applicant model
    $applicants = Applicant::with(['result', 'summary'])
        ->get()
        ->map(function ($applicant) {
            $applicant->has_assessment_results = $applicant->results && $applicant->results->isNotEmpty() ? 1 : 0;
            return $applicant;
        });

    $assessments = Assessment::all();

    return view('HR.applicantlist', compact('applicants', 'assessments'));
}




    public function show($applicant_id)
    {
        $applicant = Applicant::findOrFail($applicant_id);
        return view('applicants.show', compact('applicant'));
    }



  public function summarize($applicant_id)
    {
        // Find applicant (your primary key appears to be applicant_id)
        $applicant = Applicant::findOrFail($applicant_id);

        // Prevent duplicate summary: if exists, redirect to it
        $existingSummary = ApplicantSummary::where('applicant_id', $applicant->applicant_id)->first();
        if ($existingSummary) {
            return redirect()
                ->route('applicants.summary.show', $existingSummary->id)
                ->with('info', 'Applicant already summarized. Redirected to existing summary.');
        }

        // Fetch latest assessment result
        $assessment = DB::table('assessment_results')
            ->where('applicant_id', $applicant->applicant_id)
            ->latest()
            ->first();

        $totalScore = $assessment_->total_score ?? 0;
        $performanceRatingFromTest = $assessment->performance_rating ?? 'N/A';

        // Determine strongest category (Strength, Ability, Knowledge)
        $scores = [
            'Strength'  => $assessment->strength_score ?? 0,
            'Ability'   => $assessment->ability_score ?? 0,
            'Knowledge' => $assessment->knowledge_score ?? 0,
        ];
        $maxScore = max($scores);
        $topCategories = array_keys($scores, $maxScore);

        // Mapping categories → positions
        $categoryToPositions = [
            'Strength'  => ['Helper'],
            'Ability'   => ['Assistant Technician', 'Technician'],
            'Knowledge' => ['Human Resource Manager', 'Administrative Manager', 'Finance Manager'],
        ];

        $mappedPositions = [];
        foreach ($topCategories as $cat) {
            if (isset($categoryToPositions[$cat])) {
                $mappedPositions = array_merge($mappedPositions, $categoryToPositions[$cat]);
            }
        }

        // 🔹 Decide capability
        $positionName = $applicant->position ?? 'N/A';
        if (in_array($positionName, $mappedPositions)) {
            $capabilityResult = "Capable in {$positionName}";
        } else {
            if (count($mappedPositions) > 1) {
                $capabilityResult = "Capable in " . implode(' and ', $mappedPositions);
            } else {
                $capabilityResult = "Not capable in {$positionName}";
            }
        }

        $position  = strtolower($applicant->position ?? '');
        $skills    = strtolower($applicant->skills ?? '');
        $objective = strtolower($applicant->career_objective ?? '');
        $text      = $skills . ' ' . $objective;

        // Position skills mapping
        $positionSkills = [
            'helper' => ['basic tools', 'cleaning', 'manual labor', 'support'],
            'assistant technician' => ['wiring', 'maintenance', 'basic repair', 'installation'],
            'technician' => ['diagnostics', 'air-conditioning', 'electrical', 'troubleshooting', 'hvac'],
            'human resource manager' => ['recruitment', 'training', 'employee relations', 'hr management'],
            'administrative manager' => ['organization', 'scheduling', 'documentation', 'office management'],
            'finance manager' => ['accounting', 'budgeting', 'financial analysis', 'payroll', 'auditing'],
        ];

        // Position objectives mapping
        $positionObjectives = [
            'helper' => [
                'assist', 'help', 'learn', 'cleanliness', 'safety',
                'tools', 'equipment', 'support', 'manual', 'labor',
                'response', 'downtime', 'experience', 'professionalism',
                'teamwork', 'maintenance', 'organization', 'discipline'
            ],
            'assistant technician' => [
                'support', 'help', 'repair', 'fix', 'tools',
                'testing', 'installation', 'setup', 'wiring', 'maintenance',
                'document', 'record', 'communication', 'coordination',
                'training', 'learning', 'safety', 'trust', 'accuracy',
                'technical', 'inspection'
            ],
            'technician' => [
                'diagnose', 'troubleshoot', 'analyze', 'repair', 'fix',
                'installation', 'install', 'maintenance', 'service', 'calibration',
                'estimates', 'cost', 'quality', 'customer', 'client',
                'satisfaction', 'training', 'guide', 'safety', 'precaution',
                'electrical', 'hvac', 'air-conditioning', 'systems', 'technical'
            ],
            'human resource manager' => [
                'recruitment', 'hiring', 'selection', 'onboarding',
                'compliance', 'laws', 'policy', 'payroll', 'salary',
                'culture', 'grievances', 'disputes', 'performance',
                'records', 'documentation', 'career', 'growth',
                'retention', 'employee', 'relations', 'motivation',
                'training', 'development', 'evaluation', 'management'
            ],
            'administrative manager' => [
                'operations', 'organization', 'scheduling', 'planning',
                'records', 'documentation', 'communication', 'coordination',
                'inventory', 'supplies', 'workflow', 'process',
                'inquiries', 'policy', 'procedures', 'supervision',
                'compliance', 'support', 'office', 'efficiency',
                'administration', 'monitoring'
            ],
            'finance manager' => [
                'budgets', 'income', 'expenses', 'payroll', 'salaries',
                'profitability', 'records', 'accounting', 'finance',
                'decision-making', 'reporting', 'compliance', 'auditing',
                'expenditures', 'funds', 'assets', 'control',
                'advising', 'forecasting', 'investments', 'safeguard',
                'analysis', 'planning'
            ],
        ];

        $ratingScore = 0;
        $matchedSkills = [];
        $matchedObjectives = [];

        // Rule 1: Match position skills
        if (array_key_exists($position, $positionSkills)) {
            foreach ($positionSkills[$position] as $req) {
                if ($this->textContains($text, $req)) {
                    $matchedSkills[] = ucfirst($req);
                }
            }
            $matches = count($matchedSkills);

            if ($matches >= 3) {
                $ratingScore += 2;
            } elseif ($matches >= 1) {
                $ratingScore += 1;
            }
        }

        // Rule 2: Match position objectives
        if (array_key_exists($position, $positionObjectives)) {
            foreach ($positionObjectives[$position] as $obj) {
                if ($this->textContains($text, $obj)) {
                    $matchedObjectives[] = ucfirst($obj);
                }
            }
            $objMatches = count($matchedObjectives);

            if ($objMatches >= 5) {
                $ratingScore += 2;
            } elseif ($objMatches >= 2) {
                $ratingScore += 1;
            }
        }

        // Rule 3: Document bonus points
        $goodMoral = $applicant->good_moral_file;
        $coe = $applicant->coe_file;
        $resume = $applicant->resume_file;

        if ($goodMoral && $coe && $resume) {
            $ratingScore += 3;
        } elseif (($goodMoral && $coe) || ($goodMoral && $resume) || ($coe && $resume)) {
            $ratingScore += 2;
        } elseif ($goodMoral || $coe || $resume) {
            $ratingScore += 1;
        }

        // Final rating
        if ($ratingScore >= 5) {
            $finalRating = 'High';
        } elseif ($ratingScore >= 3) {
            $finalRating = 'Average';
        } else {
            $finalRating = 'Low';
        }

        // Save summary (guarded by earlier check to avoid duplicates)
        $newSummary = ApplicantSummary::create([
            'applicant_id'             => $applicant->applicant_id,
            'performance_rating'       => $finalRating,
            'total_score'              => $totalScore,
            'capability_result'        => $capabilityResult,
            'good_moral_file'          => $goodMoral,
            'coe_file'                 => $coe,
            'resume_file'              => $resume,
            'skills'                   => $applicant->skills ?? 'Not provided',
            'achievements'             => $applicant->achievements ?? 'Not provided',
            'career_objective'         => $applicant->career_objective ?? 'Not provided',
            'position'                 => $positionName,
            'matched_skills'           => json_encode($matchedSkills),
            'matched_career_objective' => json_encode($matchedObjectives),
        ]);

        // Update status (keeps your original behavior)
        $oldStatus = $applicant->applicant_status;
        $applicant->applicant_status = 'On Screening';
        $applicant->save();

        if ($oldStatus !== $applicant->applicant_status) {
            $applicant->notify(new StatusChangedNotification($applicant->applicant_status));
        }

        return redirect()->route('applicants.summary.show', [
        'applicant_summary_id' => $newSummary->applicant_summary_id,
    ])->with('success', 'Applicant summarized successfully.');
    }   

    /**
     * Search/normalize text check (same logic you provided)
     */
    private function textContains($text, $keyword)
    {
        $text = strtolower($text);
        $keyword = strtolower($keyword);

        // Quick direct match
        if (strpos($text, $keyword) !== false) {
            return true;
        }

        // Handle plural/singular
        if (substr($keyword, -1) === 's') {
            $singular = rtrim($keyword, 's');
            if (strpos($text, $singular) !== false) {
                return true;
            }
        } else {
            $plural = $keyword . 's';
            if (strpos($text, $plural) !== false) {
                return true;
            }
        }

        // Handle common word endings (expanded)
        $altEndings = [
            'ing'    => '',    // accounting ↔ account
            'ion'    => '',    // supervision ↔ supervise
            'ics'    => 'ic',  // diagnostics ↔ diagnostic
            'al'     => '',    // technical ↔ technic
            'ation'  => 'ate', // evaluation ↔ evaluate
            'ies'    => 'y',   // policies ↔ policy
            'ment'   => '',    // management ↔ manage
            'er'     => '',    // recruiter ↔ recruit
            'ors'    => 'or',  // advisors ↔ advisor
        ];

        foreach ($altEndings as $end => $replace) {
            if (substr($keyword, -strlen($end)) === $end) {
                $alt = substr($keyword, 0, -strlen($end)) . $replace;
                if (strpos($text, $alt) !== false) {
                    return true;
                }
            }
        }

        // Synonym handling
        $synonyms = [
            'assist'     => ['help', 'support', 'aid'],
            'prepare'    => ['ready', 'setup', 'arrange'],
            'technician' => ['tech', 'specialist'],
            'diagnose'   => ['identify', 'analyze', 'check'],
            'repair'     => ['fix', 'mend', 'restore'],
            'maintain'   => ['keep', 'service', 'preserve'],
            'manage'     => ['handle', 'supervise', 'oversee'],
            'train'      => ['teach', 'coach', 'instruct'],
            'document'   => ['record', 'note', 'log'],
            'communication' => ['interaction', 'correspondence'],
            'budget'     => ['expenses', 'funds', 'finance'],
            'audit'      => ['review', 'inspect', 'examine'],
            'salary'     => ['wage', 'compensation'],
            'employee'   => ['staff', 'worker', 'personnel'],
            'relations'  => ['relationship', 'connection'],
            'evaluation' => ['assess', 'evaluate', 'review'],
            'training'   => ['development', 'coaching'],
        ];

        foreach ($synonyms as $word => $list) {
            if (strpos($keyword, $word) !== false) {
                foreach ($list as $alt) {
                    if (strpos($text, str_replace($word, $alt, $keyword)) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Show summary - same as your implementation
     */
    public function showSummary($applicant_summary_id)
    {
        $summary = ApplicantSummary::with('applicant')->findOrFail($applicant_summary_id);

        $assessmentResult = DB::table('assessment_results')
            ->where('applicant_id', $summary->applicant_id)
            ->select('total_score', 'performance_rating')
            ->first();

        $testTotalScore = $assessmentResult->total_score ?? 'N/A';
        $testRating     = $assessmentResult->performance_rating ?? 'N/A';

        return view('HR.summary', [
            'summary'        => $summary,
            'testRating'     => $testRating,
            'testTotalScore' => $testTotalScore,
            'capability'     => $summary->capability_result,
        ]);
    }

 public function review($applicant_id)
    {
        $applicant = Applicant::where('applicant_id', $applicant_id)->firstOrFail();
        $summary = ApplicantSummary::where('applicant_id', $applicant_id)->first();

        return view('HR.reviewdocument', compact('applicant', 'summary'));
    }

    
  public function markReviewed($applicant_id)
{
    $applicant = Applicant::where('applicant_id', $applicant_id)->firstOrFail();
    $oldStatus = $applicant->applicant_status;

    $applicant->applicant_status = 'Reviewed';
    $applicant->save();

    // ✅ Send email only if status changed
    if ($oldStatus !== $applicant->applicant_status) {
        Mail::to($applicant->email)->send(
            new ApplicantStatusChanged($applicant, $applicant->applicant_status)
        );
    }

    return redirect()->route('review.document', $applicant_id)
                     ->with('success', 'Applicant marked as Reviewed and email sent.');
}

 public function markScheduledInterview(Request $request, $applicant_id)
{
    $request->validate([
        'interview_date' => 'required|date',
        'interview_time' => 'required',
        'location'       => 'required|string|max:255',
        'hr_manager'     => 'required|string|max:255',
    ]);

    $applicant = Applicant::where('applicant_id', $applicant_id)->firstOrFail();
    $oldStatus = $applicant->applicant_status;

    // ✅ Save to interviews table
    $interview = new Interview();
    $interview->applicant_id   = $applicant->applicant_id;
    $interview->interview_date = $request->interview_date;
    $interview->interview_time = $request->interview_time;
    $interview->location       = $request->location;
    $interview->hr_manager     = $request->hr_manager;
    $interview->save();

    // ✅ Update applicant status
    $applicant->applicant_status = 'Scheduled Interview';
    $applicant->save();

    // ✅ Send notification only if status changed
    if ($oldStatus !== $applicant->applicant_status) {
        $applicant->notify(new InterviewScheduledNotification($interview));
    }

    return redirect()
        ->route('show.listapplicants')
        ->with('success', 'Interview scheduled successfully.');
}

public function viewResume($applicant_id)
{
    $applicant = Applicant::findOrFail($applicant_id);

    if (!$applicant->resume_file) {
        abort(404, 'No resume found for this applicant.');
    }

    $filePath = storage_path('app/public/' . $applicant->resume_file);

    if (!file_exists($filePath)) {
        abort(404, 'Resume file not found.');
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // If DOC/DOCX → convert to PDF
    if (in_array($extension, ['doc', 'docx'])) {
        $pdfPath = storage_path('app/public/temp/resume_' . $applicant_id . '.pdf');

        // Ensure temp directory exists
        if (!is_dir(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0777, true);
        }

        // ⚠ PhpWord requires an external PDF renderer (TCPDF/mPDF/DomPDF)
        $phpWord = IOFactory::load($filePath);
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfPath);

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // If already PDF
    if ($extension === 'pdf') {
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // If image or other file (jpg, png, etc.)
    return response()->file($filePath);
}

public function markScreening($applicant_id)
{
    // Update applicant
    $applicant = Applicant::findOrFail($applicant_id);
    $applicant->applicant_status = 'On Screening';
    $applicant->save();

    // Update assessment_result for this applicant
    $result = DB::table('assessment_results')
        ->where('applicant_id', $applicant_id)
        ->latest('created_at')
        ->first();

    if ($result) {
        DB::table('assessment_results')
            ->where('assessment_result_id', $result->assessment_result_id)
            ->update(['assessment_result_status' => 'Result Viewed']);
    }

    return redirect()->route('show.listapplicants')
        ->with('success', 'Applicant moved to On Screening and result marked as viewed.');
}

public function markPassed(Request $request, $id)
{
    $applicant = Applicant::findOrFail($id);

    // Update status
    $applicant->applicant_status = "Passed Screening";
    $applicant->save();

    // Send notification
    $applicant->notify(new ScreeningPassed());

    return response()->json(['success' => true]);
}

public function markFailed($id)
{
    $applicant = Applicant::findOrFail($id);
    $applicant->applicant_status = "Failed Screening";
    $applicant->save();

    // Send Gmail apology
    $applicant->notify(new ApplicantFailedNotification());

    return response()->json(['success' => true]);
}

public function deleteApplicant($id)
{
    $applicant = Applicant::with('interviews')->findOrFail($id);

    if (!in_array($applicant->applicant_status, ['Rejected', 'Failed Screening']) 
        && (!isset($applicant->interviews) || $applicant->interviews->status !== 'Unattended')) {
        return redirect()->back()->with('error', 'Only applicants with status "Rejected", "Unattended", or "Failed Screening" can be deleted.');
    }

    $applicant->delete();

    return redirect()->route('show.listapplicants')
                     ->with('success', 'Applicant deleted successfully.');
}

public function updateStatus($applicantId, $status)
{
    $applicant = Applicant::findOrFail($applicantId);

    // Update only applicant_status column
    $applicant->applicant_status = $status;
    $applicant->save();

   if ($status === 'Hired') {
    $applicant->notify(new ApplicantHiredNotification($applicant));
} elseif ($status === 'Rejected') {
    $applicant->notify(new ApplicantRejectedNotification($applicant));
}


    return back()->with('success', "Applicant status updated to {$status}");
}

}


