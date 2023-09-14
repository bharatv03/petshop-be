<?php
// app/Repositories/JwtTokenRepositoryInterface.php

namespace App\Repositories;

interface JwtTokenRepositoryInterface
{
    public function addToken($input);
    public function checkToken($where);
    public function updateToken($where, $data);
}
