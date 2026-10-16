<?php

namespace App\Console\Commands;

use App\Jobs\SyncStartupMrr;
use App\Models\Startup;
use Illuminate\Console\Command;

class SyncMrrCommand extends Command
{
    protected $signature = 'mrr:sync {--now : Run synchronously instead of queueing}';

    protected $description = 'Sync verified MRR for every startup with a connected payment provider';

    public function handle(): int
    {
        $startups = Startup::whereHas('connections')->pluck('id');

        if ($startups->isEmpty()) {
            $this->info('No startups with connected providers.');

            return self::SUCCESS;
        }

        foreach ($startups as $id) {
            if ($this->option('now')) {
                SyncStartupMrr::dispatchSync($id);
            } else {
                SyncStartupMrr::dispatch($id);
            }
        }

        $this->info("Queued MRR sync for {$startups->count()} startup(s).");

        return self::SUCCESS;
    }
}
