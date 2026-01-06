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
        $this->withoutMiddleware([
            \App\Http\Middleware\RoleMiddleware::class,
            \App\Http\Middleware\AdminOnly::class,
            \App\Http\Middleware\LogAdminActivity::class,
        ]);
        Storage::fake('public');
    }

    public function test_admin_can_update_hero_and_blocks_and_gallery(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin']);

        // Get the seeded restaurant block
        $restaurantBlock = DineRelaxBlock::where('slug', 'restaurant')->first();

        $heroImage = UploadedFile::fake()->create('hero.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($admin)
            ->put(route('dine-relax.hero.update'), [
                'hero_title_en' => 'Hero EN',
                'hero_title_fr' => 'Hero FR',
                'hero_tagline_en' => 'Tag EN',
                'hero_tagline_fr' => 'Tag FR',
                'hero_lead_en' => 'Lead EN',
                'hero_lead_fr' => 'Lead FR',
                'hero_image' => $heroImage,
                'hero_image_alt' => 'Alt hero',
            ]);

        $response->assertRedirect(route('dine-relax.edit'));
        $response->assertSessionHasNoErrors();

        $page = DineRelaxPage::first();
        $this->assertEquals('Hero EN', $page->translation('en')->hero_title);

        // Test block update separately
        $blockImage = UploadedFile::fake()->create('restaurant.jpg', 50, 'image/jpeg');

        $blockResponse = $this->actingAs($admin)->put(route('dine-relax.blocks.update', $restaurantBlock->id), [
            'name' => 'Restaurant',
            'heading_en' => 'Restaurant',
            'heading_fr' => 'Restaurant FR',
            'body_en' => 'Body EN',
            'body_fr' => 'Body FR',
            'image' => $blockImage,
            'image_alt' => 'Restaurant alt',
            'display_order' => 1,
        ]);

        $blockResponse->assertRedirect(route('dine-relax.edit'));

        $restaurant = $restaurantBlock->fresh();
        $this->assertEquals('Restaurant', $restaurant->translation('en')->heading);

        Storage::disk('public')->assertExists($page->hero_image_path);
        Storage::disk('public')->assertExists($restaurant->image_path);
    }

    public function test_admin_can_update_menu_metadata_without_pdf_and_toggle(): void
    {
        $this->seed();
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf');
        $card = UploadedFile::fake()->create('card.jpg', 50, 'image/jpeg');

        $saveResponse = $this->actingAs($admin)->post(route('dine-relax.menus.save', 'beverage'), [
            'type' => 'Beverage EN',
            'type_fr' => 'Beverage FR',
            'file' => $pdf,
            'card_image' => $card,
            'card_image_alt_en' => 'Card alt',
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
            'type' => 'Beverage EN 2',
            'type_fr' => 'Beverage FR 2',
            'button_label_en' => 'Download EN 2',
            'button_label_fr' => 'Download FR 2',
            'version_note_en' => 'v2',
            'version_note_fr' => 'v2',
            'card_image_alt_en' => 'Card alt',
            'is_active' => false,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $menu->refresh();
        $this->assertEquals('Beverage EN 2', $menu->translation('en')->title);
        $this->assertFalse($menu->is_active);
    }
}
