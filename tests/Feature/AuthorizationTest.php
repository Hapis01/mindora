<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guests_are_redirected_from_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_guests_are_redirected_from_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('login'));
    }

    public function test_standard_users_can_access_user_dashboard(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat datang');
    }

    public function test_standard_users_cannot_access_admin_dashboard(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@Mindora.test')->first();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrasi Mindora');
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $superAdmin = User::where('email', 'superadmin@Mindora.test')->first();

        $response = $this->actingAs($superAdmin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrasi Mindora');
    }
}
