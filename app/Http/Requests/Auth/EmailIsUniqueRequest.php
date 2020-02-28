<?php

namespace App\Http\Requests\Auth;

use Auth;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class EmailIsUniqueRequest extends JsonFormRequest
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
            'email' => 'required|email|unique:users,email',
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
            'email.required' => 'An email is required',
            'email.email'  => 'Not a valid email address',
            'email.unique'  => 'There is already an account with this email',
        ];
    }
}