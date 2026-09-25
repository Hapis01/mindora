from typing import Dict, List, Optional
from pydantic import BaseModel, Field


class FeatureVector(BaseModel):
    logical: float = Field(default=50.0, ge=0.0, le=100.0, description="Logical reasoning score (0-100)")
    numerical: float = Field(default=50.0, ge=0.0, le=100.0, description="Numerical reasoning score (0-100)")
    verbal: float = Field(default=50.0, ge=0.0, le=100.0, description="Verbal reasoning score (0-100)")
    openness: float = Field(default=50.0, ge=0.0, le=100.0, description="Openness to experience (0-100)")
    conscientiousness: float = Field(default=50.0, ge=0.0, le=100.0, description="Conscientiousness (0-100)")
    extraversion: float = Field(default=50.0, ge=0.0, le=100.0, description="Extraversion (0-100)")
    agreeableness: float = Field(default=50.0, ge=0.0, le=100.0, description="Agreeableness (0-100)")
    neuroticism: float = Field(default=50.0, ge=0.0, le=100.0, description="Neuroticism (0-100)")
    consistency: float = Field(default=95.0, ge=0.0, le=100.0, description="Internal response consistency (0-100)")
    avg_response_time_ms: float = Field(default=2500.0, ge=0.0, description="Average response latency in milliseconds")

    def to_array(self) -> List[float]:
        return [
            self.logical,
            self.numerical,
            self.verbal,
            self.openness,
            self.conscientiousness,
            self.extraversion,
            self.agreeableness,
            self.neuroticism,
            self.consistency,
            self.avg_response_time_ms,
        ]


class InferenceRequest(BaseModel):
    anonymous_user_id: str = Field(..., description="Pseudo-anonymous identifier (e.g. ANON-A7B8C9), strictly zero PII")
    features: FeatureVector


class ClusterResult(BaseModel):
    cluster_id: int
    cluster_label: str
    description: str
    distance_to_center: float
    model_version: str


class PredictionResult(BaseModel):
    predicted_category: str
    confidence: float
    probabilities: Dict[str, float]
    model_version: str


class FeatureImpact(BaseModel):
    feature: str
    feature_name_id: str
    raw_value: float
    impact_score: float
    direction: str  # positive, negative, neutral


class ExplainabilityResult(BaseModel):
    predicted_category: str
    top_features: List[FeatureImpact]
    method: str
    base_value: Optional[float] = None


class FullInferenceResponse(BaseModel):
    status: str = "success"
    anonymous_user_id: str
    cluster: ClusterResult
    prediction: PredictionResult
    explainability: ExplainabilityResult
    disclaimer: str = (
        "Hasil analisis Machine Learning ini merupakan model eksploratif/demonstrasi untuk "
        "pengayaan wawasan psikometri mandiri, bukan diagnosis klinis atau evaluasi deterministik."
    )


class HealthResponse(BaseModel):
    status: str
    service: str
    version: str
    models_loaded: bool
    clusterer: str
    classifier: str
    shap_available: bool
    environment_notice: str = "Development / Demonstration Model"
