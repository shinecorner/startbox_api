<?php

namespace App\Traits;

use App\Models\Organization;

trait HasOrganization
{
    public static function bootHasOrganization()
    {
        self::creating(function ($model) {
            if (is_null($model->organization_id)) {
                $model->organization_id = auth()->user()->organization_id;
            }
        });
    }

    public function organzation()
    {
        return $this->belongsTo(Organization::class);
    }
}
