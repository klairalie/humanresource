<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InterviewUnattendedNotification extends Notification
{
    use Queueable;

    protected $applicant;

    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Interview Attendance Notice')
            ->greeting('Hello ' . $this->applicant->first_name . ',')
            ->line('We noticed that you did not attend your scheduled interview.')
            ->line('Unfortunately, this makes your application invalid, and there will be no rescheduled interview.')
            ->line('Kind regards,')
            ->line('HR Department');
    }
}
