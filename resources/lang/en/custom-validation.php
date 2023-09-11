<?php
// resources/lang/en/custom-validation.php

return [
    'first_name' => [
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute field must be a string.',
        'max' => 'The :attribute field cannot exceed :max characters.',
    ],
    'last_name' => [
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute field must be a string.',
        'max' => 'The :attribute field cannot exceed :max characters.',
    ],
    'email' => [
        'required' => 'The :attribute field is required.',
        'email' => 'The :attribute field must be a valid email address.',
        'unique' => 'The :attribute has already been taken.',
    ],
    'password' => [
        'required' => 'The :attribute field is required.',
        'min' => 'The :attribute field must be at least :min characters.',
    ],
    'password_comfirmation' => [
        'required' => 'The :attribute field is required.',
        'confirmed' => 'The :attribute confirmation does not match.',
    ],
    'address' => [
        'required' => 'The :attribute field is required.',
    ],
    'phone_number' => [
        'required' => 'The :attribute field is required.',
        'digits_between' => 'The :attribute should be between :min & :max',
    ],
];