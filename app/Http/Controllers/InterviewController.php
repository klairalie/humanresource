<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Interview;
use Illuminate\Http\Request;
use App\Notifications\InterviewUnattendedNotification;
use App\Notifications\ApplicantHiredNotification;
use App\Notifications\ApplicantRejectedNotification;

class InterviewController extends Controller
{
    /**
     * Update interviews.status for Done / Unattended (and send unattended email).
     */
    public function updateStatus(Applicant $applicant, $status)
    {
        // find the latest interview record for this applicant
        $interview = Interview::where('applicant_id', $applicant->applicant_id)->latest('created_at')->first();

        if (! $interview) {
            return back()->with('error', 'Interview record not found.');
        }

        $interview->status = $status;
        $interview->save();

        if ($status === 'Unattended') {
            // send unattended email
            $applicant->notify(new InterviewUnattendedNotification($applicant));
        }

        return back()->with('success', "Interview status updated to {$status}.");
    }

    /**
     * Final decision (Hired / Rejected) — updates applicants.applicant_status only and sends email.
     */
    public function finalDecision(Applicant $applicant, $status)
    {
        if (! in_array($status, ['Hired', 'Rejected'])) {
            return back()->with('error', 'Invalid final status.');
        }

        $applicant->applicant_status = $status;
        $applicant->save();

        if ($status === 'Hired') {
            $applicant->notify(new ApplicantHiredNotification($applicant));
        } else { // Rejected
            $applicant->notify(new ApplicantRejectedNotification($applicant));
        }

        return back()->with('success', "Applicant marked as {$status}.");
    }
}
