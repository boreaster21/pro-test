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
            if ($store->name === '評価なしテスト店') {
                continue;
            }

            $numberOfReviews = rand(1, 5);
            $availableReviewersCount = $generalUsers->count();
            if ($numberOfReviews > $availableReviewersCount) {
                $numberOfReviews = $availableReviewersCount;
            }
            if ($numberOfReviews <= 0) {
                continue;
            }

            $reviewers = $generalUsers->random($numberOfReviews);

            if ($reviewers instanceof User) {
                $reviewers = collect([$reviewers]);
            }

            foreach ($reviewers as $reviewer) {
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
