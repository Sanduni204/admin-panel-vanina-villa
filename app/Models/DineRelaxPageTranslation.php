<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxPageTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'dine_relax_page_id',
        'locale',
        'hero_tagline',
        'hero_title',
        'hero_lead',
        'meta_title',
        'meta_description',
        'menus_description',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(DineRelaxPage::class, 'dine_relax_page_id');
    }
}
