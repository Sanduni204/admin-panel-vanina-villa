<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Villa extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'slug',
        'featured',
        'display_order',
        'published_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($villa) {
            if ($villa->isForceDeleting()) {
                // Clean up media files on force delete
                foreach ($villa->media as $media) {
                    $filePath = storage_path('app/public/' . $media->image_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        });
    }

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function translations()
    {
        return $this->hasMany(VillaTranslation::class);
    }

    public function media()
    {
        return $this->hasMany(VillaMedia::class);
    }

    public function getTranslation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', 'en')->first();
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at', 'desc');
    }
}
