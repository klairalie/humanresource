<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendEvaluationNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $evaluateeName;

    public function __construct($token, $evaluateeName)
    {
        $this->token = $token;
        $this->evaluateeName = $evaluateeName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Employee Evaluation Request')
            ->greeting('Hello ' . $notifiable->fullname)
            ->line('You have been requested to evaluate ' . $this->evaluateeName . '.')
            ->action('Start Evaluation', url('/evaluation/' . $this->token))
            ->line('This evaluation link will expire in 3 hours.');
    }
}
