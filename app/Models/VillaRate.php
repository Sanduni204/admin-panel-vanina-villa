<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillaRate extends Model
{
    protected $fillable = [
        'villa_id',
        'room_type',
        'season_name',
        'season_start',
        'season_end',
        'price',
        'display_order',
    ];

    protected $casts = [
        'season_start' => 'date',
        'season_end' => 'date',
        'price' => 'decimal:2',
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}
