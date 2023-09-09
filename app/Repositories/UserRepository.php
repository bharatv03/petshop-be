<?php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function getAll()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function getByField($field, $value)
    {
        return User::where($field, $value)->get();
    }

    public function getByFieldSingleRecord($field, $value)
    {
        return User::where($field, $value)->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(array $data, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
    }
}
