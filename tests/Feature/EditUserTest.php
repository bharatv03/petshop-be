<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\{Str, Facades\Hash};

class EditUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @Edit User  */
    public function test_a_user_can_be_edit()
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
        //Edit user which logged in above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->put('/api/v1/user/edit/',[
            'first_name' => 'Testgg',
            'last_name' => 'Test',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'Test address',
            'phone_number' => '9111104811',
            'avatar' => 'asdfasdf'
        ]);

        $response2->assertStatus(200);
    }

    /** @Edit user failure via admin */
    public function test_a_user_cannot_edit_without_token()
    {
        //Edit user via admin
        $response = $this->put('/api/v1/user/edit/');

        // Assert that the response has a 401 status code (unauthorized)
        $response->assertStatus(401);
    }
}
