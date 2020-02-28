<?php

namespace App\Models;

use App\Traits\HasCreator;
use App\Traits\SetFullName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes, HasCreator, SetFullName;

    /***************************************************************************************
     ** RELATINOS
     ***************************************************************************************/

    public function facilities()
    {
        return $this->belongsToMany(Facility::class)->using(FacilityProvider::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function user()
    {
        return $this->morphOne(User::class, 'profileable');
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function getSuffix()
    {
        switch($this->suffix_type) {
            case 'md':
                return 'M.D.';
                break;
            case 'phd':
                return 'Ph.D.';
                break;
            case 'md-phd':
                return 'M.D.,Ph.D';
                break;
            case 'pa':
                return 'P.A.';
                break;
            case 'np':
                return 'N.P.';
                break;
            case 'rn':
                return 'R.N.';
                break;
        }
    }
}