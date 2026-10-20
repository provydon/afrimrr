<?php

namespace App\Console\Commands;

use App\Mail\WeeklyReportMail;
use App\Models\Subscriber;
use App\Services\Newsletter\WeeklyReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyNewsletter extends Command
{
    protected $signature = 'newsletter:send-weekly {--dry : Build the report and count recipients without sending}';

    protected $description = 'Queue the weekly "Spot the Next Big Thing" report to all subscribed addresses';

    public function handle(WeeklyReport $builder): int
    {
        $report = $builder->build();

        if (! $builder->hasContent($report)) {
            $this->warn('No verified startups to report yet — skipping this week.');

            return self::SUCCESS;
        }

        $recipients = Subscriber::query()->subscribed()->count();

        if ($recipients === 0) {
            $this->warn('No active subscribers — nothing to send.');

            return self::SUCCESS;
        }

        if ($this->option('dry')) {
            $this->info("Dry run — would queue the report to {$recipients} subscriber(s).");
            $this->line('Period: '.$report['period_label']);
            $this->line('Top: '.collect($report['top'])->pluck('name')->implode(', '));

            return self::SUCCESS;
        }

        $sent = 0;

        Subscriber::query()->subscribed()->chunkById(200, function ($subscribers) use ($report, &$sent) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new WeeklyReportMail($report, $subscriber));
                $subscriber->forceFill(['last_sent_at' => now()])->save();
                $sent++;
            }
        });

        $this->info("Queued the weekly report to {$sent} subscriber(s).");

        return self::SUCCESS;
    }
}
