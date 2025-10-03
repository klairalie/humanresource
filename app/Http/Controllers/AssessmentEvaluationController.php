<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentToken;
use App\Models\Employeeprofiles;
use App\Models\AssessmentEvaluationQuestion;
use App\Models\AssessmentEvaluationResult;

class AssessmentEvaluationController extends Controller
{
    /**
     * Show the evaluation questionnaire page
     */
    public function showEvaluation($token)
    {
        // Validate token
        $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();

        if ($tokenRecord->used_at) {
            return redirect()->route('evaluation.result', $token)
                ->with('error', 'This evaluation has already been submitted.');
        }

        if ($tokenRecord->created_at->addHours(3)->isPast()) {
            abort(403, 'This evaluation link has expired.');
        }

        $employeeEvaluator = Employeeprofiles::findOrFail($tokenRecord->employee_id); // evaluator
        $assessment        = Assessment::findOrFail($tokenRecord->assessment_id);

        $questions = AssessmentEvaluationQuestion::where('assessment_id', $assessment->assessment_id)->get();

        return view('Evaluations.evaluationquestionnaire', compact('assessment', 'questions', 'employeeEvaluator', 'token'));
    }

    /**
     * Store submitted evaluation
     */
    public function storeEvaluation(Request $request, $token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();
        $assessment  = Assessment::findOrFail($tokenRecord->assessment_id);

        $questions = AssessmentEvaluationQuestion::where('assessment_id', $assessment->assessment_id)->get();

        foreach ($questions as $question) {
            $rating = $request->input("question_{$question->assessment_evaluation_question_id}");

            if ($rating) {
                AssessmentEvaluationResult::create([
                    'assessment_id'                     => $assessment->assessment_id,
                    'assessment_evaluation_question_id' => $question->assessment_evaluation_question_id,
                    'employeeprofiles_id'                       => $tokenRecord->employeeprofiles_id, // evaluator
                    'rated_employeeprofiles_id'                 => $request->rated_employeeprofiles_id, // evaluatee
                    'rating'                            => $rating,
                ]);
            }
        }

        // Mark token as used
        $tokenRecord->update(['used_at' => now()]);

        return redirect()->route('evaluation.result', $token)
            ->with('success', 'Evaluation submitted successfully!');
    }

    /**
     * Show evaluation result confirmation
     */
    public function showEvaluationResult($token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();

        return view('Evaluations.result', compact('tokenRecord'));
    }
}
