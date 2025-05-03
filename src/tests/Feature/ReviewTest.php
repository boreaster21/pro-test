<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Store;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_general_user_can_post_review_with_valid_data(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $imageData = UploadedFile::fake()->image('review.jpg');
        $reviewData = [
            'rating' => 5,
            'comment' => 'とても美味しかったです！',
            'image' => $imageData,
        ];

        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);

        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'store_id' => $store->id,
            'rating' => $reviewData['rating'],
            'comment' => $reviewData['comment'],
            'image_path' => 'reviews/' . $imageData->hashName(),
        ]);
        $review = Review::first();
        $this->assertNotNull($review->image_path);
        Storage::disk('public')->assertExists($review->image_path);
    }

    public function test_authenticated_general_user_can_post_review_without_image(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'rating' => 4,
            'comment' => '画像なしのテストコメント。',
            'image' => null,
        ];

        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);

        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'store_id' => $store->id,
            'rating' => $reviewData['rating'],
            'comment' => $reviewData['comment'],
            'image_path' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_post_review(): void
    {
        $store = Store::factory()->create();
        $reviewData = [
            'rating' => 5,
            'comment' => 'テストコメント',
        ];
        $response = $this->post(route('reviews.store', $store), $reviewData);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_admin_user_cannot_post_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $admin->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'rating' => 5,
            'comment' => '管理者のコメント',
        ];
        $response = $this->actingAs($admin)
                         ->post(route('reviews.store', $store), $reviewData);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_user_cannot_post_review_for_store_they_did_not_reserve(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        $reviewData = [
            'rating' => 5,
            'comment' => '予約してない店のコメント',
        ];
        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_user_cannot_post_review_before_reservation_datetime(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->addDay(),
        ]);
        $reviewData = [
            'rating' => 5,
            'comment' => '来店前のコメント',
        ];
        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_user_cannot_post_review_twice_for_the_same_store(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
        ]);
        $reviewData = [
            'rating' => 4,
            'comment' => '２回目のコメント',
        ];
        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);
        $response->assertStatus(403);
        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_review_post_fails_if_rating_is_missing(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'comment' => '評価なしテスト',
            'image' => UploadedFile::fake()->image('review.jpg'),
        ];
        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);
        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_review_post_fails_if_comment_is_missing(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'rating' => 5,
            'image' => UploadedFile::fake()->image('review.jpg'),
        ];
        $response = $this->actingAs($user)->post(route('reviews.store', $store), $reviewData);
        $response->assertSessionHasErrors('comment');
         $this->assertDatabaseMissing('reviews', [
            'rating' => $reviewData['rating'],
        ]);
    }

    public function test_review_post_fails_if_comment_is_too_long(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'rating' => 5,
            'comment' => str_repeat('a', 401),
            'image' => UploadedFile::fake()->image('review.jpg'),
        ];
        $response = $this->actingAs($user)->post(route('reviews.store', $store), $reviewData);
        $response->assertSessionHasErrors('comment');
         $this->assertDatabaseMissing('reviews', [
            'rating' => $reviewData['rating'],
        ]);
    }

    public function test_review_post_fails_if_rating_is_out_of_range(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'rating' => 6,
            'comment' => '評価範囲外テスト',
            'image' => UploadedFile::fake()->image('review.jpg'),
        ];
        $response = $this->actingAs($user)->post(route('reviews.store', $store), $reviewData);
        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
        $reviewData['rating'] = 0;
        $response = $this->actingAs($user)->post(route('reviews.store', $store), $reviewData);
        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_review_post_fails_if_image_is_invalid_format(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $store = Store::factory()->create();
        Reservation::factory()->create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'reservation_datetime' => Carbon::now()->subDay(),
        ]);
        $reviewData = [
            'rating' => 5,
            'comment' => '不正画像フォーマット',
            'image' => UploadedFile::fake()->create('document.pdf'),
        ];
        $response = $this->actingAs($user)
                         ->post(route('reviews.store', $store), $reviewData);
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('reviews', [
            'comment' => $reviewData['comment'],
        ]);
    }

    public function test_authenticated_user_can_view_edit_page_for_their_review(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        $review = Review::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)
                         ->get(route('reviews.edit', $review));
        $response->assertStatus(200);
        $response->assertViewIs('reviews.edit');
        $response->assertSee($review->comment);
        $response->assertSee('value="' . $review->rating . '"' , false);
    }

    public function test_authenticated_user_cannot_view_edit_page_for_others_review(): void
    {
        $owner = User::factory()->create(['role' => 'general']);
        $otherUser = User::factory()->create(['role' => 'general']);
        $review = Review::factory()->create(['user_id' => $owner->id]);
        $response = $this->actingAs($otherUser)
                         ->get(route('reviews.edit', $review));
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_update_their_review_with_valid_data(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'image_path' => UploadedFile::fake()->image('old_review.jpg')->store('reviews', 'public')
        ]);
        $oldImagePath = $review->image_path;
        $store = $review->store;
        $newImageData = UploadedFile::fake()->image('new_review.png');
        $updatedData = [
            'rating' => 4,
            'comment' => '編集後のコメントです。',
            'image' => $newImageData,
        ];
        $response = $this->actingAs($user)
                         ->put(route('reviews.update', $review), $updatedData);
        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => $updatedData['rating'],
            'comment' => $updatedData['comment'],
        ]);
        $updatedReview = $review->fresh();
        $this->assertNotNull($updatedReview->image_path);
        $this->assertNotEquals($oldImagePath, $updatedReview->image_path);
        Storage::disk('public')->assertExists($updatedReview->image_path);
        Storage::disk('public')->assertMissing($oldImagePath);
    }

    public function test_authenticated_user_can_update_their_review_without_changing_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $initialImagePath = UploadedFile::fake()->image('initial_review.jpg')->store('reviews', 'public');
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'image_path' => $initialImagePath
        ]);
        $store = $review->store;
        $updatedData = [
            'rating' => 3,
            'comment' => '画像は変更せずにコメントだけ更新。',
        ];
        $response = $this->actingAs($user)
                         ->put(route('reviews.update', $review), $updatedData);
        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => $updatedData['rating'],
            'comment' => $updatedData['comment'],
            'image_path' => $initialImagePath,
        ]);
        Storage::disk('public')->assertExists($initialImagePath);
    }

    public function test_authenticated_user_cannot_update_others_review(): void
    {
        $owner = User::factory()->create(['role' => 'general']);
        $otherUser = User::factory()->create(['role' => 'general']);
        $review = Review::factory()->create(['user_id' => $owner->id]);
        $originalComment = $review->comment;
        $updatedData = [
            'rating' => 1,
            'comment' => '他人が更新しようとしたコメント。',
        ];
        $response = $this->actingAs($otherUser)
                         ->put(route('reviews.update', $review), $updatedData);
        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'comment' => $originalComment,
        ]);
    }

    public function test_review_update_fails_with_invalid_data(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        $review = Review::factory()->create(['user_id' => $user->id]);
        $originalComment = $review->comment;
        $invalidData = [
            'rating' => 5,
            'comment' => str_repeat('b', 401),
        ];
        $response = $this->actingAs($user)
                         ->put(route('reviews.update', $review), $invalidData);
        $response->assertSessionHasErrors('comment');
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'comment' => $originalComment,
        ]);
    }

    public function test_authenticated_user_can_delete_their_review(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'general']);
        $imagePath = UploadedFile::fake()->image('to_be_deleted.jpg')->store('reviews', 'public');
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'image_path' => $imagePath
            ]);
        $store = $review->store;
        $response = $this->actingAs($user)
                         ->delete(route('reviews.destroy', $review));
        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_authenticated_user_cannot_delete_others_review(): void
    {
        $owner = User::factory()->create(['role' => 'general']);
        $otherUser = User::factory()->create(['role' => 'general']);
        $review = Review::factory()->create(['user_id' => $owner->id]);
        $response = $this->actingAs($otherUser)
                         ->delete(route('reviews.destroy', $review));
        $response->assertStatus(403);
        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    public function test_admin_user_can_delete_any_review(): void
    {
        Storage::fake('public');
        $generalUser = User::factory()->create(['role' => 'general']);
        $admin = User::factory()->create(['role' => 'admin']);
        $imagePath = UploadedFile::fake()->image('admin_deletes_this.jpg')->store('reviews', 'public');
        $review = Review::factory()->create([
            'user_id' => $generalUser->id,
            'image_path' => $imagePath
            ]);
        $store = $review->store;
        $response = $this->actingAs($admin)
                         ->delete(route('reviews.destroy', $review));
        $response->assertRedirect(route('stores.show', $store));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }
}