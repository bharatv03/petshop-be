<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use App\Helpers\Auth\TokenHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;

class CommonHelper
{
    // get jwt info from header
    public function getRawJwt()
    {
        $header = request()->header('Authorization', '');
        // check if header exists
        if (empty($header)) {
            throw new AuthenticationException('authorization header not found');
        }

        // check if bearer token exists
        if ((Str::startsWith($header, 'Bearer'))) {
            // extract token
            $jwt = Str::substr($header, 7);
            if (!$jwt) {
                throw new AuthenticationException('could not extract token');
            }
        } else {
            throw new AuthenticationException('token not found');
        }

        return $jwt;
    }

    public function decodeRawJwt($jwt)
    {
        // use secret key to decode token
        $secretKey = env('JWT_SECRET');
        try {
            $token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
        } catch(Exception $e) {
            throw new AuthenticationException('unauthorized');
        }

        return $token;
    }

    public function getAndDecodeJwt()
    {
        $jwt = $this->getRawJwt();
        return $this->decodeRawJwt($jwt);
    }

    public static function loginAttempt($input, $remember, $jwtTokenRepo)
    {
        if (Auth::attempt($input, $remember)) {
            $user = Auth::user();
            $tokenHelper = new TokenHelper();
            $token = $tokenHelper->generateToken($user, $jwtTokenRepo);
            return ['user' => $user, 'token' => $token];
        } else {
            return ['error' => __('message.invalid_login')];
        }
    }

    public static function gridManagement($repObj, $gridObj)
    {
        $sortCol = 'created_at';
        $sortType = 'asc';
        $page = 1;
        $limit = 10;
        $request = request();
        if ($request->sort) {
            $sortCol = $request->sort;
        }
        if ($request->desc) {
            $sortType = 'desc';
        }
        if ($request->page) {
            $page = $request->page;
        }
        if ($request->limit) {
            $limit = $request->limit;
        }

        $gridData = $gridObj->getField();
        $fieldArray = [];
        foreach ($gridData as $value) {
            $fieldArray[] = $value['name'];
        }
        return $repObj->getPaginatedData($fieldArray, $limit, $page, $sortCol, $sortType);
    }

    public static function deleteUser($uuid, $repObj)
    {
        $deleteUser = $repObj->deleteByUuidNotAdmin($uuid);
        if ($deleteUser) {
            $response = ['success' => __('message.user.delete_success')];
        } else {
            $response = ['error' => __('message.user.delete_error')];
        }
        return $response;
    }

    public static function getUserDetails($repObj, $uuid)
    {
        $userDetails = $repObj->getByFieldSingleRecord('uuid', $uuid);

        if ($userDetails) {
            $response = ['success' => __('message.user.fetch_success'),'data' => $userDetails];
        } else {
            $response = ['error' => __('message.user.fetch_error')];
        }
        return $response;
    }

    public static function logout($uniqueToken, $jwtTokenRepo)
    {
        $data['expires_at'] = date('Y-m-d H:i:s');
        $where['unique_id'] = $uniqueToken;
        $jwtTokenRepo->updateToken($where, $data);
    }
}
