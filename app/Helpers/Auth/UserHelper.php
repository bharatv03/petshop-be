<?php

namespace App\Helpers\Auth;

use App\Helpers\CommonHelper;

class UserHelper 
{
    // get authenticated user from DB
    public function GetAuthUser($userRepository)
    {
        $commonHelper = new CommonHelper();
        $token = $commonHelper->GetAndDecodeJWT();
        $uuid = $token->data->uuid;
        $uuid = $userRepository->getByFieldSingleRecord('uuid',$uuid);
        return $uuid;
    }
}