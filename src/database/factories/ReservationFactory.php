<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Carbon;

class ReservationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reservationDateTime = $this->faker->dateTimeBetween('-1 year', '+1 year');

        $reservationDateTime->setTime(
            $reservationDateTime->format('H'),
            ceil($reservationDateTime->format('i') / 15) * 15,
            0
        );

        return [
            'user_id' => User::factory(),
            'store_id' => Store::factory(),
            'reservation_datetime' => $reservationDateTime,
            'number_of_people' => $this->faker->numberBetween(1, 10),
        ];
    }
} 