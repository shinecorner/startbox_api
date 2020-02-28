<?php

namespace App\Models;

use App\Traits\HasCreator;
use Illuminate\Support\Arr;
use App\Traits\SetsPropertyIfAvailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nogo extends Model
{
    use SoftDeletes, HasCreator, SetsPropertyIfAvailable;

    const REASON_OTHER = 0;
    const REASON_PATIENT = 1;
    const REASON_SITE = 2;
    const REASON_LATERALITY = 3;
    const REASON_MEDICAL = 4;
    const REASON_EQUIPMENT = 5;

    const REASONS = [
        Nogo::REASON_PATIENT,
        Nogo::REASON_SITE,
        Nogo::REASON_LATERALITY,
        Nogo::REASON_MEDICAL,
        Nogo::REASON_EQUIPMENT,
        Nogo::REASON_OTHER,
    ];

    public $with = ['procedure'];

    protected $updateableFields = ['reason_id', 'description'];

    public static function boot()
    {
        parent::boot();

        self::created(function ($nogo) {
            if($nogo->procedure->kit) {
                $nogo->procedure->kit->delete();
            }
        });
    }

    /***************************************************************************************
     ** RELATIONS
     ***************************************************************************************/

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    /***************************************************************************************
     ** CREATE / UPDATE
     ***************************************************************************************/

    public static function makeOne(array $data)
    {
        $nogo = new Nogo;
        $nogo->procedure()->associate(Procedure::find($data['procedure_id']));
        $nogo->organization_id = $nogo->procedure->organization_id;
        $nogo->patient_id = $nogo->procedure->patient_id;
        $nogo->provider_id = $nogo->procedure->provider_id;
        $nogo->reason_id = $data['reason_id'];
        $nogo->description = $data['description'];
        $nogo->save();

        return $nogo;
    }

    public function updateMe(array $data)
    {
        $this->setIfAvailable($this->updateableFields, $data);
        $this->save();
    }
}