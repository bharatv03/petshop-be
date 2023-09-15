<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, DatabaseMigrations, 
    DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\{User, PasswordResetToken};

class ResetPasswordTokenTest extends TestCase
{
    use DatabaseMigrations;

    /** @User reset password token */
    public function test_reset_password_success(): void
    {
        $uuidUser = Str::uuid();
        $token = Str::random(60);
        // Create a user
        $user = User::factory()->create([
            'uuid' => $uuidUser,
            'is_admin' => false
        ]);

        // Create token with the email in token table
        $admin = PasswordResetToken::factory()->create([
            'email' => $user->email,
            'token' => $token,
        ]);

        // Attempt to reset password with token and details required
        $response = $this->post('/api/v1/user/reset-password-token', [
            'email' => $user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'token' => $token
        ]);

        $response->assertStatus(200);
    }

    /** @attempt reset password without token */
    public function test_reset_password_token_required()
    {
       // Attempt to reset password without token 
       $response = $this->post('/api/v1/user/reset-password-token', [
            'email' => 'user@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);
        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(422);
    }
}
