<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Villa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'is_published',
        'display_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function pages()
    {
        return $this->hasMany(VillaPage::class);
    }

    public function rates()
    {
        return $this->hasMany(VillaRate::class)->orderBy('display_order');
    }

    public function getPageByLocale($locale = 'en')
    {
        return $this->pages()->where('locale', $locale)->first();
    }
}
