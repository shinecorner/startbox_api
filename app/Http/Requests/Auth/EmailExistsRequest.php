<?php

namespace App\Http\Requests\Auth;

use Auth;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class EmailExistsRequest extends JsonFormRequest
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
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'Sorry. We cannot find an account with that email.',
        ];
    }
}