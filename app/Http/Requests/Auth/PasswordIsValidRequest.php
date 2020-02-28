<?php

namespace App\Http\Requests\Auth;

use Auth;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class PasswordIsValidRequest extends JsonFormRequest
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
            'password' => [
                'required',
                'min:6',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.required' => 'A password is required',
            'password.min'  => 'Your password must be a minimum of 6 characters',
        ];
    }
}