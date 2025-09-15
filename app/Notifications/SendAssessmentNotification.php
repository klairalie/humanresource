<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAssessmentNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $position;

    public function __construct($token, $position)
    {
        $this->token = $token;
        $this->position = $position;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Use APP_URL from .env
        $url = config('app.url') . '/assessment/start/' . $this->token;

        return (new MailMessage)
            ->subject('Assessment Test for ' . $this->position)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('You have been shortlisted for the position of ' . $this->position . '.')
            ->line('Please take the assessment test by clicking the button below:')
            ->action('Start Assessment', $url)
            ->line('This link will expire in 3 hours.');
    }
}
