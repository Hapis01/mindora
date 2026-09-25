<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnlockProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\EncryptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user profile.
     * Sensitive fields are masked by default unless temporarily unlocked.
     */
    public function show(EncryptionService $encryptionService): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure user has a profile record
        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        $unlockedUntil = session('sensitive_data_expires_at');
        $isUnlocked = $unlockedUntil && now()->timestamp < $unlockedUntil;

        $remainingSeconds = $isUnlocked ? max(0, $unlockedUntil - now()->timestamp) : 0;

        $profileData = [];

        if ($isUnlocked) {
            try {
                $profileData = $profile->getDecryptedData($encryptionService);

                AuditLog::record(
                    action: 'decrypt_sensitive_data',
                    target: $profile,
                    metadata: ['status' => 'success', 'fields' => ['name', 'dob', 'phone', 'address']],
                    actorId: $user->id,
                    actorType: User::class
                );
            } catch (\Exception $e) {
                // If decryption fails due to corrupted data/keys
                $isUnlocked = false;
                session()->forget(['sensitive_data_unlocked_at', 'sensitive_data_expires_at']);
                session()->flash('error', 'Gagal memverifikasi integritas enkripsi: ' . $e->getMessage());
            }
        }

        return view('profile.show', [
            'user' => $user,
            'profile' => $profile,
            'isUnlocked' => $isUnlocked,
            'remainingSeconds' => $remainingSeconds,
            'profileData' => $profileData,
        ]);
    }

    /**
     * Temporarily unlock sensitive profile data using Password + Secondary Factor (Date of Birth).
     */
    public function unlock(UnlockProfileRequest $request, EncryptionService $encryptionService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $throttleKey = 'unlock_profile|' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'password' => "Terlalu banyak percobaan buka profil yang gagal. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $profile = $user->profile ?? UserProfile::firstOrCreate(['user_id' => $user->id]);

        // Factor 1: Verify Password
        if (!Hash::check($request->validated('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 300);

            AuditLog::record(
                action: 'failed_unlock_profile',
                target: $profile,
                metadata: ['reason' => 'invalid_password'],
                actorId: $user->id,
                actorType: User::class
            );

            return back()->withErrors([
                'password' => 'Kata sandi tidak sesuai. Verifikasi identitas gagal.',
            ]);
        }

        // Factor 2: Verify Date of Birth (Secondary verification factor)
        // If profile DOB has not yet been configured, allow password verification to establish it
        if ($profile->encrypted_dob !== null && !$profile->verifyDob($request->validated('date_of_birth'), $encryptionService)) {
            RateLimiter::hit($throttleKey, 300);

            AuditLog::record(
                action: 'failed_unlock_profile',
                target: $profile,
                metadata: ['reason' => 'invalid_dob_factor'],
                actorId: $user->id,
                actorType: User::class
            );

            return back()->withErrors([
                'date_of_birth' => 'Tanggal lahir yang dimasukkan tidak cocok dengan data profil Anda.',
            ]);
        }

        // Verification Succeeded
        RateLimiter::clear($throttleKey);

        // Session-based temporary unlock: 5 minutes timeout
        session([
            'sensitive_data_unlocked_at' => now()->timestamp,
            'sensitive_data_expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        AuditLog::record(
            action: 'unlock_profile',
            target: $profile,
            metadata: ['timeout_minutes' => 5],
            actorId: $user->id,
            actorType: User::class
        );

        return redirect()->route('profile.show')->with('success', 'Data profil sensitif berhasil dibuka selama 5 menit.');
    }

    /**
     * Lock sensitive profile data immediately.
     */
    public function lock(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        session()->forget(['sensitive_data_unlocked_at', 'sensitive_data_expires_at']);

        AuditLog::record(
            action: 'lock_profile',
            target: $user->profile,
            metadata: ['mode' => 'manual_lock'],
            actorId: $user->id,
            actorType: User::class
        );

        return redirect()->route('profile.show')->with('info', 'Data profil Anda telah dikunci kembali (masked).');
    }

    /**
     * Update user profile with AES-256-GCM envelope encryption.
     */
    public function update(UpdateProfileRequest $request, EncryptionService $encryptionService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Must be unlocked to update
        $unlockedUntil = session('sensitive_data_expires_at');
        if (!$unlockedUntil || now()->timestamp >= $unlockedUntil) {
            return back()->withErrors([
                'unlock' => 'Anda harus membuka (unlock) data profil terlebih dahulu sebelum dapat memperbarui informasi sensitif.',
            ]);
        }

        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $profile->setEncryptedProfile($request->validated(), $encryptionService);
        $profile->save();

        // Sync display name in users table
        $user->update(['name' => $request->validated('name')]);

        AuditLog::record(
            action: 'profile_update',
            target: $profile,
            metadata: ['updated_fields' => array_keys($request->validated())],
            actorId: $user->id,
            actorType: User::class
        );

        return redirect()->route('profile.show')->with('success', 'Profil Anda berhasil diperbarui dan disimpan secara terenkripsi (AES-256-GCM).');
    }
}
