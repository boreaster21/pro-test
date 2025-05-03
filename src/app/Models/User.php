<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }


    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'favorites', 'user_id', 'store_id')->withTimestamps();
    }


    public function isFavorite(Store $store): bool
    {
        return $this->favorites()->where('store_id', $store->id)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
