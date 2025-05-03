<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Store;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Review $review): bool
    {
        return true;
    }

    public function create(User $user, Store $store): bool
    {
        $isGeneralUser = $user->role === 'general';
        if (!$isGeneralUser) {
            return false;
        }

        $hasValidReservation = $user->reservations()
                                   ->where('store_id', $store->id)
                                   ->where('reservation_datetime', '<', now())
                                   ->exists();

        $hasNotReviewed = !$user->reviews()->where('store_id', $store->id)->exists();

        return $hasValidReservation && $hasNotReviewed;
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->isAdmin();
    }

    public function restore(User $user, Review $review): bool
    {
        return false;
    }

    public function forceDelete(User $user, Review $review): bool
    {
        return false;
    }
}
