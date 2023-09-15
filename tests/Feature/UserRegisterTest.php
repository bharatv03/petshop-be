<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, DatabaseMigrations, 
    DatabaseTransactions};
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @User registration with correct details */
    public function test_a_user_can_register(): void
    {

        // Attempt to registration with correct details
        $response = $this->post('/api/v1/user/create', [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'Test address',
            'avatar' => 'asdfasfda-asdfasdf',
            'phone_number' => '9111104811'
        ]);

        $response->assertStatus(200);
    }

    /** @User Registration Test With incorrect details */
    public function test_user_registration_requires_password_confirmation()
    {
        // Attempt to registartion with incorrect details
        $response = $this->post('/api/v1/user/create', [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'address' => 'Test address',
            'phone_number' => '9111104811'
        ]);

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(422);
    }
}
