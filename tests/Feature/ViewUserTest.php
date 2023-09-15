<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class ViewUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @View User details */
    public function test_a_user_can_view_details()
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
        //view user with the token created above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->get('/api/v1/user/');

        $response2->assertStatus(200);
    }

    /** @View user details failure */
    public function test_a_user_cannot_view_without_token()
    {
        //view user with the token created above
        $response = $this->get('/api/v1/user/');

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(401);
    }
}
