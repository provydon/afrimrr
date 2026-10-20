<?php

namespace Tests\Feature;

use App\Mail\WeeklyReportMail;
use App\Models\Startup;
use App\Models\Subscriber;
use App\Services\Newsletter\WeeklyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WeeklyNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscriber_gets_an_unsubscribe_token_on_create(): void
    {
        $subscriber = Subscriber::create(['email' => 'reader@example.com']);

        $this->assertNotEmpty($subscriber->unsubscribe_token);
        $this->assertTrue($subscriber->isSubscribed());
    }

    public function test_command_queues_one_mail_per_subscribed_address(): void
    {
        Mail::fake();
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00, 'is_demo' => false]);

        Subscriber::factory()->count(3)->create();
        $gone = Subscriber::factory()->unsubscribed()->create(['email' => 'gone@example.com']);

        $this->artisan('newsletter:send-weekly')->assertSuccessful();

        Mail::assertQueued(WeeklyReportMail::class, 3);
        Mail::assertNotQueued(WeeklyReportMail::class, fn (WeeklyReportMail $m) => $m->hasTo($gone->email));
    }

    public function test_command_skips_when_there_are_no_reportable_startups(): void
    {
        Mail::fake();
        Subscriber::factory()->count(2)->create();
        // Only demo/private/anonymous exist — nothing to report.
        Startup::factory()->verified()->demo()->create(['mrr_usd' => 50_000_00]);

        $this->artisan('newsletter:send-weekly')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_command_skips_when_there_are_no_subscribers(): void
    {
        Mail::fake();
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00, 'is_demo' => false]);

        $this->artisan('newsletter:send-weekly')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_dry_run_queues_nothing(): void
    {
        Mail::fake();
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00, 'is_demo' => false]);
        Subscriber::factory()->count(2)->create();

        $this->artisan('newsletter:send-weekly', ['--dry' => true])->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_report_excludes_demo_private_and_anonymous_startups(): void
    {
        Startup::factory()->verified()->create(['name' => 'Public Co', 'mrr_usd' => 10_000_00, 'is_demo' => false]);
        Startup::factory()->verified()->demo()->create(['name' => 'Demo Co', 'mrr_usd' => 99_000_00]);
        Startup::factory()->verified()->private()->create(['name' => 'Private Co', 'mrr_usd' => 99_000_00]);
        Startup::factory()->verified()->anonymous()->create(['name' => 'Anon Co', 'mrr_usd' => 99_000_00]);

        $report = app(WeeklyReport::class)->build();

        $names = collect($report['top'])->pluck('name')->all();
        $this->assertSame(['Public Co'], $names);
        $this->assertSame(1, $report['stats']['verified_count']);
    }

    public function test_unsubscribe_marks_the_subscriber_and_is_idempotent(): void
    {
        $subscriber = Subscriber::factory()->create();

        $this->get(route('newsletter.unsubscribe', $subscriber->unsubscribe_token))
            ->assertOk()
            ->assertSee($subscriber->email);

        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
        $firstStamp = $subscriber->fresh()->unsubscribed_at;

        // Second click doesn't move the timestamp or error.
        $this->get(route('newsletter.unsubscribe', $subscriber->unsubscribe_token))->assertOk();
        $this->assertEquals($firstStamp, $subscriber->fresh()->unsubscribed_at);
    }

    public function test_unknown_unsubscribe_token_404s(): void
    {
        $this->get('/unsubscribe/'.str_repeat('z', 48))->assertNotFound();
    }

    public function test_mailable_renders_with_a_working_unsubscribe_link(): void
    {
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00, 'is_demo' => false]);
        $subscriber = Subscriber::factory()->create();
        $report = app(WeeklyReport::class)->build();

        $rendered = (new WeeklyReportMail($report, $subscriber))->render();

        $this->assertStringContainsString(
            route('newsletter.unsubscribe', $subscriber->unsubscribe_token),
            $rendered,
        );
        $this->assertStringContainsString('Spot the next big thing', $rendered);
    }
}
