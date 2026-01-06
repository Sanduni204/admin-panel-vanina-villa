<?php

namespace Tests\Feature;

use App\Models\DineRelaxBlock;
use App\Models\DineRelaxBlockTranslation;
use App\Models\DineRelaxGallery;
use App\Models\DineRelaxPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DineRelaxBlockTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private DineRelaxPage $page;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\RoleMiddleware::class,
            \App\Http\Middleware\AdminOnly::class,
            \App\Http\Middleware\LogAdminActivity::class,
        ]);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->page = DineRelaxPage::create([
            'is_published' => true,
        ]);
        Storage::fake('public');
    }

    public function test_admin_can_view_dine_relax_edit_page()
    {
        $response = $this->actingAs($this->admin)->get(route('dine-relax.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dine-relax.edit');
        $response->assertViewHas('page');
        $response->assertViewHas('blocks');
    }

    public function test_admin_can_view_block_create_form()
    {
        $response = $this->actingAs($this->admin)->get(route('dine-relax.blocks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dine-relax.block-form');
        $response->assertViewHas('page');
    }

    public function test_admin_can_create_new_block()
    {
        $imageFile = UploadedFile::fake()->create('block-image.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('dine-relax.blocks.store'), [
            'name' => 'Test Block',
            'heading_en' => 'Test Heading EN',
            'heading_fr' => 'Test Heading FR',
            'body_en' => 'Test body content in English',
            'body_fr' => 'Test body content in French',
            'image' => $imageFile,
            'image_alt' => 'Test image alt text',
            'display_order' => 1,
        ]);

        $response->assertRedirect(route('dine-relax.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dine_relax_blocks', [
            'name' => 'Test Block',
            'slug' => 'test-block',
            'display_order' => 1,
        ]);

        $block = DineRelaxBlock::where('slug', 'test-block')->first();
        $this->assertNotNull($block->image_path);
        Storage::disk('public')->assertExists($block->image_path);

        $this->assertDatabaseHas('dine_relax_block_translations', [
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'Test Heading EN',
            'body' => 'Test body content in English',
        ]);

        $this->assertDatabaseHas('dine_relax_block_translations', [
            'dine_relax_block_id' => $block->id,
            'locale' => 'fr',
            'heading' => 'Test Heading FR',
            'body' => 'Test body content in French',
        ]);
    }

    public function test_admin_can_view_block_edit_form()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Edit Test Block',
            'slug' => 'edit-test-block',
            'display_order' => 1,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'Edit Test Heading',
        ]);

        $response = $this->actingAs($this->admin)->get(route('dine-relax.blocks.edit', $block->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dine-relax.block-form');
        $response->assertViewHas('block', function ($viewBlock) use ($block) {
            return $viewBlock->id === $block->id;
        });
    }

    public function test_admin_can_update_existing_block()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Original Block',
            'slug' => 'original-block',
            'display_order' => 1,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'Original Heading',
        ]);

        $response = $this->actingAs($this->admin)->put(route('dine-relax.blocks.update', $block->id), [
            'name' => 'Updated Block',
            'heading_en' => 'Updated Heading EN',
            'heading_fr' => 'Updated Heading FR',
            'body_en' => 'Updated body EN',
            'body_fr' => 'Updated body FR',
            'display_order' => 2,
        ]);

        $response->assertRedirect(route('dine-relax.edit'));

        $block->refresh();
        $this->assertEquals('Updated Block', $block->name);
        $this->assertEquals(2, $block->display_order);

        $translation = $block->translations()->where('locale', 'en')->first();
        $this->assertEquals('Updated Heading EN', $translation->heading);
        $this->assertEquals('Updated body EN', $translation->body);
    }

    public function test_admin_can_delete_block()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Block to Delete',
            'slug' => 'block-to-delete',
            'display_order' => 1,
        ]);

        DineRelaxBlockTranslation::create([
            'dine_relax_block_id' => $block->id,
            'locale' => 'en',
            'heading' => 'Delete Test',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('dine-relax.blocks.delete', $block->id));

        $response->assertRedirect(route('dine-relax.edit'));
        $this->assertDatabaseMissing('dine_relax_blocks', ['id' => $block->id]);
        $this->assertDatabaseMissing('dine_relax_block_translations', ['dine_relax_block_id' => $block->id]);
    }

    public function test_admin_can_upload_gallery_images_to_block()
    {
        $block = DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Gallery Block',
            'slug' => 'gallery-block',
            'display_order' => 1,
        ]);

        $images = [
            UploadedFile::fake()->create('gallery1.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('gallery2.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('gallery3.jpg', 100, 'image/jpeg'),
        ];

        $response = $this->actingAs($this->admin)->put(route('dine-relax.blocks.update', $block->id), [
            'name' => 'Gallery Block',
            'heading_en' => 'Gallery Test',
            'heading_fr' => 'Test Galerie',
            'gallery_images' => $images,
            'gallery_images_alt' => 'Gallery images alt text',
        ]);

        $response->assertRedirect(route('dine-relax.edit'));

        $this->assertCount(3, $block->gallery);

        foreach ($block->gallery as $index => $galleryItem) {
            $this->assertNotNull($galleryItem->image_path);
            $this->assertEquals('Gallery images alt text', $galleryItem->image_alt);
            $this->assertEquals($index, $galleryItem->display_order);
            Storage::disk('public')->assertExists($galleryItem->image_path);
        }
    }

    public function test_block_validation_requires_name_and_headings()
    {
        $response = $this->actingAs($this->admin)->post(route('dine-relax.blocks.store'), [
            'name' => '',
            'heading_en' => '',
            'heading_fr' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'heading_en', 'heading_fr']);
    }

    public function test_block_slug_is_automatically_generated()
    {
        $response = $this->actingAs($this->admin)->post(route('dine-relax.blocks.store'), [
            'name' => 'My Special Block',
            'heading_en' => 'Special Heading',
            'heading_fr' => 'En-tête Spécial',
        ]);

        $this->assertDatabaseHas('dine_relax_blocks', [
            'name' => 'My Special Block',
            'slug' => 'my-special-block',
        ]);
    }

    public function test_blocks_are_ordered_by_display_order()
    {
        DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Block C',
            'slug' => 'block-c',
            'display_order' => 3,
        ]);

        DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Block A',
            'slug' => 'block-a',
            'display_order' => 1,
        ]);

        DineRelaxBlock::create([
            'dine_relax_page_id' => $this->page->id,
            'name' => 'Block B',
            'slug' => 'block-b',
            'display_order' => 2,
        ]);

        $blocks = $this->page->blocks()->orderBy('display_order')->get();

        $this->assertEquals('Block A', $blocks[0]->name);
        $this->assertEquals('Block B', $blocks[1]->name);
        $this->assertEquals('Block C', $blocks[2]->name);
    }
}
