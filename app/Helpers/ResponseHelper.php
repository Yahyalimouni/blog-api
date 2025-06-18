<?php

namespace App\Helpers;


class ResponseHelper 
{
    public static function setExceptionResponse(\Exception $e) {
        return response()->json([
            'success' => false,
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
}

?>