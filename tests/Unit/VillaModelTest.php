<?php

namespace Tests\Unit;

use App\Models\Villa;
use App\Models\VillaMedia;
use App\Models\VillaTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function villa_has_many_translations()
    {
        $villa = Villa::factory()->create();
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
        ]);
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'fr',
        ]);

        $this->assertCount(2, $villa->translations);
    }

    /** @test */
    public function villa_has_many_media()
    {
        $villa = Villa::factory()->create();
        VillaMedia::factory(3)->create(['villa_id' => $villa->id]);

        $this->assertCount(3, $villa->media);
    }

    /** @test */
    public function get_translation_returns_locale_specific_translation()
    {
        $villa = Villa::factory()->create();
        $enTranslation = VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);
        $frTranslation = VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'fr',
            'title' => 'French Title',
        ]);

        $this->assertEquals('English Title', $villa->getTranslation('en')->title);
        $this->assertEquals('French Title', $villa->getTranslation('fr')->title);
    }

    /** @test */
    public function get_translation_fallback_to_english()
    {
        $villa = Villa::factory()->create();
        VillaTranslation::factory()->create([
            'villa_id' => $villa->id,
            'locale' => 'en',
            'title' => 'English Title',
        ]);

        $translation = $villa->getTranslation('de');
        $this->assertEquals('English Title', $translation->title);
    }

    /** @test */
    public function published_scope_filters_published_villas()
    {
        Villa::factory()->create(['published_at' => now()]);
        Villa::factory()->create(['published_at' => null]);

        $published = Villa::published()->count();
        $this->assertEquals(1, $published);
    }

    /** @test */
    public function featured_scope_filters_featured_villas()
    {
        Villa::factory()->create(['featured' => true]);
        Villa::factory()->create(['featured' => false]);

        $featured = Villa::featured()->count();
        $this->assertEquals(1, $featured);
    }

    /** @test */
    public function ordered_scope_sorts_by_display_order()
    {
        Villa::factory()->create(['display_order' => 2]);
        Villa::factory()->create(['display_order' => 1]);
        Villa::factory()->create(['display_order' => 3]);

        $villas = Villa::ordered()->get();
        $this->assertEquals(1, $villas[0]->display_order);
        $this->assertEquals(2, $villas[1]->display_order);
        $this->assertEquals(3, $villas[2]->display_order);
    }

    /** @test */
    public function villa_can_be_soft_deleted()
    {
        $villa = Villa::factory()->create();
        $villa->delete();

        $this->assertSoftDeleted('villas', ['id' => $villa->id]);
    }
}
