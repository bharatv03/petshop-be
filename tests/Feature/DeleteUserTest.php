<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @Delete User */
    public function test_a_user_can_be_deleted()
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
        //Delete user which logged in above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->delete('/api/v1/user/delete/');

        $response2->assertStatus(200);
    }

    /** @Delete user failure */
    public function test_a_user_cannot_be_deleted_without_token()
    {
        //delete user failure without token
        $response = $this->delete('/api/v1/user/delete');

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(401);
    }
}
