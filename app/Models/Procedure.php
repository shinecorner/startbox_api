<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasCreator;
use Illuminate\Support\Arr;
use App\Traits\SetsPropertyIfAvailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procedure extends Model
{
    use SoftDeletes, HasCreator, SetsPropertyIfAvailable;

    protected $guarded = ['id'];
    protected $dates = ['scheduled_at'];
    protected $updateableFields = ['title', 'description', 'laterality', 'script'];

    public static $lateralities = [
        0 => 'L', 1 => 'R', 2 => 'N',
    ];

    public static function boot()
    {
        parent::boot();

        self::updating(function ($procedure) {
            if($procedure->isDirty('laterality') && !is_null($procedure->kit)) {
                $procedure->kit->delete();
            }
        });
    }

    /***************************************************************************************
     ** SCOPES
     ***************************************************************************************/

    public function scopeScheduledToday($query)
    {
        $timezone = getRequestTimezone();

        $dayStart = Carbon::now($timezone)->startOfDay();
        $dayEnd = Carbon::now($timezone)->endOfDay();

        return $query->whereBetween('scheduled_at', [$dayStart, $dayEnd]);
    }

    public function scopeFutureDates($query)
    {
        $timezone = getRequestTimezone();

        return $query->where('scheduled_at', '>', Carbon::now($timezone)->endOfDay());
    }

    /***************************************************************************************
     ** RElATIONS
     ***************************************************************************************/

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function template()
	{
		return $this->belongsTo(Template::class);
    }

    public function band()
    {
        return $this->hasOne(ProcedureBand::class);
    }

    public function kit()
    {
        return $this->hasOne(ProcedureKit::class);
    }

    public function recordings()
    {
        return $this->hasMany(Recording::class);
    }

    /***************************************************************************************
     ** CRUD
     ***************************************************************************************/

    public static function makeOne(array $data)
    {
        $procedure = new Procedure;
        $procedure->location()->associate(Location::find($data['location_id']));
        $procedure->facility()->associate($procedure->location->facility);
        $procedure->organization_id = $procedure->location->organization_id;
        $procedure->patient_id = $data['patient_id'];
        $procedure->provider_id = $data['provider_id'];
        $procedure->title = $data['title'];
        $procedure->description = $data['description'];
        $procedure->laterality = $data['laterality'];
        $procedure->script = Arr::get($data, 'script');
        $procedure->scheduled_at = Carbon::createFromFormat('Y-m-d', $data['scheduled_at'])->toDateString();
        $procedure->save();

        $procedure->load('patient', 'provider');

        return $procedure;
    }

    public function updateMe(array $data)
    {
        $this->setIfAvailable($this->updateableFields, $data);

        // Updating Schedule Time
        if (Arr::has($data, 'scheduled_at')) {
            $this->scheduled_at = Carbon::createFromFormat('Y-m-d', $data['scheduled_at'])->toDateString();
        }

        // Archiving
        if (Arr::has($data, 'archived')) {
            $this->handleArchiving($data['archived']);
        }

        $this->save();
    }

    public function handleArchiving(bool $archive)
    {
        if ($archive && $this->archived_at === NULL) {
            $this->archived_at = now();
            return;
        }
        if ($archive === false && $this->archived_at) {
            $this->archived_at = null;
        }
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function getLateralityStringAttribute()
    {
        return $this->laterality($this->laterality);
    }

    public static function laterality($laterality)
    {
        if(isset(self::$lateralities[$laterality])) {
            return self::$lateralities[$laterality];
        }

        throw new \Exception("Invalid laterality");
    }

    public function isActive()
    {
        return
            is_null($this->completed_at) &&
            is_null($this->canceled_at) &&
            is_null($this->archived_at) &&
            is_null($this->deleted_at);
    }

    public function isNotActive()
    {
        return ! $this->isActive();
    }
}
