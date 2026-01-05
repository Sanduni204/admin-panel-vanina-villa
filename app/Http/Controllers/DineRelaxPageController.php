<?php

namespace App\Http\Controllers;

use App\Models\DineRelaxMenu;
use App\Models\DineRelaxPage;

class DineRelaxPageController extends Controller
{
    public function show()
    {
        $page = DineRelaxPage::with([
            'translations',
            'blocks.translations',
            'blocks.gallery',
        ])->first();

        if (! $page || ! $page->is_published) {
            abort(404);
        }

        $translation = $page->translation(null, true);
        if (! $translation) {
            abort(404);
        }

        $blocks = $page->blocks()
            ->with(['translations', 'gallery'])
            ->orderBy('display_order')
            ->get();

        $menus = DineRelaxMenu::with('translations')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('pages.dine-relax', [
            'page' => $page,
            'blocks' => $blocks,
            'menus' => $menus,
        ]);
    }
}
