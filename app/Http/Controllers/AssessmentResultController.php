<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentResult;
use App\Models\EvaluationSummary;

class AssessmentResultController extends Controller
{
    /**
     * Show all assessment results and evaluation summaries
     */
    public function showAssessmentResults()
    {
        // Assessment Results (no pagination)
        $results = AssessmentResult::with(['applicant', 'assessment'])
                    ->orderBy('submitted_at', 'desc')
                    ->get();

        // Evaluation Summaries (paginated)
        $summaries = EvaluationSummary::with(['evaluator', 'evaluatee', 'assessment'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20); // ✅ Pagination added

        return view('Assessments.assessmentresult', compact('results', 'summaries'));
    }
}
