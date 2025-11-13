<?php

namespace App\Observers;

use App\Models\Applicant;
use App\Models\ActivityLog;

class ApplicantObserver
{
    public function created(Applicant $applicant)
    {
        $actorEmail = session('user_email') ?? 'user@example.com'; // Permanently stored in DB

        ActivityLog::create([
            'action_type' => 'New Applicant Submitted',
            'email' => $actorEmail,
            'description' => "New applicant submitted: {$applicant->first_name} {$applicant->last_name} ({$applicant->position})",
        ]);
    }

    public function updated(Applicant $applicant)
    {
        $actorEmail = session('user_email') ?? 'user@example.com'; // Permanently stored in DB

        // ✅ Detect if applicant_status was changed
        if ($applicant->isDirty('applicant_status')) {
            $oldStatus = $applicant->getOriginal('applicant_status');
            $newStatus = $applicant->applicant_status;

            // Status messages synced with your blade/UI
            $statusMessages = [
                'Pending'               => 'Application submitted and awaiting screening.',
                'On Screening'          => 'Application moved to screening phase.',
                'Passed Screening'      => 'Applicant passed the screening stage.',
                'Reviewed'              => 'Applicant document reviewed.',
                'Scheduled Interview'   => 'Interview has been scheduled.',
                'Failed Screening'      => 'Applicant failed screening stage.',
                'Done'                  => 'Applicant has completed the process.',
                'Unattended'            => 'Applicant did not attend the interview.',
                'Hired'                 => 'Applicant has been hired.',
                'Rejected'              => 'Applicant was rejected after evaluation.',
                'Done Taking Assessment' => 'Applicant finished taking the assessment.',
            ];

            $statusMessage = $statusMessages[$newStatus] ?? "Status changed from '{$oldStatus}' to '{$newStatus}'.";

            ActivityLog::create([
                'action_type' => 'Applicant Status Updated',
                'email' => $actorEmail,
                'description' => "{$applicant->first_name} {$applicant->last_name}: {$statusMessage}",
            ]);
        } else {
            // ✅ Log other updated fields
            $changedFields = implode(', ', array_keys($applicant->getDirty()));

            ActivityLog::create([
                'action_type' => 'Applicant Information Updated',
                'email' => $actorEmail,
                'description' => "Updated applicant {$applicant->first_name} {$applicant->last_name}. Changed fields: {$changedFields}.",
            ]);
        }
    }

    public function deleted(Applicant $applicant)
    {
        $actorEmail = session('user_email') ?? 'user@example.com'; // Permanently stored in DB

        ActivityLog::create([
            'action_type' => 'Applicant Deleted',
            'email' => $actorEmail,
            'description' => "Deleted applicant record: {$applicant->first_name} {$applicant->last_name} ({$applicant->position})",
        ]);
    }
}
