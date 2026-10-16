<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string|null $headline
 * @property string|null $bio
 * @property string|null $avatar_url
 * @property string|null $twitter
 * @property string|null $website
 * @property bool $is_admin
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'username', 'headline', 'bio', 'avatar_url', 'twitter', 'website', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    /** @return HasMany<Startup, $this> */
    public function startups(): HasMany
    {
        return $this->hasMany(Startup::class);
    }

    public function publicUsername(): string
    {
        return $this->username ?: 'user-'.$this->id;
    }

    /**
     * Ensure every user has a unique public username handle.
     */
    public static function uniqueUsername(string $base): string
    {
        $slug = Str::slug($base) ?: 'founder';
        $username = $slug;
        $i = 1;

        while (static::where('username', $username)->exists()) {
            $username = $slug.'-'.$i++;
        }

        return $username;
    }
}
