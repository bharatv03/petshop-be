<?php

namespace App\Helpers\Auth;

use Firebase\JWT\JWT;
use DateTimeImmutable;

class TokenHelper
{
    /**
     * Handles operations related to generate tokens for authentication
     */

    // generate token
    public function GenerateToken($user)
    {
        $secretKey  = env('JWT_SECRET');
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+5 minutes')->getTimestamp();
        $serverName = env('APP_URL');
        $uuid   = $user->uuid;

        // Create the token as an array
        $data = [
            'iat'  => $issuedAt->getTimestamp(),
            'jti'  => $tokenId,
            'iss'  => $serverName,
            'nbf'  => $issuedAt->getTimestamp(),
            'exp'  => $expire,
            'data' => [
                'uuid' => $uuid,
            ]
        ];

        // Encode the array to a JWT string.
        $token = JWT::encode(
            $data,
            $secretKey,
            'HS256'
        );
        return $token;
    }
}