<?php

namespace App\Traits;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model): void {
            if (auth()->check()) {
                app(ActivityLogService::class)->log(
                    auth()->user(),
                    'created '.class_basename($model),
                    ['new' => $model->getAttributes()],
                    class_basename($model),
                    $model->getKey()
                );
            }
        });

        static::updated(function (Model $model): void {
            if (auth()->check()) {
                app(ActivityLogService::class)->log(
                    auth()->user(),
                    'updated '.class_basename($model),
                    [
                        'old' => $model->getOriginal(),
                        'new' => $model->getAttributes(),
                    ],
                    class_basename($model),
                    $model->getKey()
                );
            }
        });

        static::deleted(function (Model $model): void {
            if (auth()->check()) {
                app(ActivityLogService::class)->log(
                    auth()->user(),
                    'deleted '.class_basename($model),
                    ['old' => $model->getOriginal()],
                    class_basename($model),
                    $model->getKey()
                );
            }
        });
    }
}
