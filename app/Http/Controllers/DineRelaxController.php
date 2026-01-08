<?php

namespace App\Http\Controllers;

use App\Models\DineRelaxBlock;
use App\Models\DineRelaxBlockTranslation;
use App\Models\DineRelaxGallery;
use App\Models\DineRelaxPage;
use App\Models\DineRelaxPageTranslation;
use App\Models\DineRelaxMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DineRelaxController extends Controller
{
    /**
     * Show the Dine & Relax hero edit page with blocks list.
     */
    public function edit()
    {
        $page = DineRelaxPage::with(['translations', 'blocks.translations'])->first();

        if (! $page) {
            $page = DineRelaxPage::create();
        }

        $blocks = $page->blocks()->with('translations')->orderBy('display_order')->get();

        return view('admin.dine-relax.edit', [
            'page' => $page,
            'blocks' => $blocks,
        ]);
    }

    /**
     * Show hero edit form.
     */
    public function heroEdit()
    {
        $page = DineRelaxPage::with('translations')->firstOrCreate([]);
        return view('admin.dine-relax.hero-form', [
            'page' => $page,
        ]);
    }

    /**
     * Update hero section.
     */
    public function heroUpdate(Request $request)
    {
        $validated = $request->validate([
            'hero_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'hero_image_alt' => 'required_with:hero_image|string|max:255',
            'hero_title_en' => 'required|string|max:255',
            'hero_title_fr' => 'required|string|max:255',
            'hero_tagline_en' => 'nullable|string|max:255',
            'hero_tagline_fr' => 'nullable|string|max:255',
            'hero_lead_en' => 'nullable|string',
            'hero_lead_fr' => 'nullable|string',
        ]);

        $page = DineRelaxPage::firstOrCreate([]);

        DB::transaction(function () use ($request, $page) {
            $this->updatePage($page, $request);
            $this->updateTranslations($page, $request);
        });

        return redirect()->route('dine-relax.edit')->with('success', 'Hero section updated successfully.');
    }

    /**
     * Create/update a common bilingual description for the Menus section.
     */
    public function menuInfoUpdate(Request $request)
    {
        $request->validate([
            'menus_description_en' => 'nullable|string',
            'menus_description_fr' => 'nullable|string',
            'clear' => 'sometimes|boolean',
        ]);

        $page = DineRelaxPage::firstOrCreate([]);

        foreach (['en', 'fr'] as $locale) {
            $value = $request->boolean('clear') ? null : $request->input("menus_description_{$locale}");

            // Get existing translation or create new one
            $translation = $page->translations()->where('locale', $locale)->first();

            if ($translation) {
                $translation->update(['menus_description' => $value]);
            } else {
                // Create with required fields if it doesn't exist
                DineRelaxPageTranslation::create([
                    'dine_relax_page_id' => $page->id,
                    'locale' => $locale,
                    'hero_title' => '',
                    'heading' => '',
                    'menus_description' => $value,
                ]);
            }
        }

        return back()->with('success', $request->boolean('clear') ? 'Menus description cleared.' : 'Menus description saved.');
    }

    /**
     * Show form to create a new block.
     */
    public function blockCreate()
    {
        $page = DineRelaxPage::firstOrCreate([]);
        return view('admin.dine-relax.block-form', [
            'page' => $page,
            'block' => null,
        ]);
    }

    /**
     * Show form to edit a block.
     */
    public function blockEdit($blockId)
    {
        $block = DineRelaxBlock::with('translations', 'gallery')->findOrFail($blockId);
        return view('admin.dine-relax.block-form', [
            'page' => $block->page,
            'block' => $block,
        ]);
    }

    /**
     * Store a new block or update existing block.
     */
    public function blockStore(Request $request, $blockId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'heading_en' => 'required|string|max:255',
            'heading_fr' => 'required|string|max:255',
            'body_en' => 'nullable|string',
            'body_fr' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'image_alt' => 'required_with:image|string|max:255',
            'display_order' => 'nullable|integer',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,webp|max:5120',
            'gallery_images_alt' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);
        $page = DineRelaxPage::firstOrCreate([]);

        if ($blockId) {
            // Update existing block
            $block = DineRelaxBlock::findOrFail($blockId);
        } else {
            // Create new block
            $slug = $this->generateUniqueSlug($page, $validated['name']);
            $block = DineRelaxBlock::create([
                'dine_relax_page_id' => $page->id,
                'slug' => $slug,
                'name' => $validated['name'],
            ]);
        }

        // Update block data
        $updateData = [
            'name' => $validated['name'],
            'display_order' => $validated['display_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            $updateData['image_path'] = $this->storeImage($request->file('image'), "blocks/{$block->slug}");
            $updateData['image_alt'] = $validated['image_alt'];
        }

        $block->update($updateData);

        // Update translations
        foreach (['en', 'fr'] as $locale) {
            DineRelaxBlockTranslation::updateOrCreate(
                ['dine_relax_block_id' => $block->id, 'locale' => $locale],
                [
                    'heading' => $validated["heading_{$locale}"],
                    'body' => $validated["body_{$locale}"] ?? null,
                ]
            );
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryAlt = $validated['gallery_images_alt'] ?? 'Gallery image';
            $existingCount = $block->gallery()->count();

            foreach ($request->file('gallery_images') as $index => $file) {
                $imagePath = $this->storeImage($file, "blocks/{$block->slug}/gallery");

                DineRelaxGallery::create([
                    'dine_relax_block_id' => $block->id,
                    'image_path' => $imagePath,
                    'image_alt' => $galleryAlt,
                    'display_order' => $existingCount + $index,
                ]);
            }
        }

        return redirect()->route('dine-relax.edit')->with('success', 'Block saved successfully.');
    }

    /**
     * Delete a block.
     */
    public function blockDelete($blockId)
    {
        $block = DineRelaxBlock::findOrFail($blockId);
        $block->delete();
        return redirect()->route('dine-relax.edit')->with('success', 'Block deleted successfully.');
    }

    /**
     * Update hero section only.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'hero_image_alt' => 'required_with:hero_image|string|max:255',
            'hero_title_en' => 'required|string|max:255',
            'hero_title_fr' => 'required|string|max:255',
            'hero_tagline_en' => 'nullable|string|max:255',
            'hero_tagline_fr' => 'nullable|string|max:255',
            'hero_lead_en' => 'nullable|string',
            'hero_lead_fr' => 'nullable|string',
        ]);

        $page = DineRelaxPage::firstOrCreate([]);

        DB::transaction(function () use ($request, $page) {
            $this->updatePage($page, $request);
            $this->updateTranslations($page, $request);
        });

        return redirect()->route('dine-relax.edit')->with('success', 'Hero section updated.');
    }


    private function updatePage(DineRelaxPage $page, Request $request): void
    {
        $data = [
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->hasFile('hero_image')) {
            $data['hero_image_path'] = $this->storeImage($request->file('hero_image'), 'hero');
            $data['hero_image_alt'] = $request->input('hero_image_alt');
        }

        if ($request->hasFile('meta_image')) {
            $data['meta_image_path'] = $this->storeImage($request->file('meta_image'), 'meta');
        }

        $page->update($data);
    }

    private function updateTranslations(DineRelaxPage $page, Request $request): void
    {
        foreach (['en', 'fr'] as $locale) {
            DineRelaxPageTranslation::updateOrCreate(
                ['dine_relax_page_id' => $page->id, 'locale' => $locale],
                [
                    'hero_tagline' => $request->input("hero_tagline_{$locale}"),
                    'hero_title' => $request->input("hero_title_{$locale}"),
                    'hero_lead' => $request->input("hero_lead_{$locale}"),
                    'meta_title' => $request->input("meta_title_{$locale}"),
                    'meta_description' => $request->input("meta_description_{$locale}"),
                    'is_published' => $request->boolean("hero_publish_{$locale}"),
                ]
            );
        }
    }

    /**
     * Generate a unique slug from a block name
     */
    private function generateUniqueSlug(DineRelaxPage $page, string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        // Check for duplicates on this page
        while (DineRelaxBlock::where('dine_relax_page_id', $page->id)
                    ->where('slug', $slug)
                    ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function storeImage($file, string $path): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $fullPath = "dine-relax/{$path}/{$filename}";
        Storage::disk('public')->putFileAs("dine-relax/{$path}", $file, $filename);
        return $fullPath;
    }
}
