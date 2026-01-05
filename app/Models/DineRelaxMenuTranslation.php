<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineRelaxMenuTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'dine_relax_menu_id',
        'locale',
        'title',
        'button_label',
        'version_note',
    ];

    public function menu()
    {
        return $this->belongsTo(DineRelaxMenu::class, 'dine_relax_menu_id');
    }
}
