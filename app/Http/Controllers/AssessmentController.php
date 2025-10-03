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
        'title'         => 'required|string|max:255',
        'type'          => 'required|in:test,evaluation',
        'position_name' => 'required_if:type,test|nullable|in:Helper,Assistant Technician,Technician,Human Resource Manager,Administrative Manager,Finance Manager',
        'description'   => 'nullable|string',
    ]);

    Assessment::create([
        'title'         => $request->title,
        'type'          => $request->type,
        'position_name' => $request->position_name,
        'description'   => $request->description,
    ]);

    return redirect()->back()->with('success', 'Assessment added successfully!');
}

    public function showQuestionnaire($token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();

        if ($tokenRecord->used_at) {
            return redirect()->route('assessment.result', $token)
                ->with('error', 'This assessment has already been submitted.');
        }

        if ($tokenRecord->created_at->addHours(3)->isPast()) {
            abort(403, 'This assessment link has expired.');
        }

        $applicant  = Applicant::findOrFail($tokenRecord->applicant_id);
        $assessment = Assessment::findOrFail($tokenRecord->assessment_id);
        $questions  = AssessmentQuestion::where('assessment_id', $assessment->assessment_id)->get();

        return view('Assessments.assessmentquestionnaire', compact('assessment', 'questions', 'applicant', 'token'));
    }

    public function storeQuestion(Request $request, $token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();
        $applicant   = Applicant::findOrFail($tokenRecord->applicant_id);
        $assessment  = Assessment::findOrFail($tokenRecord->assessment_id);
        $questions   = AssessmentQuestion::where('assessment_id', $assessment->assessment_id)->get();

        if ($assessment->type === 'test') {
            // ---------------- Test logic (with correct answers) ----------------
            $abilityScore   = 0;
            $knowledgeScore = 0;
            $strengthScore  = 0;

            foreach ($questions as $question) {
                $answer = $request->input("question_{$question->assessment_question_id}");

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

            if ($totalScore >= 24) {
                $performanceRating = 'High';
            } elseif ($totalScore >= 20) {
                $performanceRating = 'Average';
            } else {
                $performanceRating = 'Low';
            }

        } else {
            // ---------------- Evaluation logic (scale 1–5) ----------------
            $ratings = [];
            foreach ($questions as $question) {
                $ratings[] = (int) $request->input("question_{$question->assessment_question_id}");
            }

            $average = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;

            if ($average >= 4.5) {
                $performanceRating = 'Excellent';
            } elseif ($average >= 3.5) {
                $performanceRating = 'Very Good';
            } elseif ($average >= 2.5) {
                $performanceRating = 'Good';
            } elseif ($average >= 1.5) {
                $performanceRating = 'Fair';
            } else {
                $performanceRating = 'Poor';
            }

            // For evaluation, we store average instead of test breakdown
            $abilityScore   = null;
            $knowledgeScore = null;
            $strengthScore  = null;
            $totalScore     = $average; 
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

        $tokenRecord->update(['used_at' => now()]);

        return redirect()->route('assessment.result', $token)
            ->with('success', 'Assessment submitted successfully!');
    }

    public function showResult($token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->firstOrFail();
        return view('Assessments.result', compact('tokenRecord'));
    }
}
