<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Review;

class StoreController extends Controller
{

    public function index(Request $request): View
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
            $store->is_favorite = (bool) ($store->is_favorite ?? false);
            return $store;
        });

        return view('stores.index', compact('stores'));
    }

    public function show(Store $store): View
    {
        $user = Auth::user();
        $canPostReview = $user ? $user->can('create', [Review::class, $store]) : false;

        return view('stores.show', compact('store', 'canPostReview'));
    }

    public function reviewsApi(Store $store): JsonResponse
    {
        $user = Auth::user();

        $reviews = $store->reviews()->with('user:id,name')
                         ->latest()
                         ->get()
                         ->map(function ($review) use ($user) {
                            $review->image_url_full = $review->image_url; 
                            $review->user_name_display = $review->user ? $review->user->name : '匿名ユーザー';
                            $review->created_at_formatted = $review->created_at->format('Y/m/d H:i');
                            $review->review_id = $review->id;
                            $review->can_update = $user ? $user->can('update', $review) : false;
                            $review->can_delete = $user ? $user->can('delete', $review) : false;
                            return $review;
                         });

        return response()->json($reviews);
    }
}