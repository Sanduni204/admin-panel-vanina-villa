<?php

namespace Database\Factories;

use App\Models\Villa;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VillaFactory extends Factory
{
    protected $model = Villa::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true);

        return [
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(5),
            'featured' => $this->faker->boolean(),
            'display_order' => $this->faker->randomNumber(),
            'published_at' => $this->faker->boolean() ? now() : null,
        ];
    }
}
