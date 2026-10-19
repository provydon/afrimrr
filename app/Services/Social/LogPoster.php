<?php

namespace App\Services\Social;

use App\Services\Social\Contracts\SocialPoster;
use Illuminate\Support\Facades\Log;

/**
 * Safe default poster: records the announcement in the log instead of calling a
 * live API. Used until real credentials are configured.
 */
class LogPoster implements SocialPoster
{
    public function post(string $text): void
    {
        Log::info('[social announcement]'.PHP_EOL.$text);
    }

    public function channel(): string
    {
        return 'log';
    }
}
