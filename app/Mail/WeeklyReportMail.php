<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        public array $report,
        public Subscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AfricanMRR Weekly — '.($this->report['period_label'] ?? 'the data report'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-report',
            with: [
                'report' => $this->report,
                'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->unsubscribe_token),
                'browseUrl' => route('leaderboard'),
            ],
        );
    }
}
