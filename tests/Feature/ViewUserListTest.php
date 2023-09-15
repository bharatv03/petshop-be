<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class ViewUserListTest extends TestCase
{
    use DatabaseMigrations;

    /** @View User list */
    public function test_a_user_can_view_list()
    {
        $uuid = Str::uuid();
        // Create a admin user
        $admin = User::factory()->create([
            'uuid' => $uuid,
            'is_admin' => true
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        //view user with the token created above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->get('/api/v1/admin/user-listing');

        $response2->assertStatus(200);
    }

    /** @View user list failure */
    public function test_a_user_cannot_view_list_without_token()
    {
        //view user with the token created above
        $response = $this->get('/api/v1/admin/user-listing');

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(401);
    }
}
