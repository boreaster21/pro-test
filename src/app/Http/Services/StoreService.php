<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class StoreService
{
    /**
     * @param Request
     * @return LengthAwarePaginator
     */
    public function getStoresForIndex(Request $request): LengthAwarePaginator
    {
        $sort = $request->input('sort', 'random');
        $region = $request->input('region');
        $genre = $request->input('genre');
        $keyword = $request->input('keyword');

        $query = Store::query();
        $query->withAvg('reviews', 'rating')
              ->withCount('reviews');

        if (Auth::check()) {
            $userId = Auth::id();
            $query->withExists(['favoritedByUsers as is_favorite' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }]);
        }

        $query->when($keyword, function (Builder $q, $keyword) {
            return $q->where(function (Builder $subQuery) use ($keyword) {
                $subQuery->where('name', 'like', "%{$keyword}%")
                         ->orWhere('description', 'like', "%{$keyword}%")
                         ->orWhere('region', 'like', "%{$keyword}%")
                         ->orWhere('genre', 'like', "%{$keyword}%");
            });
        });

        $query->when($region, function (Builder $q, $region) {
            return $q->where('region', $region);
        });

        $query->when($genre, function (Builder $q, $genre) {
            return $q->where('genre', $genre);
        });

        if ($sort === 'favorites' && Auth::check()) {
            $query->orderByDesc('is_favorite')->orderBy('id');
             $query->orderByDesc('is_favorite')->orderBy('id'); 
        } else {
            switch ($sort) {
                case 'rating_desc':
                    $query->orderByRaw('reviews_count > 0 DESC')
                          ->orderByDesc('reviews_avg_rating');
                    break;
                case 'rating_asc':
                    $query->orderByRaw('reviews_count > 0 DESC')
                          ->orderBy('reviews_avg_rating');
                    break;
                case 'random':
                default:
                    $query->inRandomOrder();
                    break;
            }
        }

        $stores = $query->paginate(15)->withQueryString();

        $stores->through(function ($store) {
            $store->average_rating = $store->reviews_avg_rating ?? 0.0;
            $store->review_count = $store->reviews_count ?? 0;
            $store->is_favorite = (bool) $store->is_favorite;
            return $store;
        });

        return $stores;
    }
} 