<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'dine_relax_block_id',
        'image_path',
        'image_alt',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function block()
    {
        return $this->belongsTo(DineRelaxBlock::class, 'dine_relax_block_id');
    }
}
