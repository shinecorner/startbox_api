<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecordingRequest extends FormRequest
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

    public function all($keys = null)
    {

        return parent::all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'provider_id' => 'required',
            'patient_id' => 'required',
            'type' => 'required|in:timeout,signout,decision',
            'path' => 'required',
            'started_at' => 'required',
            'ended_at' => 'required',
            'script' => 'required',
        ];
    }
}
