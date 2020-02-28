<?php

namespace App\Http\Requests;

use App\Models\Procedure;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class ProcedureRequest extends JsonFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkAuthorization(Procedure::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'location_id' => 'required_if:is_post,true|exists:locations,id',
            'patient_id' => 'required_if:is_post,true|exists:patients,id',
            'provider_id' => 'required_if:is_post,true|exists:providers,id',
            'title' => 'required_if:is_post,true|string',
            'description' => 'required_if:is_post,true|string',
            'laterality' => 'required_if:is_post,true|in:0,1,2',
            'script' => 'string|nullable',
            'scheduled_at' => 'required_if:is_post,true|date_format:Y-m-d',
            'archived' => 'boolean',
        ];
    }
}