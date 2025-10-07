<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Employeeprofiles;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationToken;
use App\Models\EvaluationResult;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Notifications\SendEvaluationNotification;
use App\Models\EvaluationSummary;
class EvaluationQuestionController extends Controller
{
    /**
     * Store submitted evaluation questions (admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'assessment_id'          => 'required|exists:assessments,assessment_id',
            'position'               => 'required|string|max:255',
            'questions'              => 'required|array',
            'questions.*.question'   => 'required|string',
            'questions.*.category'   => 'required|string|in:Work Performance & Skills,Teamwork & Collaboration,Professional Behavior,Safety & Responsibility',
        ]);

        foreach ($request->questions as $q) {
            EvaluationQuestion::create([
                'assessment_id' => $request->assessment_id,
                'position'      => $request->position,
                'category'      => $q['category'],
                'question'      => $q['question'],
            ]);
        }

        return redirect()->route('evaluation.create')
                         ->with('success', 'Evaluation questions saved successfully!');
    }

    /**
     * Show create question form
     */
    public function create()
    {
        $assessments = Assessment::where('title', 'Employee Evaluation')->get();
        $employees = Employeeprofiles::all();

        return view('Evaluation.createevaluation', compact('assessments', 'employees'));
    }

    /**
     * List questions + employee table
     */
    public function showEvaluation()
    {
        $questions = EvaluationQuestion::paginate(20);

        // ✅ Employees for peer evaluation table
        $employees = Employeeprofiles::whereIn('position', ['Helper','Technician','Assistant Technician'])->get();

        return view('Evaluation.viewevaluation', compact('questions', 'employees'));
    }

    /**
     * Bulk delete
     */
    public function deleteAll(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || count($ids) === 0) {
            return redirect()->route('evaluation.view')
                             ->with('error', 'No questions selected for deletion.');
        }

        EvaluationQuestion::whereIn('evaluation_question_id', $ids)->delete();

        return redirect()->route('evaluation.view')
                         ->with('success', 'Selected evaluation questions deleted successfully!');
    }

    /**
     * Update a single question
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = EvaluationQuestion::where('evaluation_question_id', $id)->firstOrFail();
        $question->update([
            'question' => $request->question,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('evaluation.view')->with('success', 'Question updated successfully!');
    }

    /**
     * HR: Send evaluation link via email
     */
    public function sendEvaluation(Request $request)
    {
        $request->validate([
            'evaluator_id'  => 'required|exists:employeeprofiles,employeeprofiles_id',
            'assessment_id' => 'required|exists:assessments,assessment_id',
            // optional → HR doesn’t assign evaluatee
            'evaluatee_id'  => 'nullable|exists:employeeprofiles,employeeprofiles_id',
        ]);

        $evaluator  = Employeeprofiles::findOrFail($request->evaluator_id);
        $assessment = Assessment::findOrFail($request->assessment_id);

        // generate token
        $token = Str::random(60);

        EvaluationToken::create([
            'evaluator_id'  => $evaluator->employeeprofiles_id,
            'evaluatee_id'  => $request->evaluatee_id ?: null,
            'assessment_id' => $assessment->assessment_id,
            'token'         => $token,
            'expires_at'    => now()->addHours(24),
            'is_used'       => false,
        ]);

        // send email
        $evaluator->notify(new SendEvaluationNotification($token, $assessment->position_name));

        return back()->with('success', 'Evaluation link sent to ' . $evaluator->email);
    }

    /**
     * Evaluator opens token link
     */
    public function showEvaluationQuestionnaire($token)
    {
        $tokenRecord = EvaluationToken::where('token', $token)->firstOrFail();

        if ($tokenRecord->is_used) {
            return redirect()->route('evaluate.used')->with('error', 'This evaluation link has already been used.');
        }

        if ($tokenRecord->expires_at->isPast()) {
            return redirect()->route('evaluate.expired')->with('error', 'This evaluation link has expired.');
        }

        $evaluator = Employeeprofiles::findOrFail($tokenRecord->evaluator_id);
        $evaluatee = $tokenRecord->evaluatee_id
            ? Employeeprofiles::find($tokenRecord->evaluatee_id)
            : null;

        $assessment = Assessment::findOrFail($tokenRecord->assessment_id);
        $questions  = EvaluationQuestion::where('assessment_id', $assessment->assessment_id)->get();

        // Eligible employees: not self, not same position
        $eligibleEmployees = Employeeprofiles::where('employeeprofiles_id', '!=', $evaluator->employeeprofiles_id)
            ->where('position', '!=', $evaluator->position)
            ->whereIn('position', ['Helper','Technician','Assistant Technician'])
            ->get();

        return view('Evaluation.evaluationquestionnaire', [
            'assessment' => $assessment,
            'questions'  => $questions,
            'evaluator'  => $evaluator,
            'evaluatee'  => $evaluatee,
            'token'      => $token,
            'employees'  => $eligibleEmployees,
        ]);
    }

    /**
     * Save evaluation submission
     */


public function submitEvaluation(Request $request, $token)
{
    // Find the token record
    $tokenRecord = EvaluationToken::where('token', $token)->first();
    if (!$tokenRecord) {
        return response()->json(['error' => 'Invalid token'], 400);
    }

    if ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast()) {
        return response()->json(['error' => 'Expired link'], 410);
    }

    // Get evaluator
    $evaluator = Employeeprofiles::find($tokenRecord->evaluator_id);
    if (!$evaluator) {
        return response()->json(['error' => 'Evaluator not found'], 404);
    }

    // Determine allowed positions
    $allowedPositions = match ($evaluator->position) {
        'Helper'               => ['Technician', 'Assistant Technician'],
        'Technician'           => ['Helper', 'Assistant Technician'],
        'Assistant Technician' => ['Helper', 'Technician'],
        default                => [],
    };

    // Get allowed employee IDs
    $allowedEvaluateeIds = Employeeprofiles::where('employeeprofiles_id', '!=', $evaluator->employeeprofiles_id)
        ->whereIn('position', $allowedPositions)
        ->pluck('employeeprofiles_id')
        ->toArray();

    // Validate request
    $validated = $request->validate([
        'evaluatee_id' => ['required', Rule::in($allowedEvaluateeIds)],
        'answers'      => 'required|array',
        'answers.*'    => 'required|integer|min:1|max:5',
        'feedback'     => 'nullable|string|max:2000',
    ]);

    $evaluatee = Employeeprofiles::find($validated['evaluatee_id']);

    // Prevent duplicate evaluation
    $alreadyEvaluated = EvaluationSummary::where('evaluator_id', $evaluator->employeeprofiles_id)
        ->where('evaluatee_id', $evaluatee->employeeprofiles_id)
        ->where('assessment_id', $tokenRecord->assessment_id)
        ->exists();

    if ($alreadyEvaluated) {
        return response()->json(['error' => 'You already evaluated this employee.'], 409);
    }

    // Save answers and compute category totals
    $categoryTotals = [];

    foreach ($validated['answers'] as $questionId => $rating) {
        $question = EvaluationQuestion::find($questionId);
        if (!$question) continue;

        EvaluationResult::create([
            'evaluator_id'  => $evaluator->employeeprofiles_id,
            'evaluatee_id'  => $evaluatee->employeeprofiles_id,
            'assessment_id' => $tokenRecord->assessment_id,
            'question_id'   => $questionId,
            'rating'        => $rating,
            'feedback'      => $validated['feedback'] ?? null,
        ]);

        // Sum ratings per category
        $cat = $question->category ?? 'Uncategorized';
        $categoryTotals[$cat] = ($categoryTotals[$cat] ?? 0) + $rating;
    }

    // Category scores = raw total per category
    $categoryScores = $categoryTotals;

    // Overall total score = sum of all category totals
    $totalScore = array_sum($categoryTotals);

    // Save evaluation summary
    EvaluationSummary::create([
        'evaluator_id'    => $evaluator->employeeprofiles_id,
        'evaluatee_id'    => $evaluatee->employeeprofiles_id,
        'assessment_id'   => $tokenRecord->assessment_id,
        'total_score'     => $totalScore,
        'category_scores' => json_encode($categoryScores),
        'feedback'        => $validated['feedback'] ?? null,
    ]);

    // Determine remaining employees
    $evaluatedIds = EvaluationSummary::where('evaluator_id', $evaluator->employeeprofiles_id)
        ->where('assessment_id', $tokenRecord->assessment_id)
        ->pluck('evaluatee_id')
        ->toArray();

    $remaining = array_diff($allowedEvaluateeIds, $evaluatedIds);

    // Mark token as used if all done
    if (empty($remaining)) {
        $tokenRecord->update(['is_used' => true]);
    }

    return response()->json([
        'success'   => true,
        'remaining' => count($remaining),
        'message'   => empty($remaining)
            ? 'All evaluations are completed. Thank you!'
            : 'Evaluation submitted successfully. You can still evaluate other employees.'
    ]);
}


public function showExpired()
{
    return view('Evaluation.expired');
}
    public function showUsed()
    {
        return view('Evaluation.used');
    }

    public function showThankYou()
    {
        return view('Evaluation.thankyou');
    }
    public function showAlreadyDone()
    {
        return view('Evaluation.alreadydone');
    }


    /**
     * HR view for sending evaluations
     */
    public function sendEvalView()
    {
        $assessments = Assessment::all();
        $employees   = Employeeprofiles::whereIn('position', ['Helper', 'Technician', 'Assistant Technician'])->get();

        return view('Evaluation.sendevaluation', compact('employees', 'assessments'));
    }
}
