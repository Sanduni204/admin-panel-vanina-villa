<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillaPage extends Model
{
    protected $fillable = [
        'villa_id',
        'locale',
        'name',
        'hero_image_path',
        'hero_content',
        'brand_topic',
        'brand_express_sentence',
        'brand_description',
        'features_description',
        'rates_topic',
        'rates_sentence',
        'room_image_path',
        'gallery_images',
    ];

    protected $casts = [
        'gallery_images' => 'array',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
