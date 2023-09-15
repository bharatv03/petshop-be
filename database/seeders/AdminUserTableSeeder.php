<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uuid = Str::uuid();
        \DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'uuid' => $uuid,
            'email' => 'admin@buckhill.co.uk',
            'password' => bcrypt('password'),
            'address' => 'test',
            'phone_number' => '9111104811',
            'is_admin' => true,
            'is_marketing' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
