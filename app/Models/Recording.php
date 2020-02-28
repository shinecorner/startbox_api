<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recording extends Model
{
    public $guarded = ['id'];
    public $dates = ['started_at, ended_at, created_at, updated_at'];

    public static $types = [0 => 'decision', 1 => 'timeout', 2 => 'signout'];

    /***************************************************************************************
     ** RELATIONS
     ***************************************************************************************/

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /***************************************************************************************
     ** CRUD
     ***************************************************************************************/

    public static function makeOne($procedure, array $data)
    {
        $type = self::keyForType($data['type'] ?? null);

        $recording = new Recording();
        $recording->procedure()->associate($procedure->id);
        $recording->provider_id = $data['provider_id'];
        $recording->patient_id = $data['patient_id'];
        $recording->type = $type;
        $recording->path = $data['path'];
        $recording->started_at = $data['started_at'];
        $recording->ended_at = $data['ended_at'];
        $recording->script = $data['script'];
        $recording->save();

        return $recording;
    }

    /***************************************************************************************
     ** HELPERS
     ***************************************************************************************/

    public function getUrlAttribute()
    {
        return rtrim(config('app.filepath'), '/') . '/' . $this->path;
    }

    public function getTypeStringAttribute()
    {
        return $this->type($this->type);
    }

    public static function type($type)
    {
        if(isset(self::$types[$type])) {
            return self::$types[$type];
        }

        throw new \Exception("Invalid recording type: $type");
    }

    public static function keyForType(string $type)
    {
        $flipTypes = array_flip(self::$types);

        if (isset($flipTypes[$type])) {
            return $flipTypes[$type];
        }

        throw new \Exception("Invalid recording type: $type");
    }
}
