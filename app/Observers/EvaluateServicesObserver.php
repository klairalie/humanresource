<?php

namespace App\Observers;

use App\Models\ServiceRequestItem;
use App\Models\ActivityLog;
use Carbon\Carbon;

class EvaluateServicesObserver
{
    /**
     * Handle the "updated" event for ServiceRequestItem.
     */
    public function updated(ServiceRequestItem $service)
    {
        $actorEmail = session('user_email') ?? 'user@example.com';

        // ✅ Check if status has changed
        if ($service->isDirty('status')) {
            $oldStatus = $service->getOriginal('status');
            $newStatus = $service->status;

            // ✅ Status messages for logs
            $statusMessages = [
                'Pending'      => 'Service is pending evaluation.',
                'In Progress'  => 'Service is currently in progress.',
                'Completed'    => 'Service has been completed successfully.',
                'Cancelled'    => 'Service was cancelled.',
                'Rescheduled'  => 'Service has been rescheduled.',
                'Failed'       => 'Service failed to complete.',
            ];

            $message = $statusMessages[$newStatus] ?? "Service status changed from '{$oldStatus}' to '{$newStatus}'.";

            // ✅ Record the change in the activity logs
            ActivityLog::create([
                'action_type' => 'Service Status Updated',
                'email' => $actorEmail,
                'description' => "Service Request Item ID: {$service->item_id} — {$message}",
                'action_date' => Carbon::now(),
            ]);
        }
    }

    /**
     * Handle the "created" event for ServiceRequestItem.
     */
    public function created(ServiceRequestItem $service)
    {
        $actorEmail = session('user_email') ?? 'user@example.com';

        ActivityLog::create([
            'action_type' => 'New Service Request Recorded',
            'email' => $actorEmail,
            'description' => "Created new service request item (Type: {$service->service_type}, Quantity: {$service->quantity}).",
            'action_date' => Carbon::now(),
        ]);
    }
}
