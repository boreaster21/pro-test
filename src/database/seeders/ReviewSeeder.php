<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\User;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();
        $generalUsers = User::where('role', 'general')->get();

        if ($generalUsers->isEmpty() || $stores->isEmpty()) {
            $this->command->info('口コミを作成するためのユーザーまたは店舗が存在しません。');
            return;
        }

        foreach ($stores as $store) {
            // "評価なしテスト店" はスキップ
            if ($store->name === '評価なしテスト店') {
                continue;
            }

            $numberOfReviews = rand(1, 5);
            // レビュー投稿者数がユーザー数を超えないように調整
            $availableReviewersCount = $generalUsers->count();
            if ($numberOfReviews > $availableReviewersCount) {
                $numberOfReviews = $availableReviewersCount;
            }
            // レビュー投稿者がいない場合はスキップ
            if ($numberOfReviews <= 0) {
                continue;
            }

            $reviewers = $generalUsers->random($numberOfReviews);

            // $reviewers が単一の User モデルの場合、コレクションに変換
            if ($reviewers instanceof User) {
                $reviewers = collect([$reviewers]);
            }

            foreach ($reviewers as $reviewer) {
                // 既にレビューが存在しないか確認してから作成
                if (!$store->reviews()->where('user_id', $reviewer->id)->exists()) {
                    Review::factory()->create([
                        'user_id' => $reviewer->id,
                        'store_id' => $store->id,
                    ]);
                }
            }
        }
    }
}
