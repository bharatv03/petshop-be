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

    public function updateByUuid(array $data, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function deleteByUuidNotAdmin($uuid)
    {
        $user = User::where([['uuid','=', $uuid],['is_admin','=', false]])->first();
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    public function getPaginatedData($select = [], $limit = 2, $page = 2, $orderBy = '', $sortType = '')
    {
        $user = User::select($select)->orderBy($orderBy,$sortType)->paginate($limit);
        return $user;
    }
}
