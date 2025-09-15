<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendAssessmentNotification;
use Carbon\Carbon;
use App\Models\AssessmentToken;
use App\Models\Applicant;
use Illuminate\Support\Str;
class AssessmentTokenController extends Controller
{
     public function sendAssessment($applicant_id, $assessment_id)
{
    // 1. Find the applicant
    $applicant = Applicant::findOrFail($applicant_id);

    // 2. Generate a unique token
    $token = Str::random(32);

    // 3. Save token to assessment_tokens table
    $tokenRecord = AssessmentToken::create([
        'applicant_id'  => $applicant->applicant_id, // make sure this is the correct PK
        'assessment_id' => $assessment_id,
        'token'         => $token,
        'expires_at'    => Carbon::now()->addHours(3), // token valid for 3 hours
        'is_used'       => false,
    ]);

    // 4. Send Notification (Email) with token
    $applicant->notify(new SendAssessmentNotification($tokenRecord->token, $applicant->position));

    // 5. Update applicant status
    $applicant->update([
        'applicant_status' => 'On Screening',
    ]);

    // 6. Return back with success message
    return back()->with('success', 'Assessment link sent to applicant.');
}


// public function showAssessment($token)
// {
//     $record = AssessmentToken::where('token', $token)->first();

//     if (!$record) {
//         abort(404, 'Invalid or missing assessment token.');
//     }

//     if (now()->greaterThan($record->expires_at)) {
//         abort(403, 'This assessment link has expired.');
//     }

//     if ($record->is_used) {
//         abort(403, 'This assessment link has already been used.');
//     }

//     $applicant = $record->applicant;

//     return view('assessments.take', [
//         'token'     => $record->token,
//         'applicant' => $applicant,
//         'position'  => $applicant->position,
//     ]);
// }


 }
