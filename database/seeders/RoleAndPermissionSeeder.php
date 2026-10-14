<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Administrator', 'description' => 'Akses penuh ke seluruh sistem, AI, ML, dan konfigurasi keamanan.']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Pengelolaan assessment, bank soal, user, dan statistik model.']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'User', 'description' => 'Pengguna mandiri yang mengerjakan psikotes dan mengakses AI assistant.']
        );

        // 2. Create Permissions
        $permissions = [
            'view_admin_dashboard' => 'Melihat dashboard administrasi dan statistik',
            'manage_users' => 'Mengelola akun pengguna dan melihat data',
            'manage_assessments' => 'Mengelola kategori dan instrumen assessment',
            'manage_questions' => 'Mengelola bank soal, opsi, dan bobot skor',
            'view_audit_logs' => 'Melihat catatan audit log keamanan sistem',
            'manage_models' => 'Melihat dan mengelola model Machine Learning',
            'manage_ai_settings' => 'Mengatur provider dan konfigurasi AI Assistant',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $displayName) {
            $permissionModels[$name] = Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
        }

        // 3. Assign Permissions
        // Super Admin gets all permissions
        $superAdminRole->permissions()->sync(collect($permissionModels)->pluck('id'));

        // Admin gets operational permissions
        $adminPermissions = collect($permissionModels)->only([
            'view_admin_dashboard',
            'manage_users',
            'manage_assessments',
            'manage_questions',
            'view_audit_logs',
            'manage_models',
        ])->pluck('id');
        $adminRole->permissions()->sync($adminPermissions);

        // 4. Create Initial Seeded Users (Passwords hashed strictly with Argon2id)
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@Mindora.test'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdminPassword123!'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@Mindora.test'],
            [
                'name' => 'Admin Mindora',
                'password' => Hash::make('AdminPassword123!'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        $standardUser = User::firstOrCreate(
            ['email' => 'user@Mindora.test'],
            [
                'name' => 'Muhammad Hafiz',
                'password' => Hash::make('UserPassword123!'),
                'email_verified_at' => now(),
            ]
        );
        $standardUser->assignRole('user');

        // 5. Seed Encryption Metadata (v1)
        \Illuminate\Support\Facades\DB::table('encryption_metadata')->insertOrIgnore([
            'key_version' => 'v1',
            'algorithm' => 'AES-256-GCM',
            'status' => 'active',
            'description' => 'Initial AES-256-GCM envelope encryption key version.',
            'created_at' => now(),
        ]);

        // 6. Seed Encrypted Profile for Standard User (never overwrite an existing profile)
        $standardProfile = \App\Models\UserProfile::firstOrNew(['user_id' => $standardUser->id]);
        if (!$standardProfile->exists) {
            $standardProfile->setEncryptedProfile([
                'name' => 'Muhammad Hafiz',
                'date_of_birth' => '1998-05-20',
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman Kav. 21, Jakarta Selatan',
                'gender' => 'male',
            ], app(\App\Services\EncryptionService::class));
            $standardProfile->save();
        }

        // Audit only on first initialization so re-running the seeder adds no duplicate rows
        if ($superAdminUser->wasRecentlyCreated) {
            AuditLog::record(
                action: 'system_initialized_roles_and_users',
                target: $superAdminUser,
                metadata: ['seeded_roles' => 3, 'seeded_users' => 3, 'seeded_profiles' => 1],
                actorId: $superAdminUser->id,
                actorType: User::class
            );
        }
    }
}
