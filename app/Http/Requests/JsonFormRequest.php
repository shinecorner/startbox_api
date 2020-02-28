<?php

namespace App\Http\Requests;

use App\Helpers\Responder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JsonFormRequest extends FormRequest
{
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return null;
    }

    public function isGet()
    {
        return $this->method() == 'GET';
    }

    protected function isPost()
    {
        return $this->method() == 'POST';
    }

    protected function isPut()
    {
        return $this->method() == 'PUT';
    }

    protected function isDelete()
    {
        return $this->method() == 'DELETE';
    }

    protected function checkAuthorization($class, $key = null)
    {
        if (is_null($key)) {
            $key = strtolower(class_basename($class));
        }
        if ($this->isGet()) {
            return true;
        }
        if ($this->isPost()) {
            return auth()->user()->can('create', $class);
        }
        if ($this->isPut()) {
            return auth()->user()->can('update', $this->route($key));
        }
        if ($this->isDelete()) {
            return auth()->user()->can('delete', $this->route($key));
        }
        return false;
    }

    /***************************************************************************************
     ** Overriding
     ***************************************************************************************/

    /**
     * Append "is_update" to Request Input before validation
     */
    public function addRequestChecks()
    {
        $data = $this->all();
        $data['is_post'] = $this->isPost();
        $data['is_update'] = $this->isPut();
        $data['is_editing'] = $this->isPost() || $this->isPut();
        $data['is_delete'] = $this->isDelete();

        $this->replace($data);

        return $this->all();
    }

    /**
     * Modify Input Data Before Validation
     */
    public function validateResolved()
    {
        $this->addRequestChecks();

        parent::validateResolved();
    }

    /**
     * Modify Conditions of Validator
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        // $validator->sometimes();

        $validator->after(function ($validator) {
            $this->request->remove('is_post');
            $this->request->remove('is_update');
            $this->request->remove('is_editing');
            $this->request->remove('is_delete');
        });
        return $validator;
    }

    public function failedValidation(Validator $validator)
    {
        $response = Responder::error($validator->errors(), 'validation-error', 422);

        throw new HttpResponseException($response, 422);
    }
}