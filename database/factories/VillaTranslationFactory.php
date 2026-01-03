<?php

namespace Database\Factories;

use App\Models\Villa;
use App\Models\VillaTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class VillaTranslationFactory extends Factory
{
    protected $model = VillaTranslation::class;

    public function definition(): array
    {
        return [
            'villa_id' => Villa::factory(),
            'locale' => $this->faker->randomElement(['en', 'fr']),
            'title' => $this->faker->words(4, true),
            'description' => $this->faker->paragraphs(3, true),
            'amenities' => implode("\n", $this->faker->words(10)),
            'rules' => implode("\n", $this->faker->words(8)),
            'price' => $this->faker->randomFloat(2, 100, 500),
            'max_guests' => $this->faker->numberBetween(2, 20),
            'min_guests' => $this->faker->numberBetween(1, 4),
            'bathrooms' => $this->faker->randomFloat(1, 1, 5),
        ];
    }
}
