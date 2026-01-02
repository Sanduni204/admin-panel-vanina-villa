<?php

namespace Database\Factories;

use App\Models\Villa;
use App\Models\VillaMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class VillaMediaFactory extends Factory
{
    protected $model = VillaMedia::class;

    public function definition(): array
    {
        return [
            'villa_id' => Villa::factory(),
            'image_path' => 'villas/' . $this->faker->uuid . '.jpg',
            'alt_text_en' => $this->faker->words(3, true),
            'alt_text_fr' => $this->faker->words(3, true),
            'position' => $this->faker->numberBetween(0, 10),
            'is_featured' => false,
        ];
    }
}
