<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class StatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $applicant_status;

    /**
     * Create a new notification instance.
     *
     * @param string $applicant_status
     */
    public function __construct($applicant_status)
    {
        $this->applicant_status = $applicant_status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Application Status Update')
            ->greeting('Hello You Passed The ON SCREENING Stage!' . $notifiable->first_name . ' ' . $notifiable->last_name . ',')
            ->line('Your application status has been updated.')
            ->line(new HtmlString('<strong>Status:</strong> ' . $this->applicant_status))
            ->line('Thank you for applying! We will update you on the next steps.')
            ->salutation('– 3RS Airconditioning HR Management');
    }

    /**
     * Get the array representation of the notification (optional for database/other channels).
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'applicant_status' => $this->applicant_status,
        ];
    }
}
