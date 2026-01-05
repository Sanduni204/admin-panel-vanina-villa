<?php

namespace App\Http\Controllers;

use App\Models\DineRelaxMenu;
use App\Models\DineRelaxMenuTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DineRelaxMenuController extends Controller
{
    private array $types = ['beverage', 'snacking', 'today', 'breakfast'];

    public function index()
    {
        $menus = DineRelaxMenu::with('translations')->orderBy('display_order')->get();

        return view('admin.dine-relax.menus-index', [
            'menus' => $menus,
            'types' => $this->types,
        ]);
    }

    public function storeOrUpdate(string $type, Request $request)
    {
        $this->guardType($type);

        $validated = $request->validate([
            'file' => 'nullable|mimes:pdf|max:15360',
            'card_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'card_image_alt' => 'required_with:card_image|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_fr' => 'required|string|max:255',
            'button_label_en' => 'required|string|max:100',
            'button_label_fr' => 'required|string|max:100',
            'version_note_en' => 'nullable|string|max:100',
            'version_note_fr' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $menu = DineRelaxMenu::firstOrCreate(['type' => $type]);

        $file = $request->file('file');

        if (! $file && ! $menu->file_path) {
            return back()->withErrors(['file' => 'PDF is required for this menu.'])->withInput();
        }

        $update = [
            'is_active' => $request->boolean('is_active', true),
            'display_order' => $menu->display_order ?? 0,
        ];

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
            $update['card_image_alt'] = $request->input('card_image_alt');
        }

        $menu->update($update);

        foreach (['en', 'fr'] as $locale) {
            DineRelaxMenuTranslation::updateOrCreate(
                ['dine_relax_menu_id' => $menu->id, 'locale' => $locale],
                [
                    'title' => $validated["title_{$locale}"],
                    'button_label' => $validated["button_label_{$locale}"],
                    'version_note' => $validated["version_note_{$locale}"] ?? null,
                ]
            );
        }

        return back()->with('success', ucfirst($type) . ' menu saved.');
    }

    public function toggle(string $type, Request $request)
    {
        $this->guardType($type);

        $menu = DineRelaxMenu::where('type', $type)->firstOrFail();

        $data = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $menu->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('success', ucfirst($type) . ' menu updated.');
    }

    public function download(string $type)
    {
        $this->guardType($type);

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

    private function guardType(string $type): void
    {
        if (! in_array($type, $this->types, true)) {
            abort(404);
        }
    }
}
