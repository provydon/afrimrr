<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_subscribe(): void
    {
        $this->from('/leaderboard')
            ->post('/subscribe', ['email' => 'Founder@Example.com'])
            ->assertRedirect('/leaderboard')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('subscribers', ['email' => 'founder@example.com', 'source' => 'popup']);
    }

    public function test_subscribing_is_idempotent(): void
    {
        Subscriber::factory()->create(['email' => 'dup@example.com']);

        $this->post('/subscribe', ['email' => 'dup@example.com'])->assertSessionHasNoErrors();

        $this->assertSame(1, Subscriber::where('email', 'dup@example.com')->count());
    }

    public function test_email_is_required_and_validated(): void
    {
        $this->from('/')->post('/subscribe', ['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('subscribers', 0);
    }

    public function test_source_can_be_recorded(): void
    {
        $this->post('/subscribe', ['email' => 'src@example.com', 'source' => 'footer']);

        $this->assertDatabaseHas('subscribers', ['email' => 'src@example.com', 'source' => 'footer']);
    }
}
