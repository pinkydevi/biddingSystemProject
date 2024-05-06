<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class AuctionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $imageFiles = ['bike', 'canon', 'chair', 'guitar_elvis', 'house', 'ipad',
        'iphone', 'macbook', 'smartwatch', 'tools', 'typewriter', 'volkswagen'];

        $image = $imageFiles[rand(0, count($imageFiles)-1)];
        
        return [
            'title' => $this->faker->text(20),
            'description' => $this->faker->text(200),
            'image' => 'Images/' . $image . '.jpg',
            'min_price' => $this->faker->numberBetween(100, 1000),
            'current_price' => 0,
            'owner_id' => $this->faker->numberBetween(1, 3),
            'winner_id' => null,
            'start_time' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
        ];
    }
}