<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTimeImmutable;
use App\Helpers\CommonHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $commonHelper = new CommonHelper();

        try {
            $token = $commonHelper->GetAndDecodeJWT();
            $request->merge(['uuidHeader' => $token->data->uuid]);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error'=> $e->getMessage(),
                'data' => [],
                'errors'=> []
            ];
            return response()->json($response, HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}