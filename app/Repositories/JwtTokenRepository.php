<?php
// app/Repositories/JwtTokenRepositoryInterface.php

namespace App\Repositories;

use App\Models\JwtToken;

class JwtTokenRepository implements JwtTokenRepositoryInterface
{

    public function addToken($input)
    {
        return JwtToken::insert($input);
    }

    public function checkToken($where)
    {
        return JwtToken::where($where)->first();
    }

    public function updateToken($where, $data)
    {
        $passwordToken = JwtToken::where($where)->update($data);
        return true;
    }
}
