<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Store;
use App\Models\Review;

class StoreSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_index_page_is_accessible(): void
    {
        $response = $this->get(route('stores.index'));
        $response->assertStatus(200);
        $response->assertViewIs('stores.index');
    }

    public function test_stores_are_sorted_by_rating_descending(): void
    {
        $storeA = Store::factory()->create(['name' => 'Store A - High Rating']);
        $storeB = Store::factory()->create(['name' => 'Store B - Mid Rating']);
        $storeC = Store::factory()->create(['name' => 'Store C - No Reviews']);
        $storeD = Store::factory()->create(['name' => 'Store D - Low Rating']);
        Review::factory()->count(3)->create(['store_id' => $storeA->id, 'rating' => 5]);
        Review::factory()->count(2)->create(['store_id' => $storeB->id, 'rating' => 4]);
        Review::factory()->count(1)->create(['store_id' => $storeD->id, 'rating' => 2]);
        $storeA->updateAverageRating();
        $storeB->updateAverageRating();
        $storeC->updateAverageRating();
        $storeD->updateAverageRating();

        $response = $this->get(route('stores.index', ['sort' => 'rating_desc']));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Store A - High Rating',
            'Store B - Mid Rating',
            'Store D - Low Rating',
            'Store C - No Reviews',
        ]);
    }

    public function test_stores_are_sorted_by_rating_ascending(): void
    {
        $storeA = Store::factory()->create(['name' => 'Store A - High Rating']);
        $storeB = Store::factory()->create(['name' => 'Store B - Mid Rating']);
        $storeC = Store::factory()->create(['name' => 'Store C - No Reviews']);
        $storeD = Store::factory()->create(['name' => 'Store D - Low Rating']);
        Review::factory()->count(3)->create(['store_id' => $storeA->id, 'rating' => 5]);
        Review::factory()->count(2)->create(['store_id' => $storeB->id, 'rating' => 4]);
        Review::factory()->count(1)->create(['store_id' => $storeD->id, 'rating' => 2]);
        $storeA->updateAverageRating();
        $storeB->updateAverageRating();
        $storeC->updateAverageRating();
        $storeD->updateAverageRating();

        $response = $this->get(route('stores.index', ['sort' => 'rating_asc']));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'Store D - Low Rating',
            'Store B - Mid Rating',
            'Store A - High Rating',
            'Store C - No Reviews',
        ]);
    }

    public function test_store_card_displays_average_rating_and_review_count(): void
    {
        $store = Store::factory()->create(['name' => 'Rating Test Store']);
        Review::factory()->count(5)->create(['store_id' => $store->id, 'rating' => 4]);
        $store->updateAverageRating();
        $response = $this->get(route('stores.index'));
        $response->assertStatus(200);
        $response->assertSee('Rating Test Store');
        $response->assertSee('4.0');
        $response->assertSee('5');
    }

    public function test_average_rating_updates_after_new_review(): void
    {
        $store = Store::factory()->create(['name' => 'Update Rating Store']);
        Review::factory()->create(['store_id' => $store->id, 'rating' => 3]);
        $store->updateAverageRating();
        $response1 = $this->get(route('stores.index'));
        $response1->assertStatus(200);
        $response1->assertSee('3.0');
        $response1->assertSee('1');
        $user = User::factory()->create();
        Review::factory()->create(['store_id' => $store->id, 'user_id' => $user->id, 'rating' => 5]);
        $store->updateAverageRating();
        $response2 = $this->get(route('stores.index'));
        $response2->assertStatus(200);
        $response2->assertSee('4.0');
        $response2->assertSee('2');
    }

    public function test_authenticated_user_can_sort_by_favorites(): void
    {
        $user = User::factory()->create();
        $storeA = Store::factory()->create(['name' => 'Favorite Store A']);
        $storeB = Store::factory()->create(['name' => 'Non-Favorite Store B']);
        $storeC = Store::factory()->create(['name' => 'Favorite Store C']);
        $user->favorites()->attach([$storeA->id, $storeC->id]);
        $response = $this->actingAs($user)
                         ->get(route('stores.index', ['sort' => 'favorites']));
        $response->assertStatus(200);
         $stores = $response->viewData('stores');
         $storeNames = $stores->pluck('name')->toArray();
         $this->assertTrue(in_array('Favorite Store A', array_slice($storeNames, 0, 2)));
         $this->assertTrue(in_array('Favorite Store C', array_slice($storeNames, 0, 2)));
         $this->assertEquals('Non-Favorite Store B', $storeNames[2]);
    }
} 