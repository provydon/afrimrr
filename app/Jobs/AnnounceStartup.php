<?php

namespace App\Jobs;

use App\Models\Startup;
use App\Services\Social\StartupAnnouncer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnnounceStartup implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 30;

    public function __construct(public int $startupId) {}

    public function handle(StartupAnnouncer $announcer): void
    {
        $startup = Startup::find($this->startupId);

        if ($startup) {
            $announcer->announce($startup);
        }
    }
}
