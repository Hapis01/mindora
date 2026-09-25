# Mindora — Security Documentation

## Security Philosophy

Mindora adopts a **defense-in-depth** strategy: multiple independent security controls are layered so that a breach of one control does not result in a complete compromise.

**Core principles:**
1. **Privacy-by-design** — PII is encrypted at rest; ML/AI never receive raw personal data
2. **Least privilege** — Each component has access only to what it needs
3. **Zero trust input** — All user input is validated and sanitized
4. **Audit everything** — All significant actions are logged immutably

---

## 1. Authentication

### Password Hashing

```php
// config/.env
HASH_DRIVER=argon2id
```

- Algorithm: **Argon2id** (RFC 9106 winner, memory-hard, resistant to GPU/ASIC attacks)
- Configuration: Default Laravel Argon2id parameters (memory=65536, time=4, threads=1)
- Why Argon2id over bcrypt: Resistant to GPU-parallel cracking; recommended by OWASP 2024

### Login Rate Limiting

Two layers of protection:
1. **Route-level**: `throttle:5,1` middleware (5 requests/minute per IP)
2. **Controller-level**: `RateLimiter` keyed by `email|IP` (5 attempts/60 sec)

```php
// web.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');
```

```php
// AuthController.php
$throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
    // Return error with retry-after seconds
}
```

### Session Management

```env
SESSION_DRIVER=database       # Sessions stored in DB, not files
SESSION_ENCRYPT=true          # Session data encrypted at rest
SESSION_SAME_SITE=strict      # Prevents CSRF via cookie policy
SESSION_SECURE_COOKIE=false   # Set true in HTTPS production
```

- Session ID regenerated on login (`$request->session()->regenerate()`)
- Session invalidated on logout (`$request->session()->invalidate()`)
- Session cookie is HttpOnly (JavaScript cannot read it)
- Session cookie SameSite=Strict (browser rejects cross-site cookie sending)

---

## 2. Field-Level Encryption (PII Protection)

### Encryption Model

All PII fields in `user_profiles` are encrypted before being written to the database:

| Field | Encrypted |
|---|---|
| `full_name` | AES-256-GCM |
| `phone_number` | AES-256-GCM |
| `address` | AES-256-GCM |
| `date_of_birth` | AES-256-GCM |
| `gender` | AES-256-GCM |

### Algorithm: AES-256-GCM Envelope Encryption

```
plaintext
    │
    ▼
AES-256-GCM encrypt with:
    ├── key: ENCRYPTION_MASTER_KEY (32 bytes, base64-encoded)
    ├── iv: random 12 bytes (generated per field)
    └── tag: 16-byte auth tag
    │
    ▼
Stored as: base64(iv + tag + ciphertext) with key version prefix
```

### Key Rotation

The `ENCRYPTION_KEY_VERSION` variable tracks the active key version. A rotation script can:
1. Decrypt all records with the old key
2. Re-encrypt with the new key
3. Bump the version number

### Profile Unlock Flow

Users must re-authenticate to view decrypted PII:

```
User clicks "Lihat Data Pribadi"
    → POST /profile/unlock {password, date_of_birth}
    → ProfileController validates password hash + decrypted DOB
    → On success: session('sensitive_data_unlocked_at') = now()
    → Profile view shows decrypted data for 5 minutes
    → After 5 minutes: auto-locked (checked on each page load)
```

---

## 3. HTTP Security Headers

All HTTP responses include these headers via `SecurityHeaders` middleware:

| Header | Value | Protection |
|---|---|---|
| `Content-Security-Policy` | See below | XSS, script injection |
| `X-Frame-Options` | `DENY` | Clickjacking |
| `X-Content-Type-Options` | `nosniff` | MIME sniffing |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` | Protocol downgrade |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Referrer leakage |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=()` | API abuse |

### Content Security Policy

```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval';
style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
font-src 'self' https://fonts.gstatic.com data:;
img-src 'self' data: https:;
connect-src 'self';
frame-ancestors 'none';
base-uri 'self';
form-action 'self';
```

**Note**: `unsafe-inline` and `unsafe-eval` for scripts are required by Alpine.js/Chart.js. A nonce-based CSP can be implemented in a future hardening iteration.

---

## 4. CSRF Protection

Laravel's `VerifyCsrfToken` middleware is active for all state-changing routes. The CSRF token is embedded in every form as a hidden field.

Additional CSRF mitigation via `SameSite=Strict` session cookie: the browser will not send the session cookie with any cross-origin POST request.

---

## 5. Role-Based Access Control (RBAC)

### Roles

| Role | Permissions |
|---|---|
| `user` | Own profile, assessments, AI assistant |
| `admin` | All user permissions + user management, questions, ML models |
| `super_admin` | All admin permissions (highest privilege) |

### Implementation

```php
// EnsureUserHasRole middleware
class EnsureUserHasRole {
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            abort(403);
        }
        return $next($request);
    }
}
```

```php
// Admin routes
Route::middleware(['role:super_admin,admin'])->prefix('admin')->group(function () {
    // ...
});
```

---

## 6. Audit Logging

All significant security events are logged to the `audit_logs` table:

| Action | Logged By |
|---|---|
| `register` | AuthController |
| `login` | AuthController |
| `failed_login` | AuthController |
| `logout` | AuthController |
| `unlock_profile` | ProfileController |
| `failed_unlock_profile` | ProfileController |
| `lock_profile` | ProfileController |
| `update_profile` | ProfileController |
| `admin_view_user_data` | AdminDashboardController |
| `toggle_ml_model` | AdminDashboardController |

Audit logs are **append-only** by design — no update or delete operations are performed on this table.

---

## 7. ML Privacy Model

The ML microservice processes **zero PII**:

```php
// MachineLearningService.php
$anonId = 'ANON-' . strtoupper(bin2hex(random_bytes(8)));

$payload = [
    'anon_id' => $anonId,                        // Random, no user link
    'cognitive_score' => $scores['cognitive'],    // Score only
    'personality_scores' => $scores['big_five'],  // Score only
    'age_group' => $ageGroup,                     // Bucketed, not exact age
    'session_count' => $sessionCount,             // Count, not session IDs
];
```

The FastAPI service receives only normalized feature vectors. It cannot reverse-engineer any individual user's identity.

---

## 8. AI Assistant Privacy Model

The AI assistant receives only:
- Assessment type and date
- Raw scores and percentile ranks
- Big Five subscale scores

It never receives:
- User name
- Email address
- Phone number
- Address
- Date of birth
- Database user ID

---

## 9. Input Validation

All user input is validated via Laravel Form Request classes:

```php
// LoginRequest.php
public function rules(): array {
    return [
        'email'    => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:8'],
    ];
}

// RegisterRequest.php
public function rules(): array {
    return [
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'email', 'unique:users,email', 'max:255'],
        'password' => ['required', 'string', 'min:8', 'confirmed', Rules\Password::defaults()],
    ];
}
```

All Blade templates use `{{ $variable }}` (auto-escaped) by default. Raw `{!! !!}` is only used for trusted admin content.

---

## 10. Production Security Checklist

Before going to production, ensure:

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `SESSION_SECURE_COOKIE=true` (requires HTTPS)
- [ ] `GEMINI_API_KEY` set to production key (Google Cloud Generative Language)
- [ ] `ML_SERVICE_TOKEN` changed from default
- [ ] `ENCRYPTION_MASTER_KEY` is unique and backed up securely
- [ ] Nginx/Apache configured to add `server_tokens off`
- [ ] Database user has least-privilege permissions
- [ ] HTTPS certificate installed (Let's Encrypt or commercial)
- [ ] Firewall blocks port 8001 (ML service) from public access
- [ ] Log monitoring configured (Laravel log channel → external SIEM)
- [ ] Backup strategy for database (including encryption keys)
- [ ] `php artisan config:cache && php artisan route:cache` run
