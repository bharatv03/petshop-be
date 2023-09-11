<?php

namespace App\Helpers;

use Illuminate\{Auth\AuthenticationException, 
    Support\Str, Support\Facades\Auth};
use Exception;
use Firebase\JWT\{JWT, Key};
use DateTimeImmutable;
use App\Helpers\Auth\TokenHelper;

class CommonHelper 
{
    // get jwt info from header
    public function GetRawJWT()
    {
        $header = request()->header('Authorization', '');
        // check if header exists
        if(empty($header)) {
            throw new AuthenticationException('authorization header not found');
        }

        // check if bearer token exists
        if ((Str::startsWith($header, 'Bearer'))) {
            // extract token
            $jwt = Str::substr($header, 7);
            if (!$jwt) {
                throw new AuthenticationException('could not extract token');
            }

        }else{
            throw new AuthenticationException('token not found');
        }

        return $jwt;
    }

    public function DecodeRawJWT($jwt)
    {
        // use secret key to decode token
        $secretKey  = env('JWT_SECRET');
        try {
            $token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            $now = new DateTimeImmutable();
        } catch(Exception $e) {
            throw new AuthenticationException('unauthorized');
        }

        return $token;
    }

    public function GetAndDecodeJWT()
    {
        $jwt = $this->GetRawJWT();
        $token = $this->DecodeRawJWT($jwt);

        return $token;
    }

    public static function LoginAttempt($input, $remember)
    {
        if (Auth::attempt($input, $remember))
        {
            $user = Auth::user();

            $tokenHelper = new TokenHelper;
            $token = $tokenHelper->GenerateToken($user);
            $success = ['user' => $user, 'token' => $token];

            return $success;
        }
        else{
            $error = ['error' => "Invalid Login credentials"];
            return $error;
        }
    }
}