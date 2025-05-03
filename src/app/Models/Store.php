<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region',
        'genre',
        'description',
        'image_url',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'store_id', 'user_id')->withTimestamps();
    }

    public function updateAverageRating(): void
    {
        $reviews = $this->reviews();
        $reviewCount = $reviews->count();
        $averageRating = ($reviewCount > 0) ? $reviews->avg('rating') : 0;

        $this->average_rating = round($averageRating, 1);
        $this->review_count = $reviewCount;
        $this->saveQuietly(); 
    }
}
