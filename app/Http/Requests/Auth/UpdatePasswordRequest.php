<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\JsonFormRequest;

class UpdatePasswordRequest extends JsonFormRequest
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
            'current' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($this->current, $this->user()->password)) {
                        $fail('Current password is invalid');
                    }
                },
            ],
            'new' => 'required',
            'confirm' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($this->new !== $this->confirm) {
                        $fail('Confirmation is invalid');
                    }
                },
            ]
        ];
    }
}
