<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Throwable;

class QueueFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $exception;

    /**
     * Create a new notification instance.
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠ Queue Job Failed')
            ->greeting('Hello Admin,')
            ->line('A queue job has failed in your Laravel application.')
            ->line('**Error Message:** ' . $this->exception->getMessage())
            ->line('**File:** ' . $this->exception->getFile())
            ->line('**Line:** ' . $this->exception->getLine())
            ->line('Please check the logs or the failed_jobs table for details.')
            ->salutation('Regards, 3RS Air-Conditioning Solution');
    }
}
