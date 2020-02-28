<?php

namespace App\Traits;

trait SetFullName
{
    public static function bootSetFullName()
    {
        self::saving(function ($model) {
            $model->full_name = $model->first_name . ' ' . $model->last_name;
        });
    }
}
