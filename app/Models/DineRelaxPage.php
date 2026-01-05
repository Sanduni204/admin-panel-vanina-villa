<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_image_path',
        'hero_image_alt',
        'is_published',
        'meta_image_path',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(DineRelaxPageTranslation::class);
    }

    public function translation(?string $locale = null, bool $publishedOnly = false)
    {
        $locale = $locale ?: app()->getLocale();
        $query = $this->translations()->where('locale', $locale);
        if ($publishedOnly) {
            $query->where('is_published', true);
        }

        $match = $query->first();

        if ($match) {
            return $match;
        }

        $fallback = $this->translations()->where('locale', 'en');
        if ($publishedOnly) {
            $fallback->where('is_published', true);
        }

        return $fallback->first();
    }

    public function blocks()
    {
        return $this->hasMany(DineRelaxBlock::class)->orderBy('display_order');
    }
}
