<?php

namespace App\traits;
trait ResponseJsonTrait
{
    public function sendSuccess(string $msg, mixed $data = [], int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $data,
        ], $status);
    }
    public function sendError(string $msg, int $status = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
        ], $status);
    }
}
