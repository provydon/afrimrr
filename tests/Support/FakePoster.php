<?php

namespace Tests\Support;

use App\Services\Social\Contracts\SocialPoster;
use RuntimeException;

/**
 * In-memory SocialPoster for tests — records posts, or throws on demand.
 */
class FakePoster implements SocialPoster
{
    /** @var array<int, string> */
    public array $posts = [];

    public function __construct(public bool $shouldFail = false) {}

    public function post(string $text): void
    {
        if ($this->shouldFail) {
            throw new RuntimeException('simulated post failure');
        }

        $this->posts[] = $text;
    }

    public function channel(): string
    {
        return 'fake';
    }
}
