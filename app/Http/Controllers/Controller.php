<?php

namespace App\Http\Controllers;

use App\Helpers\Responder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data = [], string $message = '')
    {
        return Responder::success($data, $message);
    }

    public function error($data = [], string $message = '', $responseCode = 400)
    {
        return Responder::error($data, $message, $responseCode);
    }

    protected function formatValidationErrors(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $collection = collect($errors);
        $first_error = $collection->first();

        return Responder::noJsonError($errors, $first_error);
    }
}
