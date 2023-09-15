<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, DatabaseMigrations, 
    DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\{User, PasswordResetToken};

class ForgotPasswordTest extends TestCase
{
    use DatabaseMigrations;

    /** @User reset password token */
    public function test_user_can_forgot_password()
    {
        $uuidUser = Str::uuid();
        // Create a user
        $user = User::factory()->create([
            'uuid' => $uuidUser,
            'is_admin' => false
        ]);

        // Attempt to send password link with correct details
        $response = $this->post('/api/v1/user/forgot-password', [
            'email' => $user->email
        ]);

        $response->assertStatus(200);
    }

    /** @User forgot password no email address */
    public function test_forgot_password_requires_email()
    {
        // Attempt to send request without email
        $response = $this->post('/api/v1/user/forgot-password', [
            'email' => ''
        ]);
        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(422);
    }
}
