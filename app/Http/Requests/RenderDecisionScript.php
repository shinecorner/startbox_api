<?php

namespace App\Http\Requests;

use App\Models\Procedure;

class RenderDecisionScript extends JsonFormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $keys = implode(',', array_keys(Procedure::$lateralities));

        return [
            'laterality' => "required|numeric|in:$keys",
            'patient_id' => 'required|exists:patients,id',
        ];
    }
}
