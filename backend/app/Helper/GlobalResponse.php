<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
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
        // Case 1: Laravel Resource Collection (pagination সহ)
        if ($data instanceof ResourceCollection) {
            return $data->additional([
                'success' => true,
                'message' => $message,
                'status'  => $status,
            ])->response()->setStatusCode($status);
        }

        // Case 2: Paginator without Resource
        if ($data instanceof AbstractPaginator) {
            return response()->json([
                'success' => true,
                'data'    => $data->items(),
                'meta'    => [
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'total'        => $data->total(),
                ],
                'message' => $message,
                'status'  => $status,
            ], $status);
        }

        return response()->json([
            "success" => true,
            "data" => $data,
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