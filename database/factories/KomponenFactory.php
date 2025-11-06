<?php

namespace Database\Factories;

use App\Models\Komponen;
use Illuminate\Database\Eloquent\Factories\Factory;

class KomponenFactory extends Factory
{
    protected $model = Komponen::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->word() . ' Komponen',
            'harga' => $this->faker->numberBetween(10000, 500000),
            'kategori' => $this->faker->randomElement(['Camera', 'Sensor', 'Cable', 'Power Supply', 'Monitor']),
            'deskripsi' => $this->faker->sentence(),
            'gambar' => $this->faker->imageUrl(),
        ];
    }
}
