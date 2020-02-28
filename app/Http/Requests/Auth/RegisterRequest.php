<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Responder;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends JsonFormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'organization_id' => 'required|exists:organizations,id',
            'password' => [
                'required',
                'min:6',
                //'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/', (atleast one symbol) // /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/    --> one upper, one lower, and one number
            ],
        ];
    }
}