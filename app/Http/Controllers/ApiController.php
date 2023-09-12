<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\{Request, Response, JsonResponse};

class APIController extends Controller
{
    // methods to handle API responses

    public function sendResponse($data, $message, $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'error' => NULL,
            'message' => $message
        ];

        return response()->json($response, $status);
    }

    public function sendError($message, $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'error'=> $message,
            'data' => [],
            'errors'=> []

        ];

        return response()->json($response, $status);
    }

    public function resourceNotFoundResponse(string $resource): JsonResponse
    {
        $response = [
            'error' => "The $resource wasn't found",
        ];

        return response()->json($response, 404);
    }
}