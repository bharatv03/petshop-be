<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, DatabaseMigrations, 
    DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @User Login Test With correct credentials */
    public function test_user_login_with_success(): void
    {
        $uuidUser = Str::uuid();
        // Create a user
        $user = User::factory()->create([
            'uuid' => $uuidUser,
            'is_admin' => false
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/user/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    /** @User Login Test With incorrect credentials */
    public function test_user_login_with_failure(): void
    {
        // Attempt to login with incorrect credentials
        $response = $this->post('/api/v1/user/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'incorrectpassword',
        ]);

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(422);
    }
}
