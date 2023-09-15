<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, DatabaseMigrations, 
    DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class AdminLoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @Admin Login Test With correct credentials */
    public function test_admin_login_with_success(): void
    {
        $uuidAdmin = Str::uuid();
        // Create a admin user
        $admin = User::factory()->create([
            'uuid' => $uuidAdmin,
            'is_admin' => true
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    /** @Admin Login Test With incorrect credentials */
    public function test_admin_login_with_fail(): void
    {
        // Attempt to login with incorrect credentials
        $response = $this->post('/api/v1/admin/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'incorrectpassword',
        ]);

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(422);
    }
}
