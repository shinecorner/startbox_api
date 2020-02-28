<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\Procedure;
use App\Traits\HasCreator;
use Illuminate\Database\Eloquent\Model;

class ProcedureKit extends Model
{
    use HasCreator;

    public $guarded = ['id'];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
