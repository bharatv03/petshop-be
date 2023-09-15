<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker, 
    DatabaseMigrations, DatabaseTransactions};
use Illuminate\Support\{Facades\Hash, Str};
use Tests\TestCase;
use App\Models\User;

class LogoutAdminTest extends TestCase
{
    use DatabaseMigrations;

    /** @Delete User via Admin */
    public function test_a_admin_can_logout()
    {
        $uuidAdmin = Str::uuid();

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
        //Admin logout
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer:' . $response['data']['token'],
        ])->get('/api/v1/admin/logout/');

        $response2->assertStatus(200);
    }

    /** @Delete user failure */
    public function test_a_admin_account_cannot_logout()
    {
        $uuidAdmin = Str::uuid();

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
        //Delete without authentication 
        $response2 = $this->get('/api/v1/admin/logout/');

        // Assert that the response has a 401 status code (unauthorized)
        $response2->assertStatus(401);
    }
}
