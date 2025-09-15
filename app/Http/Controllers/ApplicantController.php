<?php

namespace App\Http\Controllers;



use App\Notifications\StatusChangedNotification;
use App\Mail\ApplicantStatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Models\Applicant;
use App\Models\ApplicantSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Assessment;

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



    public function index(Request $request)
    {
        $search = $request->input('search');
    $assessments = Assessment::all();

 
        $applicants = Applicant::when($search, function ($query, $search) {
            return $query->where('full_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('contact_number', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

          // Return view with both applicants and assessments
    return view('HR.applicantlist', compact('applicants', 'assessments'));
    }

    public function show($applicant_id)
    {
        $applicant = Applicant::findOrFail($applicant_id);
        return view('applicants.show', compact('applicant'));
    }



  public function summarize($id)
{
    $applicant = Applicant::findOrFail($id);

    $position = strtolower($applicant->position ?? '');
    $skills = strtolower($applicant->skills ?? '');
    $objective = strtolower($applicant->career_objective ?? '');
    $text = $skills . ' ' . $objective;

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
} elseif (
    ($goodMoral && $coe) ||
    ($goodMoral && $resume) ||
    ($coe && $resume)
) {
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

    // Save summary
    ApplicantSummary::create([
        'applicant_id'             => $applicant->applicant_id,
        'performance_rating'       => $finalRating,
        'good_moral_file'          => $goodMoral,
        'coe_file'                 => $coe,
        'resume_file'              => $resume,
        'skills'                   => $applicant->skills ?? 'Not provided',
        'achievements'             => $applicant->achievements ?? 'Not provided',
        'career_objective'         => $applicant->career_objective ?? 'Not provided',
        'position'                 => $applicant->position ?? 'N/A',
        'matched_skills'           => json_encode($matchedSkills),
        'matched_career_objective' => json_encode($matchedObjectives),
    ]);

    // Update status
    $oldStatus = $applicant->applicant_status;
    $applicant->applicant_status = 'On Screening';
    $applicant->save();

    // ✅ Send notification only if the status changed
    if ($oldStatus !== $applicant->applicant_status) {
        $applicant->notify(new StatusChangedNotification($applicant->applicant_status));
    }

    return redirect()->back()->with('success', 'Applicant summarized successfully, status updated, and email notification sent.');
}

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

    public function showSummary($applicant_summary_id)
    {
        $summary = ApplicantSummary::findOrFail($applicant_summary_id);
        return view('HR.summary', compact('summary'));
    }


    public function updateStatus(Request $request, $applicant_id)
{
    $applicant = Applicant::findOrFail($applicant_id);
    $oldStatus = $applicant->applicant_status;

    $applicant->applicant_status = $request->status;
    $applicant->save();

    // Only send email if status really changed
    if ($oldStatus !== $request->status) {
        Mail::to($applicant->email)->send(
            new ApplicantStatusChanged($applicant, $request->status)
        );
    }

    return back()->with('success', 'Applicant status updated and email sent.');
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




}


