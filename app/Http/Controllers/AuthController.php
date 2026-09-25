<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
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

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba kembali dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            AuditLog::record(
                action: 'login',
                target: $user,
                metadata: ['auth_method' => 'web_credentials'],
                actorId: $user->id,
                actorType: User::class
            );

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        AuditLog::record(
            action: 'failed_login',
            target: null,
            metadata: ['attempted_email' => $request->input('email')],
            actorId: null,
            actorType: null
        );

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request, EncryptionService $encryptionService): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')), // Uses default argon2id driver
        ]);

        $user->assignRole('user');

        // Initialize UserProfile with AES-256-GCM envelope encryption and DOB HMAC verification hash
        $profile = new UserProfile(['user_id' => $user->id]);
        $profile->setEncryptedProfile([
            'name' => $request->validated('name'),
            'date_of_birth' => $request->validated('date_of_birth'),
        ], $encryptionService);
        $profile->save();

        AuditLog::record(
            action: 'register',
            target: $user,
            metadata: ['role' => 'user'],
            actorId: $user->id,
            actorType: User::class
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Selamat datang di Mindora! Akun Anda berhasil dibuat.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            AuditLog::record(
                action: 'logout',
                target: $user,
                metadata: [],
                actorId: $user->id,
                actorType: User::class
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil keluar.');
    }
}
