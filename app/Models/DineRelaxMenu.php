<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'file_path',
        'file_name',
        'file_mime',
        'card_image_path',
        'card_image_alt',
        'card_image_alt_fr',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(DineRelaxMenuTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', 'en')->first();
    }
}
