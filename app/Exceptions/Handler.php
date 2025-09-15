<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QueueFailedNotification;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);

        if ($exception instanceof \Illuminate\Queue\MaxAttemptsExceededException) {
            Notification::route('mail', 'admin@example.com')
                ->notify(new QueueFailedNotification($exception));
        }
    }
}
