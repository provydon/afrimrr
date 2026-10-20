<?php

namespace App\Models;

use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $email
 * @property string|null $source
 * @property string $unsubscribe_token
 * @property Carbon|null $unsubscribed_at
 * @property Carbon|null $last_sent_at
 */
class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'unsubscribed_at' => 'datetime',
            'last_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = (string) Str::random(48);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'unsubscribe_token';
    }

    public function isSubscribed(): bool
    {
        return $this->unsubscribed_at === null;
    }

    /**
     * @param  Builder<Subscriber>  $query
     */
    public function scopeSubscribed(Builder $query): void
    {
        $query->whereNull('unsubscribed_at');
    }
}
