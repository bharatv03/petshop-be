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
            'data' => $data,
            'message' => $message
        ];

        return response()->json($response, $status);
    }

    public function sendError($message, $errorData = [], $status = 400): JsonResponse
    {
        $response = [
            'message' => $message
        ];

        if(!empty($errorData)) {
            $response['data'] = $errorData;
        }

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