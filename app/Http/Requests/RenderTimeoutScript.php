<?php

namespace App\Http\Requests;

use App\Models\Procedure;

class RenderTimeoutScript extends JsonFormRequest
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
        return [
            'procedure_id' => 'required|exists:procedures,id',
        ];
    }
}
