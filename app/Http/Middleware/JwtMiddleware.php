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
use App\Repositories\JwtTokenRepository;

class JwtMiddleware
{   
    protected $jwtTokenRepository;

    public function __construct(JwtTokenRepository $jwtTokenRepository)
    {
        $this->jwtTokenRepository = $jwtTokenRepository;
    }
    public function handle(Request $request, Closure $next)
    {
        $commonHelper = new CommonHelper();
        $response = [
            'success' => false,
            'error'=> '',
            'data' => [],
            'errors'=> []
        ];
        try {
            $token = $commonHelper->GetAndDecodeJWT();
            $checkData[] = ['expires_at', '>', date('Y-m-d H:i:s')];
            $checkData[] = ['unique_id', '=', $token->data->uuid.$token->jti];
            $checkUserAuth = $this->jwtTokenRepository->checkToken($checkData);
            if($checkUserAuth){
                $request->merge(['uuidHeader' => $token->data->uuid, 'tokenId' => $token->jti]);
            }else{
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