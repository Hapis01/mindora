# Mindora — ML Microservice Documentation

## Overview

The ML microservice is a standalone **FastAPI** (Python) application that handles all machine learning operations for Mindora. It is intentionally decoupled from the main Laravel application to enable independent scaling, model updates, and language-specific ML libraries.

**Key properties:**
- Stateless: No database connection. Accepts requests, returns results.
- Zero PII: Input is anonymized feature vectors only.
- Explainable: SHAP values returned for every prediction.

---

## Running the Service

```bash
cd python-ml
pip install -r requirements.txt
uvicorn app.main:app --host 127.0.0.1 --port 8001
```

For production with multiple workers:
```bash
uvicorn app.main:app --host 0.0.0.0 --port 8001 --workers 4
```

The service binds to `127.0.0.1:8001` by default (local-only access). In production, **do not expose port 8001 publicly** — it should only be accessible from the Laravel application server.

---

## Authentication

All endpoints (except `/health`) require a Bearer token:

```
Authorization: Bearer <ML_SERVICE_TOKEN>
```

The token is configured via the `ML_SERVICE_TOKEN` environment variable in Laravel's `.env`.

---

## API Endpoints

### GET /health

Returns the service health status and loaded model info.

**Request:**
```http
GET /health HTTP/1.1
```

**Response:**
```json
{
  "status": "healthy",
  "models_loaded": ["psychometric-ensemble-v1"],
  "version": "1.0.0"
}
```

---

### POST /analyze

Performs full ML analysis on a feature vector.

**Request:**
```http
POST /analyze HTTP/1.1
Authorization: Bearer <token>
Content-Type: application/json
```

**Request Body:**
```json
{
  "anon_id": "ANON-75B439DE599B",
  "cognitive_score": 72.5,
  "personality_scores": {
    "openness": 68.0,
    "conscientiousness": 75.0,
    "extraversion": 45.0,
    "agreeableness": 82.0,
    "neuroticism": 30.0
  },
  "age_group": "25-34",
  "session_count": 2
}
```

**Response:**
```json
{
  "anon_id": "ANON-75B439DE599B",
  "cluster": {
    "id": 2,
    "label": "Analytical Achiever",
    "description": "High cognitive ability with strong conscientiousness..."
  },
  "feature_importance": {
    "cognitive_score": 0.31,
    "conscientiousness": 0.24,
    "openness": 0.18,
    "agreeableness": 0.14,
    "extraversion": 0.08,
    "neuroticism": 0.05
  },
  "shap_values": {
    "cognitive_score": 0.42,
    "conscientiousness": 0.38,
    "openness": -0.12,
    "agreeableness": 0.22,
    "extraversion": -0.05,
    "neuroticism": 0.08
  },
  "confidence": 0.87
}
```

---

## Machine Learning Models

### 1. K-Means Clustering

**Purpose:** Assign users to psychometric archetypes

**Features used:**
- `cognitive_score` (normalized 0-100)
- `openness`, `conscientiousness`, `extraversion`, `agreeableness`, `neuroticism` (normalized 0-100)

**Clusters (k=6):**

| Cluster | Label | Characteristics |
|---|---|---|
| 0 | Creative Visionary | High Openness, moderate Conscientiousness |
| 1 | Steady Pragmatist | High Conscientiousness, moderate Cognitive |
| 2 | Analytical Achiever | High Cognitive, high Conscientiousness |
| 3 | Social Connector | High Extraversion, high Agreeableness |
| 4 | Resilient Thinker | Low Neuroticism, high Cognitive |
| 5 | Adaptive Explorer | Balanced profile, high Openness |

### 2. Random Forest Classifier

**Purpose:** Predict cluster assignment and compute feature importance

**Parameters:**
- `n_estimators=200`
- `max_depth=12`
- `random_state=42`

**Feature importance:** Computed via mean decrease in impurity (MDI) across all trees.

### 3. TreeSHAP (Shapley Additive Explanations)

**Purpose:** Compute per-sample, per-feature contribution to the prediction

TreeSHAP uses the exact Shapley value algorithm optimized for tree-based models:
- Positive SHAP values: Feature pushes toward this cluster assignment
- Negative SHAP values: Feature pushes away from this cluster assignment
- Magnitude: How strongly the feature influenced the prediction

**Output used in UI:** "Key factors in your result: High Conscientiousness (↑) is a primary driver of your Analytical Achiever profile."

---

## Model Training & Registry

### Admin Model Management

The Laravel admin panel (`/admin/models`) shows all registered ML models:
- **Active model**: Used for inference in production
- **Inactive models**: Available for rollback

### Retraining

```bash
# Trigger retraining via API (authenticated)
curl -X POST http://127.0.0.1:8001/train \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"min_samples": 50}'
```

Or from the admin panel → Models → "Retrain" button (triggers a queue job).

---

## Privacy Model

```
Laravel App                        FastAPI ML Service
─────────────                      ─────────────────
user_id: 42              ──────►   anon_id: "ANON-75B439DE599B"
full_name: "Ahmad"         NOT     (random, regenerated each request)
scores: {cog: 72.5, ...} ──────►   scores: {cog: 72.5, ...}
```

**Guarantees:**
1. `anon_id` is generated fresh per request — not stored or linked to user ID
2. FastAPI has no database access — cannot join on any user table
3. Feature vectors contain no PII — only numerical assessment scores
4. Even if the ML service is compromised, user identities cannot be inferred

---

## Error Handling

| HTTP Status | Meaning |
|---|---|
| `200 OK` | Successful analysis |
| `401 Unauthorized` | Invalid or missing Bearer token |
| `422 Unprocessable Entity` | Invalid feature vector |
| `503 Service Unavailable` | Model not loaded yet |

The Laravel `MachineLearningService` handles errors gracefully — if the ML service is unreachable, assessment results are saved without ML insights (users see "ML analysis pending").
