<?php

namespace Tests\Feature;

use App\Models\DineRelaxMenu;
use App\Models\DineRelaxPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DineRelaxPublicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_public_page_requires_published_page_and_locale(): void
    {
        $this->seed();
        $page = DineRelaxPage::first();
        $page->update(['is_published' => true]);
        $page->translations()->where('locale', 'en')->update(['is_published' => true]);

        $response = $this->get(route('dine-relax.show'));
        $response->assertStatus(200);

        $page->translations()->update(['is_published' => false]);
        $response = $this->get(route('dine-relax.show'));
        $response->assertStatus(404);
    }

    public function test_signed_download_serves_active_pdf(): void
    {
        $this->seed();
        $page = DineRelaxPage::first();
        $page->update(['is_published' => true]);
        $page->translations()->where('locale', 'en')->update(['is_published' => true]);

        $menu = DineRelaxMenu::firstOrCreate(['type' => 'beverage']);
        $pdf = UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf');
        Storage::disk('public')->putFileAs('dine-relax/menus/beverage', $pdf, 'menu.pdf');
        $menu->update([
            'file_path' => 'dine-relax/menus/beverage/menu.pdf',
            'file_name' => 'menu.pdf',
            'file_mime' => 'application/pdf',
            'is_active' => true,
        ]);

        $url = \Illuminate\Support\Facades\URL::signedRoute('dine-relax.menu.download', ['type' => 'beverage']);

        $response = $this->get($url);
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
    }
}
