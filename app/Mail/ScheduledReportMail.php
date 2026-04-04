<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $reportType,
        public string $htmlContent,
    ) {}

    public function build(): static
    {
        return $this->subject("LMS Scheduled Report: {$this->reportType}")
            ->html($this->htmlContent);
    }
}
