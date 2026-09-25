# Mindora — API Reference

## Authentication Model

All endpoints use **cookie-based session authentication** (not tokens). The session cookie is:
- `HttpOnly` (JavaScript cannot access it)
- `SameSite=Strict` (CSRF protection)
- Encrypted at rest in the database

CSRF protection: All state-changing requests (POST, PUT, DELETE) require a CSRF token in the request body:
```html
<input type="hidden" name="_token" value="{{ csrf_token() }}">
```
Or via AJAX: include `X-CSRF-TOKEN` header.

---

## Rate Limits

| Route | Limit |
|---|---|
| `POST /login` | 5 requests/minute per IP |
| `POST /register` | 10 requests/minute per IP |
| `POST /ai/conversations/{id}/message` | 15 requests/minute per user |

When rate limited, the server returns `HTTP 429 Too Many Requests`.

---

## Public Routes (No Authentication Required)

### GET /

Landing page.

**Response:** `200 OK` — HTML page

---

### GET /login

Display the login form.

**Response:** `200 OK` — HTML page

---

### POST /login

Authenticate the user.

**Request Body:**
```
email=user@example.com&password=YourPassword123!&remember=1
```

| Field | Type | Required | Description |
|---|---|---|---|
| `email` | string | Yes | User email address |
| `password` | string | Yes | Password (min 8 chars) |
| `remember` | boolean | No | Remember me (persistent session) |

**Response:**
- `302 Redirect to /dashboard` (success)
- `302 Redirect back with errors` (invalid credentials)
- `429 Too Many Requests` (rate limited)

---

### GET /register

Display the registration form.

**Response:** `200 OK` — HTML page

---

### POST /register

Create a new user account.

**Request Body:**
```
name=Ahmad+Fauzi&email=ahmad@example.com&password=StrongPass123!&password_confirmation=StrongPass123!&terms=1
```

| Field | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Full name |
| `email` | string | Yes | Email (must be unique) |
| `password` | string | Yes | Min 8 chars, must contain uppercase, number, special char |
| `password_confirmation` | string | Yes | Must match `password` |
| `terms` | boolean | Yes | Must accept terms |

**Response:**
- `302 Redirect to /dashboard` (success)
- `302 Redirect back with validation errors`

---

## Authenticated Routes

All routes below require a valid session (i.e., user must be logged in).

### POST /logout

Log out the authenticated user.

**Response:** `302 Redirect to /` with flash message

---

### GET /dashboard

User dashboard with assessment history and statistics.

**Response:** `200 OK` — HTML page

---

## Profile Routes

### GET /profile

View the user's profile (PII shown masked by default).

**Response:** `200 OK` — HTML page

**Data returned:**
- Masked email (e.g., `a****@example.com`)
- Masked name, phone, address (shown as `---`)
- Profile unlock button

---

### POST /profile/unlock

Unlock PII for viewing (requires re-authentication).

**Request Body:**
```
password=YourPassword123!&date_of_birth=1998-05-20
```

| Field | Type | Required | Description |
|---|---|---|---|
| `password` | string | Yes | Current account password |
| `date_of_birth` | date | Yes | Date of birth as secondary factor |

**Response:**
- `302 Redirect to /profile` with success flash (PII visible for 5 min)
- `302 Redirect back` with validation errors (wrong credentials)

---

### POST /profile/lock

Manually re-lock PII.

**Response:** `302 Redirect to /profile`

---

### PUT /profile

Update profile data (requires PII to be unlocked).

**Request Body:**
```
full_name=Ahmad+Fauzi&phone_number=081234567890&address=Jl+Sudirman+No+1&gender=male&date_of_birth=1998-05-20
```

**Response:** `302 Redirect to /profile` with success flash

---

## Assessment Routes

### GET /assessments

Browse available assessments.

**Response:** `200 OK` — HTML page with assessment catalog

---

### GET /assessments/{assessment}/instructions

View assessment instructions.

**Path Parameters:**
- `assessment` — Assessment ID (integer)

**Response:** `200 OK` — HTML page with instructions

---

### POST /assessments/{assessment}/start

Start a new assessment session.

**Response:** `302 Redirect to /assessments/sessions/{new_session_id}`

---

### GET /assessments/sessions/{session}

Take the assessment (renders current question).

**Path Parameters:**
- `session` — Session ID (integer)

**Response:** `200 OK` — HTML page with question

**Authorization:** 403 if session doesn't belong to current user

---

### POST /assessments/sessions/{session}/answer

Save an answer and advance to next question.

**Request Body:**
```
question_id=42&answer=3&response_time_ms=4500
```

| Field | Type | Required | Description |
|---|---|---|---|
| `question_id` | integer | Yes | ID of the question being answered |
| `answer` | integer/string | Yes | Selected answer value |
| `response_time_ms` | integer | No | Time taken to answer (milliseconds) |

**Response:**
- `200 JSON` with `{next_url: "..."}` or `{finish_url: "..."}`

---

### POST /assessments/sessions/{session}/finish

Finish assessment and calculate results.

**Response:**
- `302 Redirect to /assessments/results/{score_id}`
- Triggers: ScoringService, MachineLearningService (async)

---

### GET /assessments/results/{score}

View assessment results with ML insights.

**Path Parameters:**
- `score` — AssessmentScore ID (integer)

**Response:** `200 OK` — HTML page with:
- Raw score + percentile rank
- Big Five subscale breakdown (if personality test)
- ML cluster archetype + SHAP explanation
- Link to AI assistant

**Authorization:** 403 if score doesn't belong to current user

---

## AI Assistant Routes

### GET /ai/conversation/{score?}

Load or create an AI conversation for a score.

**Path Parameters:**
- `score` (optional) — AssessmentScore ID to ground the conversation

**Response:** `200 OK` — HTML page with chat interface

---

### POST /ai/conversations/{conversation}/message

Send a message to the AI assistant.

**Rate Limit:** 15 requests/minute per user

**Request Body:**
```json
{
  "message": "What does my Openness score mean for my career?"
}
```

| Field | Type | Required | Description |
|---|---|---|---|
| `message` | string | Yes | User message (max 1000 chars) |

**Response:**
```json
{
  "response": "Your Openness score of 68 percentile indicates...",
  "conversation_id": 5
}
```

- `429 Too Many Requests` if rate limit exceeded

---

### DELETE /ai/conversations/{conversation}/history

Clear the conversation history.

**Response:** `200 JSON` with `{success: true}`

---

## Admin Routes

All admin routes require `super_admin` or `admin` role.

### GET /admin

Admin dashboard with platform statistics.

### GET /admin/users

User management with masked PII table.

### POST /admin/users/{user}/unlock

Unlock a specific user's PII in admin context (requires admin re-auth).

### POST /admin/users/lock

Lock all user PII in admin session.

### GET /admin/questions

Question bank management.

### PUT /admin/questions/{question}

Update a question.

### GET /admin/models

ML model registry and diagnostics.

### PUT /admin/models/{model}/toggle

Toggle a model's active/inactive status.

### GET /admin/audit-logs

View all audit logs with filters.

---

## Error Responses

| Code | Meaning |
|---|---|
| `200 OK` | Success |
| `302 Found` | Redirect (success or validation error) |
| `403 Forbidden` | Authenticated but unauthorized (wrong role or wrong resource owner) |
| `404 Not Found` | Resource does not exist |
| `419 Page Expired` | CSRF token mismatch (re-submit the form) |
| `422 Unprocessable Entity` | Validation failed (JSON requests) |
| `429 Too Many Requests` | Rate limit exceeded |
| `500 Internal Server Error` | Server-side error (check logs) |
