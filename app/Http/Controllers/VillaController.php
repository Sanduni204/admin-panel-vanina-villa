<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVillaRequest;
use App\Models\Villa;
use App\Models\VillaMedia;
use App\Models\VillaTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class VillaController extends Controller
{
    /**
     * Display a listing of the villas.
     */
    public function index(Request $request)
    {
        $query = Villa::query();

        // Search by title
        if ($request->has('search') && $request->search) {
            $search = substr(str_replace(['%', '_'], ['\\%', '\\_'], $request->search), 0, 100);
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->has('price_min') && $request->price_min) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('price', '>=', $request->price_min);
            });
        }

        if ($request->has('price_max') && $request->price_max) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('price', '<=', $request->price_max);
            });
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured) {
            $query->where('featured', true);
        }

        // Filter by published
        if ($request->has('published') && $request->published) {
            $query->published();
        }

        // Sort
        $sort = $request->get('sort', 'display_order');
        $direction = $request->get('direction', 'asc');

        if ($sort === 'price') {
            $query->join('villa_translations', 'villas.id', '=', 'villa_translations.villa_id')
                ->where('villa_translations.locale', 'en')
                ->select('villas.*')
                ->orderBy('villa_translations.price', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $villas = $query->with(['translations', 'media'])->paginate(10);

        return view('admin.villas.index', compact('villas'));
    }

    /**
     * Show the form for creating a new villa.
     */
    public function create()
    {
        return view('admin.villas.create');
    }

    /**
     * Store a newly created villa in storage.
     */
    public function store(StoreVillaRequest $request)
    {
        $validated = $request->validated();

        // Generate slug if not provided
        $slug = $validated['slug'] ?? Str::slug($validated['title_en']);
        $slug = $this->generateUniqueSlug($slug);

        // Create villa
        $villa = Villa::create([
            'slug' => $slug,
            'featured' => $validated['featured'] ?? false,
            'published_at' => $validated['published'] ?? false ? now() : null,
        ]);

        // Create translations
        foreach (['en', 'fr'] as $locale) {
            VillaTranslation::create([
                'villa_id' => $villa->id,
                'locale' => $locale,
                'title' => $validated["title_{$locale}"],
                'description' => $validated["description_{$locale}"],
                'amenities' => $validated["amenities_{$locale}"] ?? null,
                'rules' => $validated["rules_{$locale}"] ?? null,
                'price' => $validated['price'] ?? $validated['price_shoulder_season'] ?? 0,
                'price_shoulder_season' => $validated['price_shoulder_season'] ?? null,
                'price_high_season' => $validated['price_high_season'] ?? null,
                'price_peak_season' => $validated['price_peak_season'] ?? null,
                'max_guests' => $validated['max_guests'],
                'min_guests' => $validated['min_guests'] ?? null,
            ]);
        }

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $this->storeFeaturedImage($villa, $request->file('featured_image'));
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $this->storeGalleryImages($villa, $request->file('gallery_images'), $request);
        }

        return redirect()->route('villas.show', $villa)
            ->with('success', 'Villa created successfully.');
    }

    /**
     * Display the specified villa.
     */
    public function show(Villa $villa)
    {
        $villa->load(['translations', 'media']);
        return view('admin.villas.show', compact('villa'));
    }

    /**
     * Show the form for editing the specified villa.
     */
    public function edit(Villa $villa)
    {
        $villa->load(['translations', 'media']);
        return view('admin.villas.edit', compact('villa'));
    }

    /**
     * Update the specified villa in storage.
     */
    public function update(StoreVillaRequest $request, Villa $villa)
    {
        $validated = $request->validated();

        // Update slug if provided
        if (isset($validated['slug']) && $validated['slug'] && $validated['slug'] !== $villa->slug) {
            $validated['slug'] = $this->generateUniqueSlug($validated['slug'], $villa->id);
            $villa->update(['slug' => $validated['slug']]);
        }

        // Update villa
        $villa->update([
            'featured' => $validated['featured'] ?? $villa->featured,
            'published_at' => $validated['published'] ?? false ? ($villa->published_at ?? now()) : null,
        ]);

        // Update translations
        foreach (['en', 'fr'] as $locale) {
            VillaTranslation::updateOrCreate(
                [
                    'villa_id' => $villa->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $validated["title_{$locale}"],
                    'description' => $validated["description_{$locale}"],
                    'amenities' => $validated["amenities_{$locale}"] ?? null,
                    'rules' => $validated["rules_{$locale}"] ?? null,
                    'price' => $validated['price'] ?? $validated['price_shoulder_season'] ?? 0,
                    'price_shoulder_season' => $validated['price_shoulder_season'] ?? null,
                    'price_high_season' => $validated['price_high_season'] ?? null,
                    'price_peak_season' => $validated['price_peak_season'] ?? null,
                    'max_guests' => $validated['max_guests'],
                    'min_guests' => $validated['min_guests'] ?? null,
                ]
            );
        }

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            $this->storeFeaturedImage($villa, $request->file('featured_image'));
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $this->storeGalleryImages($villa, $request->file('gallery_images'), $request);
        }

        return redirect()->route('villas.show', $villa)
            ->with('success', 'Villa updated successfully.');
    }

    /**
     * Remove the specified villa from storage.
     */
    public function destroy(Villa $villa)
    {
        $villa->delete();
        return redirect()->route('villas.index')
            ->with('success', 'Villa deleted successfully.');
    }

    /**
     * Permanently delete a villa.
     */
    public function forceDelete(Villa $villa)
    {
        // Delete media files
        foreach ($villa->media as $media) {
            if (file_exists(public_path($media->image_path))) {
                unlink(public_path($media->image_path));
            }
        }

        $villa->forceDelete();
        return redirect()->route('villas.index')
            ->with('success', 'Villa permanently deleted.');
    }

    /**
     * Update display order.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'villas' => 'required|array',
            'villas.*' => 'integer|exists:villas,id',
        ]);

        foreach ($validated['villas'] as $index => $villaId) {
            Villa::where('id', $villaId)->update(['display_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Store featured image.
     */
    private function storeFeaturedImage($villa, $image)
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = "uploads/villas/{$villa->id}";
        $fullPath = public_path($path);

        // Create directory if it doesn't exist
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Move uploaded file to public directory
        $image->move($fullPath, $filename);

        // Delete old featured image file if exists
        $oldFeaturedMedia = VillaMedia::where('villa_id', $villa->id)->where('is_featured', true)->first();
        if ($oldFeaturedMedia && file_exists(public_path($oldFeaturedMedia->image_path))) {
            unlink(public_path($oldFeaturedMedia->image_path));
        }

        // Store as featured media
        VillaMedia::where('villa_id', $villa->id)->where('is_featured', true)->delete();
        VillaMedia::create([
            'villa_id' => $villa->id,
            'image_path' => "{$path}/{$filename}",
            'is_featured' => true,
            'position' => 0,
        ]);
    }

    /**
     * Store gallery images.
     */
    private function storeGalleryImages($villa, $images, $request)
    {
        $position = VillaMedia::where('villa_id', $villa->id)->max('position') ?? 0;
        $path = "uploads/villas/{$villa->id}";
        $fullPath = public_path($path);

        // Create directory if it doesn't exist
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        foreach ($images as $index => $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

            // Move uploaded file to public directory
            $image->move($fullPath, $filename);

            $altTextEn = $request->input("gallery_alt_en.{$index}");
            $altTextFr = $request->input("gallery_alt_fr.{$index}");

            // Create media record
            VillaMedia::create([
                'villa_id' => $villa->id,
                'image_path' => "{$path}/{$filename}",
                'alt_text_en' => $altTextEn,
                'alt_text_fr' => $altTextFr,
                'position' => $position + $index + 1,
            ]);
        }
    }

    /**
     * Delete a specific media file.
     */
    public function deleteMedia(Villa $villa, VillaMedia $media)
    {
        // Verify the media belongs to this villa
        if ($media->villa_id !== $villa->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the physical file
        if (file_exists(public_path($media->image_path))) {
            unlink(public_path($media->image_path));
        }

        // Delete the database record
        $media->delete();

        return redirect()->route('villas.edit', $villa)
            ->with('success', 'Image removed successfully.');
    }

    /**
     * Generate unique slug.
     */
    private function generateUniqueSlug($slug, $excludeId = null)
    {
        $original = $slug;
        $count = 1;

        while (Villa::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
