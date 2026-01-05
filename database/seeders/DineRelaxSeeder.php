<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DineRelaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Single page shell
        $pageId = DB::table('dine_relax_pages')->insertGetId([
            'hero_image_path' => null,
            'hero_image_alt' => null,
            'is_published' => false,
            'meta_image_path' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Base translations scaffold
        foreach (['en', 'fr'] as $locale) {
            DB::table('dine_relax_page_translations')->insert([
                'dine_relax_page_id' => $pageId,
                'locale' => $locale,
                'hero_tagline' => null,
                'hero_title' => $locale === 'en' ? 'Dine & Relax' : 'Détente & Gourmandise',
                'hero_lead' => null,
                'meta_title' => $locale === 'en' ? 'Dine & Relax' : 'Détente & Gourmandise',
                'meta_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $blocks = [
            ['slug' => 'restaurant', 'order' => 1],
            ['slug' => 'bar-coffee', 'order' => 2],
            ['slug' => 'pool', 'order' => 3],
            ['slug' => 'beach', 'order' => 4],
        ];

        foreach ($blocks as $block) {
            $blockId = DB::table('dine_relax_blocks')->insertGetId([
                'dine_relax_page_id' => $pageId,
                'slug' => $block['slug'],
                'image_path' => null,
                'image_alt' => null,
                'cta_label' => null,
                'cta_url' => null,
                'display_order' => $block['order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach (['en', 'fr'] as $locale) {
                DB::table('dine_relax_block_translations')->insert([
                    'dine_relax_block_id' => $blockId,
                    'locale' => $locale,
                    'heading' => $this->defaultHeading($block['slug'], $locale),
                    'body' => null,
                    'hours' => null,
                    'highlights' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function defaultHeading(string $slug, string $locale): string
    {
        $map = [
            'restaurant' => ['en' => 'Restaurant', 'fr' => 'Restaurant'],
            'bar-coffee' => ['en' => 'Bar & Coffee', 'fr' => 'Bar & Café'],
            'pool' => ['en' => 'Pool', 'fr' => 'Piscine'],
            'beach' => ['en' => 'Beach', 'fr' => 'Plage'],
        ];

        return $map[$slug][$locale] ?? ucfirst($slug);
    }
}
