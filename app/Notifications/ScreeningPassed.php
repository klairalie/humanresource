<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScreeningPassed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Channels this notification will be sent on.
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // can also add 'database' if you want to store it
    }

    /**
     * Email representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Screening Update')
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Congratulations! 🎉 You have passed the screening.')
            ->line('The management is now reviewing your documents.')
            ->line('We will contact you with the next steps soon.');
    }
}

