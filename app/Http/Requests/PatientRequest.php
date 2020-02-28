<?php

namespace App\Http\Requests;

use App\Models\Patient;

class PatientRequest extends JsonFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkAuthorization(Patient::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:64',
            'last_name' => 'required|min:2|max:64',
            'dob' => 'required|date|before:today',
            'sex' => 'required|in:male,female,other',
            'dod_identifier' => 'required',
        ];
    }
}
