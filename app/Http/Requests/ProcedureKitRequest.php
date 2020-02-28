<?php

namespace App\Http\Requests;

use App\Models\Procedure;
use App\Rules\ActiveKitUsedOnce;
use App\Http\Requests\JsonFormRequest;

class ProcedureKitRequest extends JsonFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function all($keys = null)
    {
        $this->merge(['patient_id' => $this->route('procedure')->patient_id]);

        return parent::all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'patient_id' => 'required',
            'barcode' => ['required', new ActiveKitUsedOnce($this->barcode, $this->procedure)],
        ];
    }
}
