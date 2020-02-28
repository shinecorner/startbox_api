<?php

namespace App\Http\Requests\Auth;

use Auth;
use App\Helpers\Responder;
use App\Http\Requests\JsonFormRequest;
use Illuminate\Contracts\Validation\Validator;

class ResetPasswordRequest extends JsonFormRequest
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
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }
}
