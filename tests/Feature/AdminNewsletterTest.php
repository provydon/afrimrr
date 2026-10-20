<?php

namespace Tests\Feature;

use App\Mail\WeeklyReportMail;
use App\Models\Startup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_themselves_a_test_copy(): void
    {
        Mail::fake();
        Startup::factory()->verified()->create(['mrr_usd' => 10_000_00, 'is_demo' => false]);
        $admin = User::factory()->admin()->create(['email' => 'boss@example.com']);

        $this->actingAs($admin)->post('/admin/newsletter/test')
            ->assertRedirect()
            ->assertSessionHas('success');

        Mail::assertSent(WeeklyReportMail::class, fn (WeeklyReportMail $m) => $m->hasTo('boss@example.com'));
    }

    public function test_test_copy_is_blocked_when_no_startups_to_report(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/newsletter/test')
            ->assertRedirect()
            ->assertSessionHas('error');

        Mail::assertNothingSent();
    }

    public function test_non_admins_cannot_send_test_copies(): void
    {
        Mail::fake();
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->post('/admin/newsletter/test')->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_guests_cannot_send_test_copies(): void
    {
        $this->post('/admin/newsletter/test')->assertRedirect(route('login'));
    }
}
