<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $interview;

    public function __construct($interview)
    {
        $this->interview = $interview;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Interview Scheduled')
            ->greeting('Hello ' . $notifiable->first_name . ' ' . $notifiable->last_name . ',')
            ->line('You have been scheduled for an interview.')
            ->line('📅 Date: ' . $this->interview->interview_date)
            ->line('⏰ Time: ' . $this->interview->interview_time)
            ->line('📍 Location: ' . $this->interview->location)
            ->line('👤 HR Manager: ' . $this->interview->hr_manager)
            ->line('Please be on time and prepare well.')
            ->salutation('Best regards, HR Department');
    }
}
