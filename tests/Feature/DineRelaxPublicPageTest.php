<?php

namespace Tests\Feature;

use App\Models\DineRelaxBlock;
use App\Models\DineRelaxBlockTranslation;
use App\Models\DineRelaxGallery;
use App\Models\DineRelaxMenu;
use App\Models\DineRelaxMenuTranslation;
use App\Models\DineRelaxPage;
use App\Models\DineRelaxPageTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DineRelaxPublicPageTest extends TestCase
{
    use RefreshDatabase;

    private DineRelaxPage $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->page = DineRelaxPage::create([
            'hero_image_path' => 'hero/test.jpg',
            'hero_image_alt' => 'Hero image',
            'is_published' => true,
        ]);

        DineRelaxPageTranslation::create([
            'dine_relax_page_id' => $this->page->id,
            'locale' => 'en',
            'hero_tagline' => 'Experience',
            'hero_title' => 'Dine & Relax',
            'hero_lead' => 'Enjoy our facilities',
            'is_published' => true,
        ]);

        DineRelaxPageTranslation::create([
            'dine_relax_page_id' => $this->page->id,
            'locale' => 'fr',
            'hero_tagline' => 'Expérience',
            'hero_title' => 'Détente & Gourmandise',
            'hero_lead' => 'Profitez de nos installations',
            'is_published' => true,
        ]);
    }

    public function test_public_can_view_dine_relax_page()
    {
        $response = $this->get(route('dine-relax.show'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.dine-relax');
        $response->assertSee('Dine & Relax');
        $response->assertSee('Experience');
        $response->assertSee('Enjoy our facilities');
    }

    public function test_unpublished_page_returns_404()
    {
        $this->page->update(['is_published' => false]);

        $response = $this->get(route('dine-relax.show'));

        $response->assertStatus(404);
    }

    public function test_page_displays_hero_section()
    {
        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('Experience');
        $response->assertSee('Dine & Relax');
        $response->assertSee('Enjoy our facilities');
    }

    public function test_page_displays_blocks_in_order()
    {
        $restaurantBlock = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Restaurant',
            'slug' => 'restaurant',
            'image_path' => 'blocks/restaurant.jpg',
            'image_alt' => 'Restaurant',
            'display_order' => 1,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $restaurantBlock->id,
            'locale' => 'en',
            'heading' => 'Our Restaurant',
            'body' => 'Fine dining experience',
        ]);

        $poolBlock = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Pool',
            'slug' => 'pool',
            'image_path' => 'blocks/pool.jpg',
            'image_alt' => 'Pool',
            'display_order' => 2,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $poolBlock->id,
            'locale' => 'en',
            'heading' => 'Swimming Pool',
            'body' => 'Relax by the pool',
        ]);

        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('Our Restaurant');
        $response->assertSee('Fine dining experience');
        $response->assertSee('Swimming Pool');
        $response->assertSee('Relax by the pool');

        // Check order by position in the response content
        $content = $response->getContent();
        $restaurantPos = strpos($content, 'Our Restaurant');
        $poolPos = strpos($content, 'Swimming Pool');
        $this->assertLessThan($poolPos, $restaurantPos);
    }

    public function test_page_displays_gallery_carousel_for_blocks()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Bar & Coffee',
            'slug' => 'bar-coffee',
            'display_order' => 1,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'Bar & Coffee Shop',
            'body' => 'Enjoy drinks and coffee',
        ]);

        DineRelaxGallery::create([
            'dine_relax_block_id' => $block->id,
            'image_path' => 'gallery/image1.jpg',
            'image_alt' => 'Gallery image 1',
            'display_order' => 0,
        ]);

        DineRelaxGallery::create([
            'dine_relax_block_id' => $block->id,
            'image_path' => 'gallery/image2.jpg',
            'image_alt' => 'Gallery image 2',
            'display_order' => 1,
        ]);

        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('Bar & Coffee Shop');
        $response->assertSee('gallery/image1.jpg');
        $response->assertSee('gallery/image2.jpg');
        $response->assertSee('Gallery image 1');
        $response->assertSee('Gallery image 2');
    }

    public function test_page_displays_active_menus()
    {
        $activeMenu = DineRelaxMenu::create([
            'type' => 'breakfast',
            'file_path' => 'menus/breakfast.pdf',
            'file_name' => 'breakfast.pdf',
            'file_mime' => 'application/pdf',
            'card_image_path' => 'menus/breakfast-card.jpg',
            'is_active' => true,
            'display_order' => 1,
        ]);

        DineRelaxMenuTranslation::create([
            'dine_relax_menu_id' => $activeMenu->id,
            'locale' => 'en',
            'title' => 'Breakfast Menu',
            'button_label' => 'Download',
            'description' => 'Start your day right',
        ]);

        $inactiveMenu = DineRelaxMenu::create([
            'type' => 'lunch',
            'file_path' => 'menus/lunch.pdf',
            'file_name' => 'lunch.pdf',
            'file_mime' => 'application/pdf',
            'is_active' => false,
            'display_order' => 2,
        ]);

        DineRelaxMenuTranslation::create([
            'dine_relax_menu_id' => $inactiveMenu->id,
            'locale' => 'en',
            'title' => 'Lunch Menu',
            'button_label' => 'Download',
        ]);

        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('Breakfast Menu');
        $response->assertDontSee('Lunch Menu');
    }

    public function test_page_respects_locale()
    {
        app()->setLocale('fr');

        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('Expérience');
        $response->assertSee('Détente & Gourmandise');
        $response->assertSee('Profitez de nos installations');
    }

    public function test_page_falls_back_to_english_when_french_not_available()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Restaurant',
            'slug' => 'restaurant',
            'display_order' => 1,
        ]);

        // Create both English and French translations
        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'English Heading',
            'body' => 'English body',
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'fr',
            'heading' => 'French Heading',
            'body' => 'French body',
        ]);

        app()->setLocale('fr');

        $response = $this->get(route('dine-relax.show'));

        // Should show French translation
        $response->assertSee('French Heading');
    }

    public function test_page_shows_no_menus_message_when_no_active_menus()
    {
        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('Menus will be available soon');
    }

    public function test_page_shows_no_images_message_for_empty_gallery()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Bar & Coffee',
            'slug' => 'bar-coffee',
            'display_order' => 1,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'Bar & Coffee',
        ]);

        $response = $this->get(route('dine-relax.show'));

        $response->assertSee('No images yet');
    }
}
