<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Operation successful', int $status = 200): JsonResponse
    {
        if ($data instanceof AbstractPaginator) {
            $payload = [
                'success' => true,
                'data' => [
                    'data' => $data->items(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ],
                'message' => $message,
            ];
        } else {
            $payload = [
                'success' => true,
                'data' => $data,
                'message' => $message,
            ];
        }

        return response()->json($payload, $status);
    }

    protected function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
