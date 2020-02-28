<?php

namespace App\Rules;

use App\Models\ProcedureBand;
use Illuminate\Contracts\Validation\Rule;

class ActiveBandUsedOnce implements Rule
{
    public $barcode;

    public $procedure;

    public function __construct($barcode, $procedure)
    {
        $this->barcode = $barcode;

        $this->procedure = $procedure;
    }

    public function passes($attribute, $value)
    {
        $pairings = ProcedureBand::with('procedure')->where([
            'barcode' => $this->barcode,
        ])->get();

        $pairings = $pairings->filter(function($pairing) {
            return $pairing->procedure->isActive()
                && $pairing->procedure_id !== $this->procedure->id;
        });

        if($pairings->count() > 0) {
            return false;
        }

        $pairings = $pairings->filter(function($pairing) {
            return $pairing->procedure->isNotActive()
                && $pairing->procedure_id == $this->procedure->id;
        });

        return $pairings->count() === 0;
    }

    public function message()
    {
        return 'Barcode is paired with another active procedure';
    }
}
