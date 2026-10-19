<?php

namespace Tests\Feature;

use App\Models\Startup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OgImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_a_png_card_for_a_public_startup(): void
    {
        if (! extension_loaded('gd') || ! function_exists('imagettftext')) {
            $this->markTestSkipped('GD/FreeType not available.');
        }

        $startup = Startup::factory()->verified()->create([
            'slug' => 'acme',
            'mrr_usd' => 1_234_500,
            'active_subscriptions' => 120,
        ]);

        $response = $this->get("/og/startups/{$startup->slug}.png");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');

        $body = $response->getContent();
        $this->assertNotEmpty($body);
        // PNG magic number.
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($body, 0, 8));
    }

    public function test_card_is_hidden_for_private_startups(): void
    {
        $startup = Startup::factory()->verified()->private()->create(['slug' => 'secret']);

        $this->get("/og/startups/{$startup->slug}.png")->assertNotFound();
    }

    public function test_card_is_hidden_for_anonymous_startups(): void
    {
        $startup = Startup::factory()->verified()->anonymous()->create(['slug' => 'ghost']);

        $this->get("/og/startups/{$startup->slug}.png")->assertNotFound();
    }

    public function test_card_falls_back_when_gd_is_unavailable(): void
    {
        if (extension_loaded('gd') && function_exists('imagettftext')) {
            $this->markTestSkipped('GD is available, cannot exercise the fallback here.');
        }

        $startup = Startup::factory()->verified()->create(['slug' => 'acme']);

        $this->get("/og/startups/{$startup->slug}.png")
            ->assertRedirect(config('app.url').'/og-image.png');
    }

    public function test_profile_page_advertises_the_per_startup_card_in_meta(): void
    {
        $startup = Startup::factory()->verified()->create([
            'slug' => 'acme',
            'name' => 'Acme Payments',
        ]);

        $html = $this->get("/startups/{$startup->slug}")->assertOk()->getContent();

        $ogUrl = route('og.startup', $startup->slug);
        $this->assertStringContainsString('property="og:image" content="'.e($ogUrl).'"', $html);
        $this->assertStringContainsString('name="twitter:image" content="'.e($ogUrl).'"', $html);
        $this->assertStringContainsString('Acme Payments', $html);
    }
}
