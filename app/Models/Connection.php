<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $startup_id
 * @property string $provider
 * @property string $credentials
 * @property string $status
 * @property string|null $last_error
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 */
class Connection extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted',
            'last_synced_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Startup, $this> */
    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }

    public function providerLabel(): string
    {
        return config('mrr.providers.'.$this->provider.'.label', ucfirst($this->provider));
    }
}
