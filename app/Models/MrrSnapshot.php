<?php

namespace App\Models;

use Database\Factories\MrrSnapshotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $startup_id
 * @property int $mrr_usd
 * @property int $mrr_local
 * @property string $display_currency
 * @property int $active_subscriptions
 * @property Carbon $captured_on
 */
class MrrSnapshot extends Model
{
    /** @use HasFactory<MrrSnapshotFactory> */
    use HasFactory;

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
