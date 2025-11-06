<?php

namespace Database\Factories;

use App\Models\Paket;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaketFactory extends Factory
{
    protected $model = Paket::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word() . ' Package',
            'price' => $this->faker->numberBetween(200000, 2000000),
            'category' => $this->faker->randomElement(['Basic', 'Premium', 'Enterprise']),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
