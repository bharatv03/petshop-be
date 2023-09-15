<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\{Str, Facades\Hash};

class EditUserAdminTest extends TestCase
{
    use DatabaseMigrations;

    /** @Edit User via Admin */
    public function test_a_user_can_be_edit_via_admin()
    {
        $uuidAdmin = Str::uuid();
        $uuidUser = Str::uuid();
        // Create a user
        $user = User::factory()->create([
            'uuid' => $uuidUser,
            'is_admin' => false
        ]);

        // Create a admin
        $userAdmin = User::factory()->create([
            'uuid' => $uuidAdmin,
            'is_admin' => true
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/admin/login', [
            'email' => $userAdmin->email,
            'password' => 'password',
        ]);
        //Edit user which logged in above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->put('/api/v1/admin/user-edit/'.$uuidUser,[
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'user123@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => 'Test address',
            'phone_number' => '9111104811',
            'avatar' => 'asdfasdf'
        ]);

        $response2->assertStatus(200);
    }

    /** @Edit user failure via admin */
    public function test_a_admin_cannot_be_edit_without_token()
    {
        $uuidAdmin = Str::uuid();

        // Create a admin
        $admin = User::factory()->create([
            'uuid' => $uuidAdmin,
            'is_admin' => true
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        //Edit user via admin
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->put('/api/v1/admin/user-edit/'.$uuidAdmin);

        // Assert that the response has a 401 status code (unauthorized)
        $response2->assertStatus(422);
    }
}
