<?php

namespace App\Helpers;

class Responder
{
    public static function success($data = [], string $message = '')
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ]);
    }

    public static function error($data = [], string $error = '', $responseCode = 400)
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $error,
        ], $responseCode);
    }

    public static function noJsonSuccess($data = [], string $message = '')
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
    }

    public static function noJsonError($data = [], string $error = '')
    {
        return [
            'success' => false,
            'data' => $data,
            'message' => $error,
        ];
    }
}
