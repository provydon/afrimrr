<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $tagline
 * @property string|null $description
 * @property string|null $website
 * @property string|null $logo_url
 * @property string $category
 * @property string $country
 * @property int|null $founded_year
 * @property bool $is_verified
 * @property string|null $verification_source
 * @property int $mrr_usd
 * @property int $mrr_local
 * @property string $display_currency
 * @property int $active_subscriptions
 * @property float|null $mom_growth
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 * @property bool $is_public
 * @property bool $is_for_sale
 * @property int|null $asking_price_usd
 * @property bool $is_demo
 */
class Startup extends Model
{
    /** @use HasFactory<\Database\Factories\StartupFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_public' => 'boolean',
            'is_for_sale' => 'boolean',
            'is_demo' => 'boolean',
            'mom_growth' => 'float',
            'last_synced_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<Connection, $this> */
    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    /** @return HasMany<MrrSnapshot, $this> */
    public function snapshots(): HasMany
    {
        return $this->hasMany(MrrSnapshot::class);
    }

    /** @param Builder<Startup> $query */
    public function scopePublic(Builder $query): void
    {
        $query->where('is_public', true);
    }

    /** @param Builder<Startup> $query */
    public function scopeVerified(Builder $query): void
    {
        $query->where('is_verified', true);
    }

    /** Exclude seeded demo startups. @param Builder<Startup> $query */
    public function scopeReal(Builder $query): void
    {
        $query->where('is_demo', false);
    }

    public function categoryLabel(): string
    {
        return config('mrr.categories.'.$this->category, 'Other');
    }

    public function countryLabel(): string
    {
        return config('mrr.countries.'.$this->country, $this->country);
    }

    /**
     * Valuation multiple (asking price / annual run-rate), for the marketplace.
     */
    public function revenueMultiple(): ?float
    {
        if (! $this->is_for_sale || ! $this->asking_price_usd || $this->mrr_usd <= 0) {
            return null;
        }

        $arr = $this->mrr_usd * 12;

        return round($this->asking_price_usd / $arr, 1);
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'startup';
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
