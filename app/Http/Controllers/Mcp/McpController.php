<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use App\Support\StartupPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Minimal, stateless MCP (Model Context Protocol) server over streamable HTTP.
 * Speaks JSON-RPC 2.0. Exposes read-only tools over AfriMRR's public data.
 */
class McpController extends Controller
{
    private const PROTOCOL = '2024-11-05';

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        // Batched requests.
        if (array_is_list($payload) && $payload !== []) {
            $responses = array_values(array_filter(array_map(fn ($m) => $this->dispatch($m), $payload)));

            return response()->json($responses);
        }

        $response = $this->dispatch($payload);

        return response()->json($response ?? ['jsonrpc' => '2.0', 'id' => null, 'result' => new \stdClass]);
    }

    public function info(): JsonResponse
    {
        return response()->json([
            'name' => 'AfriMRR MCP',
            'transport' => 'streamable-http',
            'endpoint' => url('/mcp'),
            'protocolVersion' => self::PROTOCOL,
            'usage' => 'POST JSON-RPC 2.0 messages (initialize, tools/list, tools/call).',
        ]);
    }

    /**
     * @param  array<string, mixed>  $msg
     * @return array<string, mixed>|null  null for notifications
     */
    private function dispatch(array $msg): ?array
    {
        $id = $msg['id'] ?? null;
        $method = $msg['method'] ?? '';
        $params = $msg['params'] ?? [];

        // Notifications have no id and expect no response.
        if ($id === null && str_starts_with((string) $method, 'notifications/')) {
            return null;
        }

        return match ($method) {
            'initialize' => $this->result($id, [
                'protocolVersion' => self::PROTOCOL,
                'capabilities' => ['tools' => ['listChanged' => false]],
                'serverInfo' => ['name' => 'AfriMRR', 'version' => '1.0.0'],
                'instructions' => 'Read-only access to verified MRR data for African startups.',
            ]),
            'ping' => $this->result($id, new \stdClass),
            'tools/list' => $this->result($id, ['tools' => $this->tools()]),
            'tools/call' => $this->callTool($id, $params),
            default => $this->error($id, -32601, "Method not found: {$method}"),
        };
    }

    /** @return array<int, array<string, mixed>> */
    private function tools(): array
    {
        return [
            [
                'name' => 'search_startups',
                'description' => 'Search African startups by name, category or country. Returns verified MRR, growth and profile links.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Text to match against name/tagline'],
                        'category' => ['type' => 'string', 'description' => 'e.g. fintech, ai, saas, devtools'],
                        'country' => ['type' => 'string', 'description' => 'ISO-2 code, e.g. NG, KE, GH'],
                        'limit' => ['type' => 'integer', 'description' => 'Max results (default 20, max 100)'],
                    ],
                ],
            ],
            [
                'name' => 'get_startup',
                'description' => 'Get the full verified profile of one startup by its slug.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => ['slug' => ['type' => 'string']],
                    'required' => ['slug'],
                ],
            ],
            [
                'name' => 'leaderboard',
                'description' => 'Top African startups ranked by verified monthly recurring revenue (USD).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => ['limit' => ['type' => 'integer', 'description' => 'Default 20, max 100']],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function callTool(mixed $id, array $params): array
    {
        $name = $params['name'] ?? '';
        $args = $params['arguments'] ?? [];

        $data = match ($name) {
            'search_startups' => $this->searchStartups($args),
            'get_startup' => $this->getStartup($args),
            'leaderboard' => $this->leaderboard($args),
            default => null,
        };

        if ($data === null) {
            return $this->error($id, -32602, "Unknown tool: {$name}");
        }

        return $this->result($id, [
            'content' => [[
                'type' => 'text',
                'text' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ]],
        ]);
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    private function searchStartups(array $args): array
    {
        $limit = min(100, max(1, (int) ($args['limit'] ?? 20)));
        $q = trim((string) ($args['query'] ?? ''));

        $rows = Startup::public()->with('owner')
            ->when($q !== '', fn ($b) => $b->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")->orWhere('tagline', 'like', "%{$q}%")))
            ->when(! empty($args['category']), fn ($b) => $b->where('category', $args['category']))
            ->when(! empty($args['country']), fn ($b) => $b->where('country', strtoupper((string) $args['country'])))
            ->orderByDesc('mrr_usd')->limit($limit)->get()
            ->map(fn (Startup $s) => StartupPresenter::card($s));

        return ['count' => $rows->count(), 'startups' => $rows];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>|null
     */
    private function getStartup(array $args): ?array
    {
        $startup = Startup::public()->with('owner')->where('slug', $args['slug'] ?? '')->first();

        return $startup ? StartupPresenter::profile($startup) : ['error' => 'not found'];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    private function leaderboard(array $args): array
    {
        $limit = min(100, max(1, (int) ($args['limit'] ?? 20)));
        $rows = Startup::public()->with('owner')->where('mrr_usd', '>', 0)
            ->orderByDesc('mrr_usd')->limit($limit)->get()->values()
            ->map(fn (Startup $s, int $i) => array_merge(StartupPresenter::card($s), ['rank' => $i + 1]));

        return ['startups' => $rows];
    }

    private function result(mixed $id, mixed $result): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    private function error(mixed $id, int $code, string $message): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
    }
}
