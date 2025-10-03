<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentResult;
use Illuminate\Support\Facades\DB;

class AssessmentResultController extends Controller
{
    /**
     * Display a listing of the assessment results.
     */
    public function showAssessmentResults()
    {
        // Fetch all assessment results with related applicant and assessment
        $results = AssessmentResult::with(['applicant', 'assessment'])
                    ->orderBy('submitted_at', 'desc')
                    ->get();

        // Pass results to the Blade view in the Assessment folder
        return view('Assessments.assessmentresult', compact('results'));
    }

    public function showResult($applicant_id)
{
    $result = DB::table('assessment_results')
        ->join('applicants', 'assessment_results.applicant_id', '=', 'applicants.applicant_id')
        ->join('assessments', 'assessment_results.assessment_id', '=', 'assessments.assessment_id')
        ->where('assessment_results.applicant_id', $applicant_id)
        ->select(
            'applicants.applicant_id',     // ✅ make sure applicant_id is included
            'applicants.first_name',
            'applicants.last_name',
            'assessments.position_name',
            'assessments.title',
            'assessment_results.ability_score',
            'assessment_results.knowledge_score',
            'assessment_results.strength_score',
            'assessment_results.total_score',
            'assessment_results.performance_rating'
        )
        ->first();

    if (!$result) {
        abort(404, 'Assessment result not found for this applicant.');
    }

    return view('Assessments.assessmentresultview', compact('result', 'applicant_id'));
}


}
