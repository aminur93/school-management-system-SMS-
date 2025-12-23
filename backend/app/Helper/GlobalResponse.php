<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;
use streamWrapper;


class GlobalResponse
{
    public $data;
    public $message;
    public $status;

    public function __construct($data, $message, $status)
    {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;
    }

    public static function success($data, $message, $status): JsonResponse
    {
        $response = new static($data, $message, $status);

        if (!is_array($data)) {
            $data = [];
        }

        return response()->json([
            "success" => true,
            "data" => $response->data,
            "message" => $message,
            "status" => $status
        ], $status);
    }

    public static function error($data, $message, $status): JsonResponse
    {
        $response = new static($data, $message, $status);

        if (!is_array($data)) {
            $data = [];
        }

        return response()->json([
            "success" => false,
            "data" => $response->data,
            "message" => $message,
            "status" => $status
        ], $status);
    }
}