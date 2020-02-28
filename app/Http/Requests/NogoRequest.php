<?php

namespace App\Http\Requests;

use App\Models\Nogo;
use Illuminate\Validation\Rule;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class NogoRequest extends JsonFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->checkAuthorization(Nogo::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'procedure_id' => 'required_if:is_post,true|exists:procedures,id',
            'reason_id' => [
                'required_if:is_post,true',
                Rule::in(Nogo::REASONS),
            ],
            'description' => 'required_if:ist_post,string',
        ];
    }
}