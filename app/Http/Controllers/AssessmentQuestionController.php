<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use App\Models\Assessment;
use Illuminate\Http\Request;


class AssessmentQuestionController extends Controller
{
public function assessmentView()
{
    // Get all unique positions
    $positions = AssessmentQuestion::pluck('position')->unique();

    // Paginate questions per position
    $allQuestions = [];
    foreach ($positions as $pos) {
        $allQuestions[$pos] = AssessmentQuestion::where('position', $pos)
            ->paginate(10, ['*'], $pos.'_page'); 
            // custom page name so each table has its own independent pagination
    }

    return view('AssessmentQuestions.viewquestions', compact('positions', 'allQuestions'));
}



    public function create()
    {
            $assessments = Assessment::all(); 
        return view('AssessmentQuestions.createquestions', compact('assessments'));
    }

   public function store(Request $request)
{
    $request->validate([
        'position' => 'required|string',
        'assessment_id' => 'required|exists:assessments,assessment_id',
        'questions' => 'required|array',
        'questions.*.question' => 'required|string',
        'questions.*.option_a' => 'required|string',
        'questions.*.option_b' => 'required|string',
        'questions.*.option_c' => 'required|string',
        'questions.*.option_d' => 'required|string',
        'questions.*.correct_answer' => 'required|in:A,B,C,D',
    ]);

    $questions = $request->input('questions');

    // Predefined levels that repeat in order
    $levels = ['ability', 'knowledge', 'strength'];
    $questionsPerLevel = 10;

    foreach ($questions as $index => $q) {
        // Auto-assign level every 10 questions
        $levelIndex = floor($index / $questionsPerLevel) % count($levels);
        $level = $levels[$levelIndex];

        AssessmentQuestion::create([
            'position'       => $request->position,
            'assessment_id'  => $request->assessment_id,
            'level'          => $level,
            'question'       => $q['question'],
            'choice_a'       => $q['option_a'],
            'choice_b'       => $q['option_b'],
            'choice_c'       => $q['option_c'],
            'choice_d'       => $q['option_d'],
            'correct_answer' => $q['correct_answer'],
        ]);
    }

    return redirect()
        ->route('view.questions')
        ->with('success', 'Questions created successfully with auto-leveled groups!');
}




    public function edit(AssessmentQuestion $assessmentQuestion)
    {
       $assessments = Assessment::paginate(10); // 10 per page
        return view('AssessmentQuestions.edit', compact('assessmentQuestion', 'assessments'));
    }

    public function update(Request $request, AssessmentQuestion $assessmentQuestion)
{
    $request->validate([
        'question'   => 'required|string',
        'correct_answer'  => 'required|string',
    ]);

    $assessmentQuestion->update([
        'question'  => $request->question,
        'correct_answer' => $request->correct_answer,
    ]);

    return redirect()->route('view.questions')->with('success', 'Question updated successfully!');
}

    public function destroyAll()
{
    AssessmentQuestion::truncate(); // deletes all records
    return redirect()->route('view.questions')->with('success', 'All questions have been deleted successfully.');
}

}

