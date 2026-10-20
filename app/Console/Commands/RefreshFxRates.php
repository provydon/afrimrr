<?php

namespace App\Console\Commands;

use App\Services\Mrr\FxRates;
use Illuminate\Console\Command;
use Throwable;

class RefreshFxRates extends Command
{
    protected $signature = 'fx:refresh';

    protected $description = 'Fetch live USD exchange rates and cache them for MRR conversion';

    public function handle(FxRates $fx): int
    {
        try {
            $rates = $fx->refresh();
        } catch (Throwable $e) {
            $this->error('FX refresh failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Cached live rates for '.count($rates).' currencies (e.g. NGN '.($rates['NGN'] ?? 'n/a').' USD/unit).');

        return self::SUCCESS;
    }
}
