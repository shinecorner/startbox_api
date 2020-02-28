<?php

namespace App\Models;

use App\Traits\HasCreator;
use App\Traits\HasFacility;
use App\Traits\SetFullName;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'dob' => 'date:Y-m-d'
    ];

    use SoftDeletes,
        HasCreator,
        HasOrganization,
        SetFullName;

    /***************************************************************************************
     ** RELATIONS
     ***************************************************************************************/

    public function defaultProvider()
    {
        return $this->belongsTo(Provider::class, 'default_provider_id');
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class, 'patient_id');
    }

    /***************************************************************************************
     ** CREATE / UPDATE
     ***************************************************************************************/

    public static function makeOne(array $data)
    {
        $patient = new Patient;
        $patient->organization_id = Organization::first()->id; // TODO: replace
        $patient->default_provider_id = Arr::get($data, 'default_provider_id');
        $patient->first_name = Arr::get($data, 'first_name');
        $patient->last_name = Arr::get($data, 'last_name');
        $patient->dob = Arr::get($data, 'dob');
        $patient->sex = Arr::get($data, 'sex');
        $patient->save();

        return $patient;
    }

    /***************************************************************************************
     ** GENERAL
     ***************************************************************************************/

    public function getProviders()
    {
        $providers = $this->procedures()->with('provider')->get()->pluck('provider')->keyBy('id');

        // add default
        if ($this->defaultProvider && $providers->has($this->defaultProvider->id) === false) {
            $providers->push($this->defaultProvider);
        }

        return $providers;
    }
}