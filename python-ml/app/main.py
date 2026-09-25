import logging
from typing import Optional

from fastapi import Depends, FastAPI, HTTPException, Security, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPAuthorizationCredentials, HTTPBearer

from app.config import API_AUTH_TOKEN
from app.models import (
    ClusterResult,
    ExplainabilityResult,
    FullInferenceResponse,
    HealthResponse,
    InferenceRequest,
    PredictionResult,
)
from app.predictor import predictor

# Configure Logging
logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(name)s: %(message)s")
logger = logging.getLogger("psychoai.ml_service")

# Security Scheme
security = HTTPBearer(auto_error=False)


def verify_token(credentials: Optional[HTTPAuthorizationCredentials] = Security(security)):
    if not credentials or credentials.scheme.lower() != "bearer":
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Missing or invalid authentication scheme. Bearer token required.",
            headers={"WWW-Authenticate": "Bearer"},
        )
    if credentials.credentials != API_AUTH_TOKEN:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Invalid ML service authentication token.",
        )
    return credentials.credentials


app = FastAPI(
    title="Mindora ML Microservice",
    description="Dedicated Machine Learning Service providing psychometric clustering, profile classification, and SHAP explainability.",
    version="1.0.0",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/health", response_model=HealthResponse, tags=["System"])
def health_check():
    """Health check endpoint exposing model readiness and version metadata."""
    return HealthResponse(
        status="healthy" if predictor.is_ready else "degraded",
        service="Mindora ML Microservice",
        version=predictor.model_version,
        models_loaded=predictor.is_ready,
        clusterer="K-Means (k=4)",
        classifier="Random Forest Classifier (n=120)",
        shap_available=predictor.explainer is not None,
        environment_notice="DEVELOPMENT / DEMONSTRATION MODEL (Trained on Synthetic Normative Sample v1.0)",
    )


@app.post("/cluster", response_model=ClusterResult, tags=["Inference"])
def cluster_profile(request: InferenceRequest, _: str = Depends(verify_token)):
    """Assign an anonymized feature vector to a psychometric behavioral cluster."""
    if not predictor.is_ready:
        raise HTTPException(status_code=503, detail="ML models are not loaded.")
    logger.info(f"Clustering request for anonymous user: {request.anonymous_user_id}")
    return predictor.cluster(request.features)


@app.post("/predict", response_model=PredictionResult, tags=["Inference"])
def predict_category(request: InferenceRequest, _: str = Depends(verify_token)):
    """Classify psychometric profile into category with confidence probability."""
    if not predictor.is_ready:
        raise HTTPException(status_code=503, detail="ML models are not loaded.")
    logger.info(f"Classification request for anonymous user: {request.anonymous_user_id}")
    return predictor.classify(request.features)


@app.post("/explain", response_model=ExplainabilityResult, tags=["Inference"])
def explain_profile(request: InferenceRequest, _: str = Depends(verify_token)):
    """Compute TreeSHAP feature contributions and explainability for profile classification."""
    if not predictor.is_ready:
        raise HTTPException(status_code=503, detail="ML models are not loaded.")
    logger.info(f"Explainability request for anonymous user: {request.anonymous_user_id}")
    return predictor.explain(request.features)


@app.post("/analyze", response_model=FullInferenceResponse, tags=["Inference"])
def analyze_full_profile(request: InferenceRequest, _: str = Depends(verify_token)):
    """
    Perform complete psychometric ML analysis:
    - K-Means Behavioral Clustering
    - Random Forest Classification & Confidence
    - SHAP Explainability & Top Contributing Features
    """
    if not predictor.is_ready:
        raise HTTPException(status_code=503, detail="ML models are not loaded.")
    logger.info(f"Full analysis request for anonymous user: {request.anonymous_user_id}")
    return predictor.full_inference(request.anonymous_user_id, request.features)
