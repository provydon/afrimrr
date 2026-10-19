<?php

namespace App\Console\Commands;

use App\Models\Startup;
use App\Services\Social\StartupAnnouncer;
use Illuminate\Console\Command;

class AnnounceStartupCommand extends Command
{
    protected $signature = 'startups:announce {slug : The startup slug} {--force : Re-announce even if already posted}';

    protected $description = 'Post the "just joined" announcement for a startup to the configured social channel';

    public function handle(StartupAnnouncer $announcer): int
    {
        $startup = Startup::where('slug', $this->argument('slug'))->first();

        if (! $startup) {
            $this->error('Startup not found.');

            return self::FAILURE;
        }

        if (! config('social.enabled')) {
            $this->warn('Announcements are disabled (set SOCIAL_ANNOUNCE=true). Nothing posted.');

            return self::SUCCESS;
        }

        if ($this->option('force')) {
            $startup->forceFill(['announced_at' => null])->save();
        }

        if ($announcer->announce($startup)) {
            $this->info("Announced {$startup->name}.");
        } else {
            $this->warn("Nothing posted for {$startup->name} (already announced, not verified, or no MRR).");
        }

        return self::SUCCESS;
    }
}
