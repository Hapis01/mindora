# Mindora — Contributing Guide

## Development Setup

Follow the [Quick Start](../README.md#quick-start) guide to set up your local environment.

For development, use the Vite dev server for hot-module replacement:

```bash
# Terminal 1: Laravel dev server
php artisan serve --port=8000

# Terminal 2: Vite dev server (HMR)
npm run dev

# Terminal 3: ML service
cd python-ml
uvicorn app.main:app --host 127.0.0.1 --port 8001 --reload
```

---

## Git Workflow

### Branch Naming

```
feature/<short-description>    # New features
fix/<short-description>        # Bug fixes
security/<short-description>   # Security fixes
docs/<short-description>       # Documentation
refactor/<short-description>   # Code refactoring
```

### Commit Messages

Follow Conventional Commits:

```
feat: add password reset flow
fix: correct scoring for reverse-scored Big Five questions
security: add rate limiting to /login route
docs: update ML_SERVICE.md with SHAP explanation
refactor: extract UserUnlockService from ProfileController
test: add edge cases for FieldEncryptionService
```

---

## Code Standards

### PHP / Laravel

- Follow PSR-12 coding standard
- All new code must have at least one corresponding test
- Use Form Request classes for validation — never validate in controllers directly
- Use Service classes for business logic — controllers should be thin
- All DB queries should use Eloquent or Query Builder — no raw SQL in controllers
- PHPDoc blocks for all public methods

```php
/**
 * Calculate the raw and percentile score for a completed assessment session.
 *
 * @param AssessmentSession $session The completed session
 * @return AssessmentScore           The persisted score record
 * @throws \RuntimeException         If session has no answered questions
 */
public function calculateScore(AssessmentSession $session): AssessmentScore
{
    // ...
}
```

### Python / FastAPI

- Follow PEP 8
- Use type hints for all function signatures
- Document endpoints with FastAPI's built-in docstring support

```python
@app.post("/analyze", response_model=AnalysisResponse)
async def analyze(request: AnalysisRequest, token: str = Depends(verify_token)):
    """
    Perform full ML analysis on an anonymized feature vector.
    
    Returns cluster assignment, feature importance, and SHAP values.
    No PII is accepted or stored.
    """
```

### Blade Templates

- Use `{{ $variable }}` (escaped) for all user-provided content
- Never use `{!! $variable !!}` for user-provided content
- Keep business logic out of Blade — pass prepared data from controllers
- Use Blade components for reusable UI elements

---

## Testing Requirements

All PRs must:
1. Not reduce test coverage
2. Add tests for new features
3. Pass all 52+ existing tests

```bash
# Run before submitting PR
php artisan test
```

### Test Organization

| File | What to test |
|---|---|
| `tests/Unit/` | Pure logic (services, calculations, no DB) |
| `tests/Feature/` | HTTP flows (routes, controllers, DB) |

### Test Conventions

```php
// Test method names: descriptive snake_case
public function test_profile_cannot_be_unlocked_with_wrong_password(): void

// Always use RefreshDatabase for isolation
use Illuminate\Foundation\Testing\RefreshDatabase;

// Seed required data in setUp()
protected function setUp(): void
{
    parent::setUp();
    $this->seed(RoleAndPermissionSeeder::class);
}
```

---

## Pull Request Checklist

Before opening a PR, verify:

- [ ] All tests pass: `php artisan test`
- [ ] New features have tests
- [ ] Code follows PSR-12 (PHP) / PEP 8 (Python)
- [ ] No raw SQL in controllers
- [ ] No user PII in ML or AI service calls
- [ ] Sensitive data is never logged (`LOG_LEVEL=debug` must not expose PII)
- [ ] Security-sensitive changes reviewed against OWASP checklist
- [ ] `docs/` updated if architecture changed
- [ ] Commit messages follow Conventional Commits format

---

## Local Testing Tips

```bash
# Run a single test file
php artisan test tests/Feature/ProfileSecurityTest.php

# Run tests matching a name pattern
php artisan test --filter "test_profile_cannot_be_unlocked"

# Run with extra output on failure
php artisan test --stop-on-failure

# Check if ML service is running
curl http://127.0.0.1:8001/health

# Test ML service directly
curl -X POST http://127.0.0.1:8001/analyze \
  -H "Authorization: Bearer Mindora_ml_secure_token_secret_2026" \
  -H "Content-Type: application/json" \
  -d '{"anon_id":"TEST-001","cognitive_score":70,"personality_scores":{"openness":60,"conscientiousness":75,"extraversion":50,"agreeableness":80,"neuroticism":30},"age_group":"25-34","session_count":1}'
```
