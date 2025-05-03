<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . '支店', // 店舗名
            'region' => $this->faker->randomElement(['東京都', '大阪府', '福岡県']), // 地域
            'genre' => $this->faker->randomElement(['寿司', '焼肉', 'イタリアン', '居酒屋', 'ラーメン']), // ジャンル
            'description' => $this->faker->realText(150), // 店舗概要
            'image_url' => $this->faker->imageUrl(640, 480, 'food'), // 画像URL
        ];
    }
}