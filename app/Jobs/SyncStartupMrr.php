<?php

namespace App\Jobs;

use App\Actions\SyncStartup;
use App\Models\Startup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncStartupMrr implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(public int $startupId) {}

    public function handle(SyncStartup $action): void
    {
        $startup = Startup::find($this->startupId);

        if ($startup) {
            $action->handle($startup);
        }
    }
}
