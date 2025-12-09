<?php

use Illuminate\Http\JsonResponse;

if (! function_exists('ok')) {

    function ok($data = [], $status_code = 200): JsonResponse
    {
        return response()->json(['status' => 'OK', $data], $status_code);
    }

}
