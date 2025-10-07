<?php
// app/Notifications/SendEvaluationNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEvaluationNotification extends Notification
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
    $url = route('evaluation.questionnaire', $this->token);

    return (new MailMessage)
        ->subject('Monthly Evaluation for ' . $this->position)
        ->greeting('Hello ' . $notifiable->first_name . ',')
        ->line('It’s time for your monthly evaluation.')
        ->line('Click the button below to begin:')
        ->action('Start Evaluation', $url)
        ->line('This link will expire in 24 hours.');
}

}

