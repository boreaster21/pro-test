<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . '支店',
            'region' => $this->faker->randomElement(['東京都', '大阪府', '福岡県']),
            'genre' => $this->faker->randomElement(['寿司', '焼肉', 'イタリアン', '居酒屋', 'ラーメン']),
            'description' => $this->faker->realText(150),
            'image_url' => $this->faker->imageUrl(640, 480, 'food'),
        ];
    }
}