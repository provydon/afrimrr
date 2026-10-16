<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $startup_id
 * @property int $mrr_usd
 * @property int $mrr_local
 * @property string $display_currency
 * @property int $active_subscriptions
 * @property \Illuminate\Support\Carbon $captured_on
 */
class MrrSnapshot extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'captured_on' => 'date',
        ];
    }

    /** @return BelongsTo<Startup, $this> */
    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }
}
