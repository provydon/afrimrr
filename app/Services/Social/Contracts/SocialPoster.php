<?php

namespace App\Services\Social\Contracts;

interface SocialPoster
{
    /**
     * Publish a short status post. Throws on a hard failure.
     *
     * @throws \RuntimeException
     */
    public function post(string $text): void;

    public function channel(): string;
}
