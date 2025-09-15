<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $applicant_status;

    public function __construct($applicant, $applicant_status)
    {
        $this->applicant = $applicant;
        $this->applicant_status = $applicant_status;
    }

    public function build()
    {
        return $this->subject('Update on Your Application Status')
                    ->view('emails.changestatus');
    }
}
