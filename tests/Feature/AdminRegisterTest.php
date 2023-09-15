<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, DatabaseMigrations, 
    DatabaseTransactions};
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRegisterTest extends TestCase
{
    use DatabaseMigrations;

    /** @Admin registration */
    public function test_a_admin_can_register()
    {

        // Attempt to registration with correct details
        $response = $this->post('/api/v1/admin/create', [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => '123-asdsfd-12312',
            'address' => 'Test address',
            'phone_number' => '9111104811'
        ]);

        $response->assertStatus(200);
    }

    /** @Admin Registration Test With incorrect details */
    public function test_admin_registration_requires_avatar()
    {
        // Attempt to registartion with incorrect details
        $response = $this->post('/api/v1/admin/create', [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'Test address',
            'phone_number' => '9111104811'
        ]);

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(422);
    }
}
