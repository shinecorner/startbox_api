<?php

namespace App\Traits;

use App\Models\User;

trait HasCreator
{
    public static function bootHasCreator()
    {
        self::creating(function ($model) {
            if (is_null($model->creator_id)) {
                $model->creator_id = auth()->id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
