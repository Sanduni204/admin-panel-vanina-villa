<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'locale',
        'title',
        'description',
        'amenities',
        'rules',
        'price',
        'price_shoulder_season',
        'price_high_season',
        'price_peak_season',
        'max_guests',
        'min_guests',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_shoulder_season' => 'decimal:2',
        'price_high_season' => 'decimal:2',
        'price_peak_season' => 'decimal:2',
        'max_guests' => 'integer',
        'min_guests' => 'integer',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
