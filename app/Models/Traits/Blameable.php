<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait Blameable
{
    public static function bootBlameable()
    {
        static::creating(function ($model) {
            $userId = Auth::id();

            if (Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = $userId;
            }

            if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model) {
            $userId = Auth::id();
            if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = $userId;
            }
        });
    }
}
