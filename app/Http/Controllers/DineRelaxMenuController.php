<?php

namespace App\Http\Controllers;

use App\Models\DineRelaxMenu;
use App\Models\DineRelaxMenuTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DineRelaxMenuController extends Controller
{
    public function index()
    {
        $menus = DineRelaxMenu::with('translations')->orderBy('display_order')->get();

        return view('admin.dine-relax.menus-index', [
            'menus' => $menus,
        ]);
    }

    public function create()
    {
        return view('admin.dine-relax.menus-create');
    }

    public function edit(string $type)
    {
        $menu = DineRelaxMenu::where('type', $type)->with('translations')->firstOrFail();

        return view('admin.dine-relax.menus-edit', [
            'menu' => $menu,
        ]);
    }

    public function store(Request $request)
    {
        // Extract category names from form data
        $typeDisplay = $request->input('type');
        $typeFr = $request->input('type_fr');

        // Validate the inputs
        $request->validate([
            'type' => 'required|string|max:100',
            'type_fr' => 'required|string|max:100',
        ]);

        // Create a slug-friendly key from the English name for the database 'type' column
        $typeKey = Str::slug($typeDisplay, '-');

        // Override the form 'type' with display name, keep in request for translations
        $request->merge(['type' => $typeDisplay]);

        // Call storeOrUpdate with the slug key
        return $this->storeOrUpdate($typeKey, $request);
    }

    public function storeOrUpdate(string $type, Request $request)
    {
        // Allow dynamic types (no guard needed)
        $request->validate([
            'type' => 'required|string|max:100',
            'type_fr' => 'required|string|max:100',
        ]);

        $validated = $request->validate([
            'file' => 'nullable|mimes:pdf|max:15360',
            'card_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'card_image_alt_en' => 'nullable|string|max:255',
            'card_image_alt_fr' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'description_en' => 'nullable|string|max:1000',
            'description_fr' => 'nullable|string|max:1000',
            'button_label_en' => 'required|string|max:100',
            'button_label_fr' => 'required|string|max:100',
            'version_note_en' => 'nullable|string|max:100',
            'version_note_fr' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        // Use the visible category name fields as titles to ensure updates apply
        $titleEnInput = $request->input('type');
        $titleFrInput = $request->input('type_fr');

        $menu = DineRelaxMenu::firstOrCreate(
            ['type' => $type],
            [
                'file_path' => null,
                'file_name' => null,
                'file_mime' => null,
                'card_image_path' => null,
                'card_image_alt' => null,
                'is_active' => true,
                'display_order' => 0,
            ]
        );

        $file = $request->file('file');

        if (! $file && ! $menu->file_path) {
            return back()->withErrors(['file' => 'PDF is required for this menu.'])->withInput();
        }

        $update = [
            'is_active' => $request->boolean('is_active', true),
            'display_order' => $menu->display_order ?? 0,
        ];

        $altEn = $request->input('card_image_alt_en');
        $altFr = $request->input('card_image_alt_fr');

        // Persist both EN and FR alt text; use EN as primary fallback
        $update['card_image_alt'] = $altEn ?: $altFr ?: null;
        $update['card_image_alt_fr'] = $altFr ?: null;

        if ($file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $storedPath = "dine-relax/menus/{$type}/{$filename}";
            Storage::disk('public')->putFileAs("dine-relax/menus/{$type}", $file, $filename);

            $update['file_path'] = $storedPath;
            $update['file_name'] = $file->getClientOriginalName();
            $update['file_mime'] = $file->getClientMimeType();
        }

        if ($cardImage = $request->file('card_image')) {
            $cardFilename = Str::uuid() . '.' . $cardImage->getClientOriginalExtension();
            $cardStored = "dine-relax/menus/{$type}/card/{$cardFilename}";
            Storage::disk('public')->putFileAs("dine-relax/menus/{$type}/card", $cardImage, $cardFilename);
            $update['card_image_path'] = $cardStored;
        }

        $menu->update($update);

        // Use category names as titles if titles are empty
        $titleEn = $titleEnInput !== null && $titleEnInput !== '' ? $titleEnInput : $request->input('type');
        $titleFr = $titleFrInput !== null && $titleFrInput !== '' ? $titleFrInput : $request->input('type_fr');

        foreach (['en', 'fr'] as $locale) {
            $title = $locale === 'en' ? $titleEn : $titleFr;
            DineRelaxMenuTranslation::updateOrCreate(
                ['dine_relax_menu_id' => $menu->id, 'locale' => $locale],
                [
                    'title' => $title,
                    'description' => $validated["description_{$locale}"] ?? null,
                    'button_label' => $validated["button_label_{$locale}"],
                    'version_note' => $validated["version_note_{$locale}"] ?? null,
                ]
            );
        }

        return back()->with('success', ucfirst($type) . ' menu saved.');
    }

    public function delete(string $type)
    {
        $menu = DineRelaxMenu::where('type', $type)->firstOrFail();

        // Delete files from storage
        if ($menu->file_path && Storage::disk('public')->exists($menu->file_path)) {
            Storage::disk('public')->delete($menu->file_path);
        }

        if ($menu->card_image_path && Storage::disk('public')->exists($menu->card_image_path)) {
            Storage::disk('public')->delete($menu->card_image_path);
        }

        // Delete translations
        $menu->translations()->delete();

        // Delete menu
        $menu->delete();

        return back()->with('success', ucfirst($type) . ' menu deleted.');
    }

    public function toggle(string $type, Request $request)
    {
        $menu = DineRelaxMenu::where('type', $type)->firstOrFail();

        $data = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $menu->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('success', ucfirst($type) . ' menu updated.');
    }

    public function download(string $type)
    {
        $menu = DineRelaxMenu::where('type', $type)
            ->where('is_active', true)
            ->firstOrFail();

        if (! Storage::disk('public')->exists($menu->file_path)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => $menu->file_mime,
        ];

        return Storage::disk('public')->download($menu->file_path, $menu->file_name, $headers);
    }
}
