<?php

namespace App\Http\Controllers;

use App\Models\Villa;

class PagesController extends Controller
{
    /**
     * Display all villas (public page).
     */
    public function villas()
    {
        $query = Villa::published()->with(['translations', 'media']);

        // Search
        if (request('search')) {
            $search = substr(str_replace(['%', '_'], ['\\%', '\\_'], request('search')), 0, 100);
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Filter by price
        if (request('price_min')) {
            $query->whereHas('translations', function ($q) {
                $q->where('price', '>=', request('price_min'));
            });
        }

        if (request('price_max')) {
            $query->whereHas('translations', function ($q) {
                $q->where('price', '<=', request('price_max'));
            });
        }

        // Filter by guests
        if (request('guests')) {
            $query->whereHas('translations', function ($q) {
                $q->where('max_guests', '>=', request('guests'));
            });
        }

        $villas = $query->ordered()->paginate(12);

        return view('pages.villas', compact('villas'));
    }

    /**
     * Display a single villa detail page.
     */
    public function villaDetail($slug)
    {
        $villa = Villa::where('slug', $slug)
            ->with(['translations', 'media'])
            ->firstOrFail();

        // Ensure published
        if (! $villa->published_at) {
            abort(404);
        }

        // Ensure translations exist
        if ($villa->translations->isEmpty()) {
            abort(404, 'Villa content not available');
        }

        return view('pages.villa-detail', compact('villa'));
    }
}
