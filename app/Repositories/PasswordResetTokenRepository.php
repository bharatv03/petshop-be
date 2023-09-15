<?php
// app/Repositories/PasswordResetTokenRepository.php

namespace App\Repositories;

use App\Models\PasswordResetToken;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function addToken($input)
    {
        return PasswordResetToken::insert($input);
    }

    public function checkToken($where)
    {
        return PasswordResetToken::where($where)->first();
    }

    public function deleteToken($where)
    {
        PasswordResetToken::where($where)->delete();
        return true;
    }
}
