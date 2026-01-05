<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxBlockTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'dine_relax_block_id',
        'locale',
        'heading',
        'body',
        'hours',
        'highlights',
    ];

    protected $casts = [
        'highlights' => 'array',
    ];

    public function block()
    {
        return $this->belongsTo(DineRelaxBlock::class, 'dine_relax_block_id');
    }
}
