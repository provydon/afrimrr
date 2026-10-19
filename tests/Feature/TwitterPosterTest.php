<?php

namespace Tests\Feature;

use App\Services\Social\TwitterPoster;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class TwitterPosterTest extends TestCase
{
    private function poster(): TwitterPoster
    {
        return new TwitterPoster('ck', 'cs', 'at', 'as');
    }

    public function test_it_posts_the_text_with_an_oauth_signed_header(): void
    {
        Http::fake(['api.twitter.com/2/tweets' => Http::response(['data' => ['id' => '1']], 201)]);

        $this->poster()->post('hello world');

        Http::assertSent(function (Request $request) {
            $auth = $request->header('Authorization')[0] ?? '';

            return $request->url() === 'https://api.twitter.com/2/tweets'
                && $request->method() === 'POST'
                && $request['text'] === 'hello world'
                && str_starts_with($auth, 'OAuth ')
                && str_contains($auth, 'oauth_signature=')
                && str_contains($auth, 'oauth_consumer_key="ck"')
                && str_contains($auth, 'oauth_signature_method="HMAC-SHA1"');
        });
    }

    public function test_it_throws_when_x_rejects_the_post(): void
    {
        Http::fake(['api.twitter.com/2/tweets' => Http::response(['title' => 'Unauthorized'], 401)]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('401');
        $this->poster()->post('nope');
    }
}
