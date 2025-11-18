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
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Congratulations! You Are Hired')
            ->line("Dear {$this->applicant->first_name},")
            ->line('We are pleased to inform you that you have been selected for the position at 3RS Air-Conditioning Solution.')
            ->line('Thank you for applying and for showing interest in becoming part of our team.')
            ->line('')
            ->line('---')
            ->line('**BUSINESS TERMS & CONDITIONS**')
            ->line('SMART AIR-CONDITIONING SERVICES TRANSACTION MANAGEMENT SYSTEM, under the business policy of 3RS Air-Conditioning Solution, confirms that the business is a sole proprietorship or family-owned enterprise. It holds all required legal documents such as the mayor’s permit, DTI registration, and other government-mandated permits.')
            ->line('The business fully complies with tax regulations and operates as a VAT-registered entity.')
            ->line('Under Philippine labor and business guidelines, companies with fewer than 10 employees are not required to provide mandatory government benefits. Since 3RS Air-Conditioning Solution currently has only 8 employees, the business is legally permitted not to offer benefits such as SSS at this time.')
            ->line('During the hiring process, the company only requires a simple bio-data. Applicants are often relatives, acquaintances, or referrals, and selection is based primarily on skills and hands-on experience rather than formal educational background, given the technical and project-based nature of the work.')
            ->line('The company acknowledges occasional tardiness but does not impose unnecessary deductions. Only cash advances or newly implemented late penalties may be deducted from worker pay.')
            ->line('By proceeding with employment, you acknowledge and agree to these business policies and conditions of 3RS Air-Conditioning Solution.')
            ->line('---')
            ->line('')
            ->line('We look forward to having you as part of our team!')
            ->line('Sincerely,')
            ->line('3RS Air-Conditioning Solution');
    }
}
