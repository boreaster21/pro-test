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
     * 店舗一覧を取得する（検索、ソート、ページネーション、お気に入り状態付き）
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getStoresForIndex(Request $request): LengthAwarePaginator
    {
        $sort = $request->input('sort', 'random');
        $region = $request->input('region');
        $genre = $request->input('genre');
        $keyword = $request->input('keyword');

        $query = Store::query();

        // Eager load reviews count/avg for sorting, and check favorite status efficiently
        // Laravel 11では withAvg, withCount がデフォルトで利用可能
        $query->withAvg('reviews', 'rating') // 'reviews_avg_rating' というエイリアスで平均評価を取得
              ->withCount('reviews'); // 'reviews_count' というエイリアスでレビュー数を取得

        // 認証済みユーザーの場合、お気に入り状態を効率的に取得
        if (Auth::check()) {
            $userId = Auth::id();
            // 'is_favorite' という属性で、ログインユーザーがお気に入り登録しているかどうかのフラグを取得
            $query->withExists(['favoritedByUsers as is_favorite' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }]);
        }

        // Keyword Search
        $query->when($keyword, function (Builder $q, $keyword) {
            return $q->where(function (Builder $subQuery) use ($keyword) {
                $subQuery->where('name', 'like', "%{$keyword}%")
                         ->orWhere('description', 'like', "%{$keyword}%")
                         ->orWhere('region', 'like', "%{$keyword}%") // 必要であれば
                         ->orWhere('genre', 'like', "%{$keyword}%");  // 必要であれば
            });
        });

        // Region Filter
        $query->when($region, function (Builder $q, $region) {
            return $q->where('region', $region);
        });

        // Genre Filter
        $query->when($genre, function (Builder $q, $genre) {
            return $q->where('genre', $genre);
        });

        // Sorting Logic
        if ($sort === 'favorites' && Auth::check()) {
            // お気に入り（is_favoriteがtrue）の店舗を優先的に表示し、その中での順序は問わない（ID順など）
            // withExists で is_favorite を取得しているので、それを使ってソート
             $query->orderByDesc('is_favorite')->orderBy('id'); // お気に入りを先頭に、それ以外はID順
        } else {
            switch ($sort) {
                case 'rating_desc':
                    // レビューがあるものを優先し、平均評価（reviews_avg_rating）で降順ソート
                    $query->orderByRaw('reviews_count > 0 DESC') // レビュー数 > 0 のものを先に (DESCでtrueが先)
                          ->orderByDesc('reviews_avg_rating');
                    break;
                case 'rating_asc':
                    // レビューがあるものを優先し、平均評価（reviews_avg_rating）で昇順ソート
                    $query->orderByRaw('reviews_count > 0 DESC') // レビュー数 > 0 のものを先に
                          ->orderBy('reviews_avg_rating');
                    break;
                case 'random':
                default:
                    $query->inRandomOrder();
                    break;
            }
        }

        // Paginate
        $stores = $query->paginate(15)->withQueryString(); // クエリパラメータを維持

        // average_rating と review_count をモデルの属性として使えるように変換 (withAvg/withCount結果を利用)
        // is_favorite も withExists の結果を利用（キャストが必要な場合あり）
        $stores->through(function ($store) {
             // Laravel 11 では withAvg/withCount の結果は自動的にアクセス可能だが、
             // 既存のビューとの互換性のため、明示的に属性にセットする場合
            $store->average_rating = $store->reviews_avg_rating ?? 0.0;
            $store->review_count = $store->reviews_count ?? 0;
            // withExists は boolean で is_favorite 属性をセットする
            // (明示的なキャストは通常不要だが、互換性のため残す場合も)
            $store->is_favorite = (bool) $store->is_favorite;
            return $store;
        });


        return $stores;
    }
} 