<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class LogoutUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @Logout User */
    public function test_a_user_can_logout()
    {
        $uuid = Str::uuid();
        // Create a user
        $user = User::factory()->create([
            'uuid' => $uuid,
            'is_admin' => false
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/user/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        //Logout user which logged in above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->get('/api/v1/user/logout/');

        $response2->assertStatus(200);
    }

    /** @Logout user failure */
    public function test_a_user_cannot_logout_without_token()
    {
        //logout user failure without token
        $response = $this->get('/api/v1/user/logout');

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(401);
    }
}
