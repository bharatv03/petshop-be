<?php

namespace App\Gridclass;

class UserGrid
{
    public function getField()
    {
        $fieldArray[] = [
            'name' => 'id',
            'label' => 'User id',
            'sort' => 0,
            'search' => 0,
            'visible' => 0,
            'function' => '',
            'alias' => 'id',
            'second' => '',
        ];
        $fieldArray[] = [
            'name' => 'first_name',
            'label' => 'First Name',
            'sort' => 1,
            'search' => 1,
            'visible' => 1,
            'function' => '',
            'alias' => 'first_name',
            'second' => '',
        ];
        $fieldArray[] = [
            'name' => 'email',
            'label' => 'Email',
            'sort' => 1,
            'search' => 1,
            'visible' => 1,
            'function' => '',
            'alias' => 'email',
            'second' => '',
        ];
        $fieldArray[] = [
            'name' => 'phone_number',
            'label' => 'Phone',
            'sort' => 1,
            'search' => 1,
            'visible' => 1,
            'function' => '',
            'alias' => 'phone_number',
            'second' => '',
        ];
        $fieldArray[] = [
            'name' => 'created_at',
            'label' => 'Registration date',
            'sort' => 1,
            'search' => 1,
            'visible' => 1,
            'function' => 'date',
            'alias' => 'created_at',
            'second' => '',
        ];
        $fieldArray[] = [
            'name' => 'is_marketing',
            'label' => 'Marketing',
            'sort' => 1,
            'search' => 1,
            'visible' => 1,
            'function' => '',
            'alias' => 'is_marketing',
            'second' => 'id',
        ];
        return $fieldArray;
    }
}
