<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PadApplication;

class ApplicationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $pad;
    public $tenant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PadApplication $application)
    {
        $this->application = $application;
        $this->pad = $application->pad;
        $this->tenant = $application->tenant;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Application Cancelled - ' . $this->pad->padName)
                    ->view('emails.application-cancelled')
                    ->with([
                        'application' => $this->application,
                        'pad' => $this->pad,
                        'tenant' => $this->tenant,
                    ]);
    }
} 