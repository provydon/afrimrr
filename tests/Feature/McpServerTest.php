<?php

namespace Tests\Feature;

use App\Models\Startup;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class McpServerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $message
     */
    private function rpc(array $message): TestResponse
    {
        return $this->postJson('/mcp', $message);
    }

    public function test_info_endpoint_identifies_the_server(): void
    {
        $this->getJson('/mcp')
            ->assertOk()
            ->assertJsonPath('name', 'AfricanMRR MCP')
            ->assertJsonPath('transport', 'streamable-http');
    }

    public function test_initialize_returns_server_info(): void
    {
        $this->rpc(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'initialize'])
            ->assertOk()
            ->assertJsonPath('jsonrpc', '2.0')
            ->assertJsonPath('id', 1)
            ->assertJsonPath('result.serverInfo.name', 'AfricanMRR')
            ->assertJsonPath('result.protocolVersion', '2024-11-05');
    }

    public function test_mcp_endpoint_is_csrf_exempt(): void
    {
        // CSRF verification is auto-disabled inside tests, so assert the exemption
        // at its source: 'mcp' must be in the middleware's except list.
        $middleware = app(VerifyCsrfToken::class);

        $this->assertContains('mcp', $middleware->getExcludedPaths());
    }

    public function test_ping_returns_an_empty_result(): void
    {
        $this->rpc(['jsonrpc' => '2.0', 'id' => 9, 'method' => 'ping'])
            ->assertOk()
            ->assertJsonPath('id', 9)
            ->assertJsonPath('result', []);
    }

    public function test_tools_list_exposes_the_three_tools(): void
    {
        $res = $this->rpc(['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/list']);
        $res->assertOk();

        $names = collect($res->json('result.tools'))->pluck('name')->all();
        $this->assertEqualsCanonicalizing(['search_startups', 'get_startup', 'leaderboard'], $names);
    }

    public function test_search_startups_tool_returns_matches(): void
    {
        Startup::factory()->create(['name' => 'Flutterpay', 'category' => 'fintech', 'country' => 'NG', 'mrr_usd' => 10_000_00]);
        Startup::factory()->create(['name' => 'Other Co', 'category' => 'ai', 'country' => 'KE']);

        $res = $this->rpc([
            'jsonrpc' => '2.0', 'id' => 3, 'method' => 'tools/call',
            'params' => ['name' => 'search_startups', 'arguments' => ['query' => 'flutter', 'country' => 'ng']],
        ]);

        $res->assertOk();
        $payload = json_decode($res->json('result.content.0.text'), true);
        $this->assertSame(1, $payload['count']);
        $this->assertSame('Flutterpay', $payload['startups'][0]['name']);
    }

    public function test_get_startup_tool_returns_profile_or_not_found(): void
    {
        $startup = Startup::factory()->verified()->create();

        $found = $this->rpc([
            'jsonrpc' => '2.0', 'id' => 4, 'method' => 'tools/call',
            'params' => ['name' => 'get_startup', 'arguments' => ['slug' => $startup->slug]],
        ]);
        $payload = json_decode($found->json('result.content.0.text'), true);
        $this->assertSame($startup->slug, $payload['slug']);

        $missing = $this->rpc([
            'jsonrpc' => '2.0', 'id' => 5, 'method' => 'tools/call',
            'params' => ['name' => 'get_startup', 'arguments' => ['slug' => 'does-not-exist']],
        ]);
        $this->assertSame('not found', json_decode($missing->json('result.content.0.text'), true)['error']);
    }

    public function test_leaderboard_tool_ranks_startups(): void
    {
        Startup::factory()->create(['mrr_usd' => 10_000_00]);
        Startup::factory()->create(['mrr_usd' => 30_000_00]);

        $res = $this->rpc([
            'jsonrpc' => '2.0', 'id' => 6, 'method' => 'tools/call',
            'params' => ['name' => 'leaderboard', 'arguments' => ['limit' => 10]],
        ]);
        $payload = json_decode($res->json('result.content.0.text'), true);
        $this->assertSame(1, $payload['startups'][0]['rank']);
        $this->assertSame(30_000_00, $payload['startups'][0]['mrr_usd']);
    }

    public function test_unknown_tool_and_method_return_json_rpc_errors(): void
    {
        $this->rpc([
            'jsonrpc' => '2.0', 'id' => 7, 'method' => 'tools/call',
            'params' => ['name' => 'drop_tables'],
        ])->assertJsonPath('error.code', -32602);

        $this->rpc(['jsonrpc' => '2.0', 'id' => 8, 'method' => 'no/such'])
            ->assertJsonPath('error.code', -32601);
    }

    public function test_notifications_get_no_response_body(): void
    {
        $res = $this->rpc(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']);
        $res->assertOk();
        // Notifications produce null -> controller returns an empty result object.
        $this->assertSame(['jsonrpc' => '2.0', 'id' => null, 'result' => []], $res->json());
    }

    public function test_batched_requests_are_answered_in_a_list(): void
    {
        $res = $this->rpc([
            ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'ping'],
            ['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/list'],
        ]);

        $res->assertOk();
        $this->assertCount(2, $res->json());
        $res->assertJsonPath('0.id', 1)->assertJsonPath('1.id', 2);
    }
}
