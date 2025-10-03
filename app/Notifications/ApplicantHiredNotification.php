<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicantHiredNotification extends Notification
{
    use Queueable;

    protected $applicant;

    public function __construct($applicant)
    {
        $this->applicant = $applicant;
    }

    public function via($notifiable)
    {
        return ['mail']; // you can also add 'database' if you want
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Congratulations! You are Hired')
                    ->line("Dear {$this->applicant->first_name},")
                    ->line('You have been selected for the position.')
                    ->line('Thank you for applying!');
    }
}
