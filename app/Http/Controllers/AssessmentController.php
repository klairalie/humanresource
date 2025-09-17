<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AssessmentToken;
use App\Models\Applicant;
use App\Models\AssessmentQuestion;

class AssessmentController extends Controller
{
    public function create()
    {
        return view('Assessments.createassessment');
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|in:Helper,Assistant Technician,Technician,Human Resource Manager,Administrative Manager,Finance Manager',
            'title'         => 'required|string',
            'description' => 'nullable|string',
        ]);


        Assessment::create([
            'position_name' => $request->position_name,
            'title' => $request->title,
            'description' => $request->description,
        ]);


        return redirect()->back()->with('success', 'Assessment added successfully!');
    }

public function showQuestionnaire($token)
{
    // Validate token
    $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();

    // Check if already used
    if ($tokenRecord->used_at) {
        return redirect()->route('assessment.result', $token)
            ->with('error', 'This assessment has already been submitted.');
    }

    // Check if expired (3 hours after creation)
    if ($tokenRecord->created_at->addHours(3)->isPast()) {
        abort(403, 'This assessment link has expired.');
    }

    $applicant = Applicant::findOrFail($tokenRecord->applicant_id);
    $assessment = Assessment::findOrFail($tokenRecord->assessment_id);

    $questions = AssessmentQuestion::where('assessment_id', $assessment->assessment_id)->get();

    return view('Assessments.assessmentquestionnaire', compact('assessment', 'questions', 'applicant', 'token'));
}


public function storeQuestion(Request $request, $token)
{
    // Validate token again
    $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();
    $applicant   = Applicant::findOrFail($tokenRecord->applicant_id);
    $assessment  = Assessment::findOrFail($tokenRecord->assessment_id);

    $questions = AssessmentQuestion::where('assessment_id', $assessment->assessment_id)->get();

    $abilityScore   = 0;
    $knowledgeScore = 0;
    $strengthScore  = 0;

    foreach ($questions as $question) {
        // Retrieve applicant’s submitted answer
        $answer = $request->input("question_{$question->assessment_question_id}");

        // Normalize values to uppercase for safe comparison
        if ($answer && strtoupper($answer) === strtoupper($question->correct_answer)) {
            switch ($question->level) {
                case 'ability':
                    $abilityScore++;
                    break;
                case 'knowledge':
                    $knowledgeScore++;
                    break;
                case 'strength':
                    $strengthScore++;
                    break;
            }
        }
    }

    $totalScore = $abilityScore + $knowledgeScore + $strengthScore;

    // Rating logic
    if ($totalScore >= 8) {
        $performanceRating = 'High';
    } elseif ($totalScore >= 5) {
        $performanceRating = 'Average';
    } else {
        $performanceRating = 'Low';
    }

    // Store result
    AssessmentResult::create([
        'applicant_id'       => $applicant->applicant_id,
        'assessment_id'      => $assessment->assessment_id,
        'ability_score'      => $abilityScore,
        'knowledge_score'    => $knowledgeScore,
        'strength_score'     => $strengthScore,
        'total_score'        => $totalScore,
        'performance_rating' => $performanceRating,
        'submitted_at'       => now(),
    ]);

    // Mark token as used
    $tokenRecord->update(['used_at' => now()]);

    return redirect()->route('assessment.result', $token)
        ->with('success', 'Assessment submitted successfully!');
}

    
public function showResult($token)
{
    // Just validate the token exists (and was used)
    $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();

    return view('Assessments.result', compact('tokenRecord'));

}

}
