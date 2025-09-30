<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'items' => $this->faker->randomElements(
                ['burger', 'pizza', 'pasta', 'fries', 'sandwich', 'drinks'],
                $this->faker->numberBetween(1, 3)
            ),
            'pickup_time' => now()->addMinutes(30)->format('Y-m-d\TH:i:s\Z'),
            'is_vip' => false,
            'status' => 'active'
        ];
    }
}
