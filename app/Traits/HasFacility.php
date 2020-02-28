<?php

namespace App\Traits;

use App\Models\Facility;

trait HasFacility
{
    public static function bootHasFacility()
    {
        self::creating(function ($model) {
            if (is_null($model->facility_id)) {
                $model->facility_id = session('facility_id');
            }
        });
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
