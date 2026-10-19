<?php

namespace App\Services\Social;

use App\Services\Social\Contracts\SocialPoster;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Posts to X (Twitter) via API v2 (POST /2/tweets) using OAuth 1.0a
 * user-context signing. The JSON body is not part of the OAuth signature.
 */
class TwitterPoster implements SocialPoster
{
    private const ENDPOINT = 'https://api.twitter.com/2/tweets';

    public function __construct(
        private string $apiKey,
        private string $apiSecret,
        private string $accessToken,
        private string $accessSecret,
    ) {}

    public function post(string $text): void
    {
        $header = $this->authorizationHeader('POST', self::ENDPOINT);

        try {
            $response = Http::withHeaders(['Authorization' => $header])
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post(self::ENDPOINT, ['text' => $text]);
        } catch (Throwable $e) {
            throw new RuntimeException('Could not reach X: '.$e->getMessage(), previous: $e);
        }

        if (! $response->successful()) {
            throw new RuntimeException('X rejected the post ('.$response->status().'): '.$response->body());
        }
    }

    public function channel(): string
    {
        return 'twitter';
    }

    /**
     * Build the OAuth 1.0a Authorization header for the request.
     */
    private function authorizationHeader(string $method, string $url): string
    {
        $oauth = [
            'oauth_consumer_key' => $this->apiKey,
            'oauth_nonce' => bin2hex(random_bytes(16)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) time(),
            'oauth_token' => $this->accessToken,
            'oauth_version' => '1.0',
        ];

        $oauth['oauth_signature'] = $this->signature($method, $url, $oauth);

        $parts = [];
        foreach ($oauth as $key => $value) {
            $parts[] = rawurlencode($key).'="'.rawurlencode($value).'"';
        }

        return 'OAuth '.implode(', ', $parts);
    }

    /**
     * @param  array<string, string>  $oauth
     */
    private function signature(string $method, string $url, array $oauth): string
    {
        ksort($oauth);

        $params = [];
        foreach ($oauth as $key => $value) {
            $params[] = rawurlencode($key).'='.rawurlencode($value);
        }

        $base = strtoupper($method).'&'.rawurlencode($url).'&'.rawurlencode(implode('&', $params));
        $key = rawurlencode($this->apiSecret).'&'.rawurlencode($this->accessSecret);

        return base64_encode(hash_hmac('sha1', $base, $key, true));
    }
}
