<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicantFailedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Screening Result – Application Update')
            ->greeting('Dear ' . $notifiable->first_name . ' ' . $notifiable->last_name . ',')
            ->line('Thank you for taking the time to apply and participate in our screening process.')
            ->line('After careful consideration, we regret to inform you that you have not been selected to proceed further at this time.')
            ->line('We sincerely appreciate your interest in joining our organization and encourage you to apply again for future opportunities that match your skills and experience.')
            ->line('Wishing you all the best in your career journey.')
            ->salutation('Sincerely, HR Management');
    }
}
