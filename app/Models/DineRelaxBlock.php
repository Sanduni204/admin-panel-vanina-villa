<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'dine_relax_page_id',
        'slug',
        'name',
        'image_path',
        'image_alt',
        'cta_label',
        'cta_url',
        'display_order',
    ];

    public function translations()
    {
        return $this->hasMany(DineRelaxBlockTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', 'en')->first();
    }

    public function gallery()
    {
        return $this->hasMany(DineRelaxGallery::class)->orderBy('display_order');
    }
}
