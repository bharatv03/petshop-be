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
    public function generateToken($user, $jwtTokenRepo)
    {
        $tokenId = base64_encode(random_bytes(16));
        $issuedAt = new DateTimeImmutable();
        $expire = $issuedAt->modify('+10 minutes')->getTimestamp();
        $uuid = $user->uuid;
        // Create the token as an array
        $data = [
            'iat' => $issuedAt->getTimestamp(),
            'jti' => $tokenId,
            'iss' => env('APP_URL'),
            'nbf' => $issuedAt->getTimestamp(),
            'exp' => $expire,
            'data' => [
                'uuid' => $uuid,
            ],
        ];
        // Encode the array to a JWT string.
        $token = JWT::encode(
            $data,
            env('JWT_SECRET'),
            'HS256'
        );
        $issuedDate = date('Y-m-d H:i:s', $issuedAt->getTimestamp());
        $dbData = ['user_id' => $user->id, 'unique_id' => $uuid.$tokenId, 'token_title' => __('message.login.title'),
                    'restrictions' => null, 'permissions' => null, 'created_at' => $issuedDate,
                    'updated_at' => $issuedDate, 'expires_at' => date('Y-m-d H:i:s', $expire),
                    'last_used_at' => $issuedDate, 'refreshed_at' => null];
        $jwtTokenRepo->addToken($dbData);
        return $token;
    }
}
