<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class DeleteUserAdminTest extends TestCase
{
    use DatabaseMigrations;

    /** @Delete User via Admin */
    public function test_a_user_can_be_deleted_via_admin()
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
        //Delete user which logged in above
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->delete('/api/v1/admin/user-delete/'.$uuidUser);

        $response2->assertStatus(200);
    }

    /** @Delete user failure */
    public function test_a_admin_account_cannot_be_deleted()
    {
        $uuidAdmin = Str::uuid();

        // Create a admin
        $user = User::factory()->create([
            'uuid' => $uuidAdmin,
            'is_admin' => true
        ]);

        // Attempt to login with correct credentials
        $response = $this->post('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        //Delete without authentication 
        $response2 = $this->delete('/api/v1/admin/user-delete/'.$uuidAdmin);

        // Assert that the response has a 401 status code (unauthorized)
        $response2->assertStatus(401);
    }
}
