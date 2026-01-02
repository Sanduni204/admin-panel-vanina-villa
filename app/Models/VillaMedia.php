<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaMedia extends Model
{
    use HasFactory;

    protected $table = 'villa_media';

    protected $fillable = [
        'villa_id',
        'image_path',
        'alt_text_en',
        'alt_text_fr',
        'position',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'position' => 'integer',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}
