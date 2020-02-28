<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests\JsonFormRequest;
use App\Models\Patient;
use Illuminate\Support\Arr;

class PatientProviderRequest extends JsonFormRequest
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

    public function rules()
    {
        return [];
    }
}