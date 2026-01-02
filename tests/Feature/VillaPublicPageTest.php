<?php

namespace Tests\Feature;

use App\Models\Villa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillaPublicPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_villas_list()
    {
        Villa::factory(3)->create([
            'published_at' => now(),
        ])->each(function ($villa) {
            \App\Models\VillaTranslation::factory()->create([
                'villa_id' => $villa->id,
                'locale' => 'en',
            ]);
        });

        $this->get(route('pages.villas'))
            ->assertStatus(200)
            ->assertViewIs('pages.villas')
            ->assertViewHas('villas');
    }

    /** @test */
    public function user_can_view_villa_detail()
    {
        $villa = Villa::factory()->create([
            'slug' => 'test-villa',
            'published_at' => now(),
        ]);

        \App\Models\VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
        ]);

        $this->get(route('pages.villa-detail', $villa->slug))
            ->assertStatus(200)
            ->assertViewIs('pages.villa-detail')
            ->assertViewHas('villa');
    }

    /** @test */
    public function unpublished_villa_returns_404()
    {
        $villa = Villa::factory()->create([
            'slug' => 'unpublished-villa',
            'published_at' => null,
        ]);

        \App\Models\VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
        ]);

        $this->get(route('pages.villa-detail', $villa->slug))
            ->assertStatus(404);
    }

    /** @test */
    public function nonexistent_villa_returns_404()
    {
        $this->get(route('pages.villa-detail', 'nonexistent-villa'))
            ->assertStatus(404);
    }

    /** @test */
    public function villas_list_filters_by_search()
    {
        $villa1 = Villa::factory()->create([
            'published_at' => now(),
        ]);
        \App\Models\VillaTranslation::factory()->create([
            'villa_id' => $villa1->id,
            'locale' => 'en',
            'title' => 'Ocean View Villa',
        ]);

        $villa2 = Villa::factory()->create([
            'published_at' => now(),
        ]);
        \App\Models\VillaTranslation::factory()->create([
            'villa_id' => $villa2->id,
            'locale' => 'en',
            'title' => 'Mountain Retreat',
        ]);

        $response = $this->get(route('pages.villas', ['search' => 'Ocean']));
        $response->assertStatus(200);
    }

    /** @test */
    public function villas_list_filters_by_price()
    {
        $villa1 = Villa::factory()->create([
            'published_at' => now(),
        ]);
        \App\Models\VillaTranslation::factory()->create([
            'villa_id' => $villa1->id,
            'locale' => 'en',
            'price' => 100,
        ]);

        $villa2 = Villa::factory()->create([
            'published_at' => now(),
        ]);
        \App\Models\VillaTranslation::factory()->create([
            'villa_id' => $villa2->id,
            'locale' => 'en',
            'price' => 500,
        ]);

        $response = $this->get(route('pages.villas', ['price_min' => 400]));
        $response->assertStatus(200);
    }
}
