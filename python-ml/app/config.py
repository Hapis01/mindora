import os
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent.parent
ARTIFACTS_DIR = BASE_DIR / "artifacts"
ARTIFACTS_DIR.mkdir(parents=True, exist_ok=True)

# Authentication token matching Laravel config
API_AUTH_TOKEN = os.getenv("ML_SERVICE_TOKEN", "Mindora_ml_secure_token_secret_2026")

# Artifact Paths
KMEANS_MODEL_PATH = ARTIFACTS_DIR / "kmeans_clusterer.joblib"
RF_MODEL_PATH = ARTIFACTS_DIR / "rf_classifier.joblib"
SCALER_PATH = ARTIFACTS_DIR / "scaler.joblib"
METADATA_PATH = ARTIFACTS_DIR / "metadata.json"

FEATURE_NAMES = [
    "logical",
    "numerical",
    "verbal",
    "openness",
    "conscientiousness",
    "extraversion",
    "agreeableness",
    "neuroticism",
    "consistency",
    "avg_response_time_ms"
]

CLUSTER_LABELS = {
    0: {
        "title": "Analytical & Structured",
        "description": "Profil berorientasi pada pemikiran metodis, ketepatan data kuantitatif, dan eksekusi tugas yang sistematis.",
        "badge_color": "blue"
    },
    1: {
        "title": "Creative & Explorative",
        "description": "Profil dengan keterbukaan ide tinggi, antusiasme inovasi konseptual, dan fleksibilitas beradaptasi.",
        "badge_color": "purple"
    },
    2: {
        "title": "Social & Collaborative",
        "description": "Profil fasilitator interpersonal, berorientasi menjaga keharmonisan tim, dan komunikatif.",
        "badge_color": "emerald"
    },
    3: {
        "title": "Independent & Task-Oriented",
        "description": "Profil fokus mandiri yang tekun, konsisten, dan memiliki daya tahan kerja individual yang solid.",
        "badge_color": "amber"
    }
}
