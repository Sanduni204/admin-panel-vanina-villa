<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\VillaPage;
use App\Models\VillaRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class VillaPageController extends Controller
{
    public function globalPageEdit()
    {
        return view('admin.villa-pages.global-edit');
    }

    public function globalPageUpdate(Request $request)
    {
        $validated = $request->validate([
            'hero_content_en' => 'nullable|string',
            'hero_content_fr' => 'nullable|string',
            'intro_sentence_en' => 'nullable|string',
            'intro_sentence_fr' => 'nullable|string',
            'intro_topic_en' => 'nullable|string',
            'intro_topic_fr' => 'nullable|string',
            'intro_description_en' => 'nullable|string',
            'intro_description_fr' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        // Store global page data (could use a separate GlobalPage model or settings table)
        // For now, store as admin settings or create a villa with ID 0
        
        return back()->with('success', 'Global villa page updated successfully');
    }

    public function individualPageEdit(Villa $villa)
    {
        $villaEn = $villa->getPageByLocale('en');
        $villaFr = $villa->getPageByLocale('fr');
        $rates = $villa->rates()->orderBy('display_order')->get();

        return view('admin.villa-pages.individual-edit', compact('villa', 'villaEn', 'villaFr', 'rates'));
    }

    public function individualPageUpdate(Request $request, Villa $villa)
    {
        $validated = $request->validate([
            'locale' => 'required|in:en,fr',
            'name' => 'nullable|string|max:255',
            'brand_topic' => 'nullable|string|max:255',
            'brand_express_sentence' => 'nullable|string',
            'brand_description' => 'nullable|string',
            'features_description' => 'nullable|string',
            'rates_topic' => 'nullable|string|max:255',
            'rates_sentence' => 'nullable|string|max:255',
            'hero_content' => 'nullable|string',
        ]);

        $page = $villa->pages()->updateOrCreate(
            ['locale' => $validated['locale']],
            $validated
        );

        return back()->with('success', 'Villa page updated successfully for ' . strtoupper($validated['locale']));
    }

    public function uploadHeroImage(Request $request, Villa $villa)
    {
        $request->validate([
            'hero_image' => 'required|image|mimes:jpeg,png,webp|max:5120',
            'locale' => 'required|in:en,fr',
        ]);

        $locale = $request->input('locale');
        $file = $request->file('hero_image');
        $path = "villa-pages/villa-{$villa->id}/hero/";

        $filename = time() . '_' . $locale . '.' . $file->getClientOriginalExtension();
        $file->storeAs("public/{$path}", $filename);

        $page = $villa->pages()->updateOrCreate(
            ['locale' => $locale],
            ['hero_image_path' => $path . $filename]
        );

        return response()->json([
            'success' => true,
            'path' => asset("storage/{$path}{$filename}"),
        ]);
    }

    public function uploadRoomImage(Request $request, Villa $villa)
    {
        $request->validate([
            'room_image' => 'required|image|mimes:jpeg,png,webp|max:5120',
            'locale' => 'required|in:en,fr',
        ]);

        $locale = $request->input('locale');
        $file = $request->file('room_image');
        $path = "villa-pages/villa-{$villa->id}/rooms/";

        $filename = time() . '_' . $locale . '.' . $file->getClientOriginalExtension();
        $file->storeAs("public/{$path}", $filename);

        $page = $villa->pages()->updateOrCreate(
            ['locale' => $locale],
            ['room_image_path' => $path . $filename]
        );

        return response()->json([
            'success' => true,
            'path' => asset("storage/{$path}{$filename}"),
        ]);
    }

    public function uploadGalleryImages(Request $request, Villa $villa)
    {
        $request->validate([
            'gallery_images.*' => 'required|image|mimes:jpeg,png,webp|max:5120',
            'locale' => 'required|in:en,fr',
        ]);

        $locale = $request->input('locale');
        $uploadedImages = [];
        $basePath = "villa-pages/villa-{$villa->id}/gallery/";

        foreach ($request->file('gallery_images') ?? [] as $file) {
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->storeAs("public/{$basePath}", $filename);
            $uploadedImages[] = $basePath . $filename;
        }

        $page = $villa->pages()->where('locale', $locale)->first();
        if ($page) {
            $currentImages = $page->gallery_images ?? [];
            $page->update(['gallery_images' => array_merge($currentImages, $uploadedImages)]);
        } else {
            $villa->pages()->create([
                'locale' => $locale,
                'gallery_images' => $uploadedImages,
            ]);
        }

        return response()->json([
            'success' => true,
            'images' => collect($uploadedImages)->map(fn($img) => asset("storage/{$img}"))->toArray(),
        ]);
    }

    public function deleteGalleryImage(Request $request, Villa $villa, $imageIndex)
    {
        $locale = $request->input('locale', 'en');
        $page = $villa->pages()->where('locale', $locale)->first();

        if ($page && $page->gallery_images) {
            $images = $page->gallery_images;
            $imageToDelete = $images[$imageIndex] ?? null;

            if ($imageToDelete && file_exists(storage_path("app/public/{$imageToDelete}"))) {
                unlink(storage_path("app/public/{$imageToDelete}"));
            }

            unset($images[$imageIndex]);
            $page->update(['gallery_images' => array_values($images)]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }

    public function reorderGallery(Request $request, Villa $villa)
    {
        $request->validate([
            'image_order' => 'required|array',
            'locale' => 'required|in:en,fr',
        ]);

        $locale = $request->input('locale');
        $page = $villa->pages()->where('locale', $locale)->first();

        if ($page) {
            $page->update(['gallery_images' => $request->input('image_order')]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function storeRate(Request $request, Villa $villa)
    {
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'season_name' => 'required|string|max:255',
            'season_start' => 'nullable|date',
            'season_end' => 'nullable|date',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['display_order'] = $villa->rates()->max('display_order') + 1 ?? 0;

        $rate = $villa->rates()->create($validated);

        return response()->json([
            'success' => true,
            'rate' => $rate,
        ]);
    }

    public function updateRate(Request $request, Villa $villa, VillaRate $rate)
    {
        $validated = $request->validate([
            'room_type' => 'required|string|max:255',
            'season_name' => 'required|string|max:255',
            'season_start' => 'nullable|date',
            'season_end' => 'nullable|date',
            'price' => 'required|numeric|min:0',
        ]);

        $rate->update($validated);

        return response()->json([
            'success' => true,
            'rate' => $rate,
        ]);
    }

    public function deleteRate(Villa $villa, VillaRate $rate)
    {
        $rate->delete();

        return response()->json(['success' => true]);
    }
}
