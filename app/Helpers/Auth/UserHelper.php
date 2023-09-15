<?php

namespace App\Helpers\Auth;

use App\Helpers\CommonHelper;

class UserHelper
{
    // get authenticated user from DB
    public function getAuthUser($userRepository)
    {
        $commonHelper = new CommonHelper();
        $token = $commonHelper->getAndDecodeJwt();
        $uuid = $token->data->uuid;
        $uuid = $userRepository->getByFieldSingleRecord('uuid', $uuid);
        return $uuid;
    }
}
