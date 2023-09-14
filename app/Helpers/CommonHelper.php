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

    public static function LoginAttempt($input, $remember, $jwtTokenRepo)
    {
        if (Auth::attempt($input, $remember))
        {
            $user = Auth::user();
            $tokenHelper = new TokenHelper;
            $token = $tokenHelper->GenerateToken($user, $jwtTokenRepo);
            $success = ['user' => $user, 'token' => $token];
            return $success;
        }
        else{
            $error = ['error' => __('message.invalid_login')];
            return $error;
        }
    }

    public static function GridManagement($repObj, $gridObj)
    {
        $sortCol = 'created_at';
        $sortType = 'asc';
        $page = 1;
        $limit = 10;
        $request = request();
        if ($request->sort)
            $sortCol = $request->sort;
        if ($request->desc)
            $sortType = 'desc';
        if ($request->page)
            $page = $request->page;
        if ($request->limit)
            $limit = $request->limit;


        $gridData = $gridObj->getField();
        $fieldArray = [];
        foreach ($gridData as $value) {
            $fieldArray [] = $value['name'];
        }
        $gridData = $repObj->getPaginatedData($fieldArray, $limit, $page, $sortCol, $sortType);
        return $gridData;
    }

    public static function DeleteUser($uuid, $repObj)
    {
        $deleteUser = $repObj->deleteByUuidNotAdmin($uuid);
        if($deleteUser)
            $response = ['success' => __('message.user.delete_success')];
        else
            $response = ['error' => __('message.user.delete_error')];
        return $response;
    }

    public static function GetUserDetails($repObj, $uuid)
    {
        $userDetails = $repObj->getByFieldSingleRecord('uuid', $uuid);
        
        if($userDetails)
            $response = ['success' => __('message.user.fetch_success'),'data' => $userDetails];
        else
            $response = ['error' => __('message.user.fetch_error')];
        return $response;
    }

    public static function Logout($uniqueToken, $jwtTokenRepo)
    {
        $data['expires_at'] = date('Y-m-d H:i:s');
        $where['unique_id'] = $uniqueToken;
        $jwtTokenRepo->updateToken($where, $data);
    }
}