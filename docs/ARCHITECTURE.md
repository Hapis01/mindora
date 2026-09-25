# Mindora — Architecture Documentation

## Overview

Mindora is built as a **monolithic Laravel application** with a **sidecar ML microservice**. The core application handles all user-facing functionality, while a separate Python FastAPI service handles computationally intensive ML operations.

---

## System Components

```
┌─────────────────────────────────────────────────────────┐
│                      CLIENT BROWSER                     │
│  HTML/CSS/JS (Blade + Alpine.js + Chart.js via Vite)    │
└───────────────────────┬─────────────────────────────────┘
                        │ HTTP (cookies/sessions)
┌───────────────────────▼─────────────────────────────────┐
│              LARAVEL APPLICATION (PHP 8.2+)             │
│                                                         │
│  ┌─────────┐  ┌──────────┐  ┌──────────┐  ┌────────┐  │
│  │  Auth   │  │Assessment│  │    AI    │  │ Admin  │  │
│  │ Module  │  │  Engine  │  │Assistant │  │ Center │  │
│  └─────────┘  └──────────┘  └──────────┘  └────────┘  │
│                                                         │
│  ┌─────────────────┐  ┌───────────────────────────────┐ │
│  │ FieldEncryption │  │    MachineLearningService      │ │
│  │   (AES-256-GCM) │  │  (HTTP client to FastAPI)     │ │
│  └─────────────────┘  └───────────────────────────────┘ │
└───────┬───────────────────────┬─────────────────────────┘
        │                       │
┌───────▼───────┐   ┌───────────▼─────────┐
│  MySQL 8.0    │   │  FastAPI ML Service  │
│  (Primary DB) │   │  (Python 3.11+)      │
│               │   │  scikit-learn + SHAP │
└───────────────┘   └──────────┬──────────┘
                    ┌──────────▼──────────┐
                    │  Google Gemini API  │
                    │  (gemini-3.6-flash) │
                    └─────────────────────┘
```

---

## Directory Structure

```
psikotes-online/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/AdminDashboardController.php
│   │   │   ├── AIController.php
│   │   │   ├── AssessmentController.php
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── HomeController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── EnsureUserHasRole.php   # RBAC
│   │   │   ├── SecurityHeaders.php      # OWASP headers
│   │   │   └── PreventBruteForce.php    # Extensible brute force hook
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       └── RegisterRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── UserProfile.php
│   │   ├── Assessment.php
│   │   ├── Question.php
│   │   ├── AssessmentSession.php
│   │   ├── UserAnswer.php
│   │   ├── AssessmentScore.php
│   │   ├── AssessmentResult.php
│   │   ├── AIConversation.php
│   │   ├── AIMessage.php
│   │   ├── MLModel.php
│   │   ├── AuditLog.php
│   │   └── Role.php
│   ├── Services/
│   │   ├── AI/
│   │   │   ├── AIProviderInterface.php
│   │   │   ├── GeminiProvider.php      # Google Gemini Live Provider
│   │   │   └── DeepSeekProvider.php    # Fallback Provider
│   │   ├── FieldEncryptionService.php  # AES-256-GCM
│   │   ├── MachineLearningService.php  # FastAPI client
│   │   └── ScoringService.php          # Assessment scoring
│   └── Providers/
│       └── AppServiceProvider.php      # Rate limiter registration
├── python-ml/
│   ├── app/
│   │   ├── main.py          # FastAPI endpoints
│   │   └── ml_service.py    # ML logic
│   └── requirements.txt
├── resources/views/          # Blade templates
├── routes/web.php            # All routes
├── database/
│   ├── migrations/           # 20+ migration files
│   └── seeders/
├── tests/
│   ├── Feature/
│   └── Unit/
└── docs/                     # This documentation
```

---

## Database Schema

### Core Tables

| Table | Purpose | Encryption |
|---|---|---|
| `users` | Auth credentials, role | Password (Argon2id) |
| `user_profiles` | PII data | AES-256-GCM on all fields |
| `roles` | Role definitions | None |
| `user_role` | Pivot: user ↔ role | None |
| `sessions` | Session storage | Session-level encryption |
| `cache` | Cache storage | None |
| `jobs` | Queue jobs | None |

### Assessment Tables

| Table | Purpose |
|---|---|
| `assessments` | Assessment catalog (Cognitive, Big Five) |
| `questions` | Question bank (30 + 50 questions) |
| `assessment_sessions` | Active user sessions |
| `user_answers` | Individual question answers |
| `assessment_scores` | Calculated scores + percentiles |
| `assessment_results` | Full result context (ML + JSON) |

### AI & ML Tables

| Table | Purpose |
|---|---|
| `ai_conversations` | AI chat session per result |
| `ai_messages` | Individual chat messages |
| `ml_models` | Model registry (active/inactive) |

### Audit Table

| Table | Purpose |
|---|---|
| `audit_logs` | Immutable action log (all significant events) |

---

## Data Flow: Assessment → ML → AI

```
1. User completes assessment
   └── AssessmentController::finish()
         └── ScoringService::calculateScore()
               ├── raw_score, percentile_rank
               ├── sub_scores (Big Five: O/C/E/A/N)
               └── Saved to assessment_scores

2. ML Analysis (async, same request)
   └── MachineLearningService::analyze()
         ├── Creates anon_id (ANON-{random})
         ├── Builds feature vector (scores only, NO PII)
         ├── POST http://127.0.0.1:8001/analyze
         │     ├── K-Means clustering → cluster_id, cluster_label
         │     ├── Random Forest → feature_importance[]
         │     └── TreeSHAP → shap_values{}
         └── ML results stored in assessment_results.ml_insights (JSON)

3. AI Assistant (on demand)
   └── AIController::sendMessage()
         ├── Loads assessment_results context
         ├── Builds system prompt (SCORES ONLY, zero PII)
         ├── POST https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent
         └── Response stored in ai_messages
```

---

## Security Architecture

See [SECURITY.md](SECURITY.md) for complete details.

**Summary:**
- Passwords: Argon2id (memory-hard)
- PII: AES-256-GCM envelope encryption
- Sessions: Database-backed, encrypted, SameSite=Strict
- Transport: HSTS enforced via SecurityHeaders middleware
- RBAC: Custom role middleware (super_admin, admin, user)
- Audit: All significant actions logged immutably

---

## Request Lifecycle

```
Browser Request
    │
    ▼
SecurityHeaders middleware (adds OWASP headers)
    │
    ▼
CSRF Verification (VerifyCsrfToken)
    │
    ▼
Rate Limiter (throttle:5,1 on /login, throttle:15,1 on /ai/*)
    │
    ▼
Auth middleware (guest/auth)
    │
    ▼
Role middleware (admin routes only)
    │
    ▼
Controller → Service → Model → Database
    │
    ▼
Response with security headers
```
