<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Psikotes online,', false);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Mindora');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Buat akun Mindora');
    }

    public function test_users_can_register_with_valid_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Pratama',
            'email' => 'budi@example.com',
            'date_of_birth' => '1998-05-20',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('user'));

        // Verify Argon2id prefix in password hash
        $this->assertStringStartsWith('$argon2id$', $user->password);

        // Verify UserProfile with AES-256 encrypted DOB and verification hash
        $this->assertNotNull($user->profile);
        $this->assertNotNull($user->profile->encrypted_dob);
        $this->assertNotNull($user->profile->dob_verification_hash);
        $this->assertTrue($user->profile->verifyDob('1998-05-20', app(\App\Services\EncryptionService::class)));

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'register',
            'actor_id' => $user->id,
        ]);
    }

    public function test_users_cannot_register_without_date_of_birth(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi No DOB',
            'email' => 'nodob@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['date_of_birth']);
        $this->assertGuest();
    }

    public function test_users_cannot_register_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Weak',
            'email' => 'weak@example.com',
            'date_of_birth' => '1998-05-20',
            'password' => '12345',
            'password_confirmation' => '12345',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!'),
        ]);
        $user->assignRole('user');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'CorrectPassword123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'login',
            'actor_id' => $user->id,
        ]);
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPassword!',
        ]);

        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'failed_login',
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::where('email', 'user@Mindora.test')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'logout',
            'actor_id' => $user->id,
        ]);
    }
}
