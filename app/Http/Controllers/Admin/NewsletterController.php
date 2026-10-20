<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WeeklyReportMail;
use App\Models\Subscriber;
use App\Services\Newsletter\WeeklyReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class NewsletterController extends Controller
{
    /**
     * Send the current weekly report to the logged-in admin so they can
     * preview the real email before the Monday blast. Sent synchronously (not
     * queued) so the admin gets immediate pass/fail feedback.
     */
    public function sendTest(Request $request, WeeklyReport $builder): RedirectResponse
    {
        $report = $builder->build();

        if (! $builder->hasContent($report)) {
            return back()->with('error', 'No verified startups to report yet — nothing to preview.');
        }

        $user = $request->user();

        // Reuse the admin's subscriber row if they have one (so the unsubscribe
        // link is real); otherwise render against a transient one.
        $subscriber = Subscriber::firstOrNew(['email' => $user->email]);
        if (empty($subscriber->unsubscribe_token)) {
            $subscriber->unsubscribe_token = (string) Str::random(48);
        }

        try {
            Mail::to($user->email)->send(new WeeklyReportMail($report, $subscriber));
        } catch (Throwable $e) {
            Log::warning('Newsletter test send failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Could not send the test email: '.$e->getMessage());
        }

        return back()->with('success', "Test copy sent to {$user->email}.");
    }
}
