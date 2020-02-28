<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
	use SoftDeletes;

	public function organization()
	{
		return $this->belongsTo(Organization::class);
	}

	public function locations()
    {
        return $this->hasMany(Location::class);
	}

    public function providers()
    {
        return $this->belongsToMany(Provider::class);
    }
}