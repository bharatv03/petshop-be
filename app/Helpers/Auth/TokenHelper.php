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
    public function GenerateToken($user, $jwtTokenRepo)
    {
        $secretKey  = env('JWT_SECRET');
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+10 minutes')->getTimestamp();
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
        $issuedDate = date('Y-m-d H:i:s',$issuedAt->getTimestamp());
        $expiersAt = date('Y-m-d H:i:s',$expire);

        $dbData = ['user_id' => $user->id, 'unique_id' => $uuid.$tokenId, 'token_title' => __('message.login.title'), 
                    'restrictions' => NULL, 'permissions' => NULL, 'created_at' => $issuedDate,
                    'updated_at' => $issuedDate, 'expires_at' => $expiersAt, 'last_used_at' => $issuedDate,
                    'refreshed_at' => NULL];
        $jwtTokenRepo->addToken($dbData);

        return $token;
    }
}