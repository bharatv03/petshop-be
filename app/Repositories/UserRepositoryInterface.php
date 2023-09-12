<?php
// app/Repositories/UserRepositoryInterface.php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function getByField($field, $value);
    public function getByFieldSingleRecord($field, $value);
    public function create(array $data);
    public function update(array $data, $id);
    public function updateByUuid(array $data, $uuid);
    public function deleteByUuidNotAdmin($uuid);
    public function getPaginatedData($select = [], $limit = 2, $page = 2, $orderBy = '', $sortType = '');
}
