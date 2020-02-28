<?php

namespace App\Traits;

use App\Models\Practice;

trait HasPractice
{
    public static function bootHasPractice()
    {
        self::creating(function ($model) {
            if (is_null($model->practice_id)) {
                $model->practice_id = session('practice_id');;
            }
        });
    }

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }
}
