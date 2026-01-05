<?php

namespace Tests\Feature;

use App\Models\DineRelaxBlock;
use App\Models\DineRelaxMenu;
use App\Models\DineRelaxPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DineRelaxAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_update_hero_and_blocks_and_gallery(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin']);

        // Get the seeded restaurant block
        $restaurantBlock = DineRelaxBlock::where('slug', 'restaurant')->first();
        $barBlock = DineRelaxBlock::where('slug', 'bar-coffee')->first();

        $heroImage = UploadedFile::fake()->create('hero.jpg', 50, 'image/jpeg');
        $blockImage = UploadedFile::fake()->create('restaurant.jpg', 50, 'image/jpeg');
        $galleryImage = UploadedFile::fake()->create('gallery.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($admin)
            ->post(route('dine-relax.update'), [
                'is_published' => true,
                'hero_title_en' => 'Hero EN',
                'hero_title_fr' => 'Hero FR',
                'hero_tagline_en' => 'Tag EN',
                'hero_tagline_fr' => 'Tag FR',
                'hero_lead_en' => 'Lead EN',
                'hero_lead_fr' => 'Lead FR',
                'meta_title_en' => 'Meta EN',
                'meta_title_fr' => 'Meta FR',
                'meta_description_en' => 'Desc EN',
                'meta_description_fr' => 'Desc FR',
                'hero_image' => $heroImage,
                'hero_image_alt' => 'Alt hero',
                'blocks' => [
                    [
                        'id' => $restaurantBlock->id,
                        'name' => 'Restaurant',
                        'display_order' => 1,
                        'heading_en' => 'Restaurant EN',
                        'heading_fr' => 'Restaurant FR',
                        'body_en' => 'Body EN',
                        'body_fr' => 'Body FR',
                        'hours_en' => '10-22',
                        'hours_fr' => '10-22',
                        'highlights_en' => "Fresh\nLocal",
                        'highlights_fr' => "Frais\nLocal",
                        'cta_label' => 'Book',
                        'cta_url' => 'https://example.com',
                        'image_alt' => 'Restaurant alt',
                        'image' => $blockImage,
                    ],
                ],
                'gallery' => [
                    [
                        'dine_relax_block_id' => $barBlock->id,
                        'image_alt' => 'Bar alt',
                        'display_order' => 2,
                        'image' => $galleryImage,
                    ],
                ],
            ]);

        $response->assertRedirect(route('dine-relax.edit'));
        $response->assertSessionHasNoErrors();

        $page = DineRelaxPage::first();
        $this->assertTrue($page->is_published);
        $this->assertEquals('Hero EN', $page->translation('en')->hero_title);

        // Refresh the restaurant block to get updated data
        $restaurant = $restaurantBlock->fresh();
        $this->assertEquals('Restaurant EN', $restaurant->translation('en')->heading);
        $this->assertEquals(['Fresh', 'Local'], $restaurant->translation('en')->highlights);

        Storage::disk('public')->assertExists($page->hero_image_path);
        Storage::disk('public')->assertExists($restaurant->image_path);

        $bar = $barBlock->fresh();
        $this->assertGreaterThan(0, $bar->gallery()->count());
        Storage::disk('public')->assertExists($bar->gallery()->first()->image_path);
    }

    public function test_admin_can_update_menu_metadata_without_pdf_and_toggle(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf');
        $card = UploadedFile::fake()->create('card.jpg', 50, 'image/jpeg');

        $saveResponse = $this->actingAs($admin)->post(route('dine-relax.menus.save', 'beverage'), [
            'file' => $pdf,
            'card_image' => $card,
            'card_image_alt' => 'Card alt',
            'title_en' => 'Beverage EN',
            'title_fr' => 'Beverage FR',
            'button_label_en' => 'Download EN',
            'button_label_fr' => 'Download FR',
            'version_note_en' => 'v1',
            'version_note_fr' => 'v1',
            'is_active' => true,
        ]);

        $saveResponse->assertSessionHasNoErrors();

        $menu = DineRelaxMenu::where('type', 'beverage')->first();
        Storage::disk('public')->assertExists($menu->file_path);
        Storage::disk('public')->assertExists($menu->card_image_path);

        $updateResponse = $this->actingAs($admin)->post(route('dine-relax.menus.save', 'beverage'), [
            'title_en' => 'Beverage EN 2',
            'title_fr' => 'Beverage FR 2',
            'button_label_en' => 'Download EN 2',
            'button_label_fr' => 'Download FR 2',
            'version_note_en' => 'v2',
            'version_note_fr' => 'v2',
            'card_image_alt' => 'Card alt',
            'is_active' => false,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $menu->refresh();
        $this->assertEquals('Beverage EN 2', $menu->translation('en')->title);
        $this->assertFalse($menu->is_active);
    }
}
