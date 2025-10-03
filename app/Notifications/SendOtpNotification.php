<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Attendance OTP Code')
            ->line('Here is your one-time password for attendance verification:')
            ->line("**{$this->otp}**")
            ->line('⚠️ This code will expire in 2 minutes.')
            ->line('If you did not attempt to log attendance, please ignore this email.');
    }
}
