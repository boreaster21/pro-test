<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'rating',
        'comment',
        'image_path',
    ];

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->store->updateAverageRating();
        });

        static::deleted(function (Review $review) {
            if ($review->image_path) {
                Storage::disk('public')->delete($review->image_path);
            }
            $review->store->updateAverageRating();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_path ? Storage::url($this->image_path) : null,
        );
    }
}