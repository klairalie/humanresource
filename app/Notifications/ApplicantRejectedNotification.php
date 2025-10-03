<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicantRejectedNotification extends Notification
{
    use Queueable;

    protected $applicant;

    /**
     * Create a new notification instance.
     */
    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail']; // Can also add 'database' if needed
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Update on Your Application')
                    ->greeting("Hello {$this->applicant->first_name},")
                    ->line('Thank you for taking the time to interview for the position.')
                    ->line('After careful consideration, we regret to inform you that you were not selected for this role.')
                    ->line('We appreciate your interest in joining our team and wish you the best in your future endeavors.')
                    ->salutation('Sincerely, Human Resource Team');
    }
}
