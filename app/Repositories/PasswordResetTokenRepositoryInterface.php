<?php
// app/Repositories/PasswordResetTokenRepositoryInterface.php

namespace App\Repositories;

interface PasswordResetTokenRepositoryInterface
{
    public function addToken($input);
    public function checkToken($where);
    public function deleteToken($where);
}
