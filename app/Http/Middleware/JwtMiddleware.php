<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\CommonHelper;
use App\Repositories\JwtTokenRepository;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    protected $jwtTokenRepository;

    public function __construct(JwtTokenRepository $jwtTokenRepository)
    {
        $this->jwtTokenRepository = $jwtTokenRepository;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $commonHelper = new CommonHelper();
        $response = [
            'success' => false,
            'error' => '',
            'data' => [],
            'errors' => [],
        ];
        try {
            $token = $commonHelper->getAndDecodeJwt();
            $checkData[] = ['expires_at', '>', date('Y-m-d H:i:s')];
            $checkData[] = ['unique_id', '=', $token->data->uuid.$token->jti];
            $checkUserAuth = $this->jwtTokenRepository->checkToken($checkData);
            if ($checkUserAuth) {
                $request->merge(['uuidHeader' => $token->data->uuid, 'tokenId' => $token->jti]);
            } else {
                $response = __('message.user.token_invalid');
                return response()->json($response, HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            return response()->json($response, HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
