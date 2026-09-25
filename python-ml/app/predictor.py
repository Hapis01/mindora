import json
import logging
from typing import Any, Dict, List, Optional

import joblib
import numpy as np
import shap

from app.config import (
    CLUSTER_LABELS,
    FEATURE_NAMES,
    KMEANS_MODEL_PATH,
    METADATA_PATH,
    RF_MODEL_PATH,
    SCALER_PATH,
)
from app.models import (
    ClusterResult,
    ExplainabilityResult,
    FeatureImpact,
    FeatureVector,
    FullInferenceResponse,
    PredictionResult,
)

logger = logging.getLogger("psychoai.predictor")

FEATURE_DISPLAY_NAMES_ID = {
    "logical": "Penalaran Logis (Logical Reasoning)",
    "numerical": "Penalaran Numerik (Numerical Reasoning)",
    "verbal": "Penalaran Verbal (Verbal Reasoning)",
    "openness": "Openness (Keterbukaan Ide & Eksplorasi)",
    "conscientiousness": "Conscientiousness (Ketelitian & Disiplin)",
    "extraversion": "Extraversion (Interaksi & Kolaborasi)",
    "agreeableness": "Agreeableness (Empati & Kerja Sama)",
    "neuroticism": "Sensitivitas Responsif / Stabilitas Emosi",
    "consistency": "Konsistensi Respons Jawaban",
    "avg_response_time_ms": "Kecepatan Respons Rata-rata"
}


class PsychometricPredictor:
    def __init__(self):
        self.scaler = None
        self.kmeans = None
        self.rf = None
        self.explainer = None
        self.metadata = {}
        self.model_version = "v1.0.0"
        self.is_ready = False
        self._load_artifacts()

    def _load_artifacts(self):
        try:
            if KMEANS_MODEL_PATH.exists() and RF_MODEL_PATH.exists() and SCALER_PATH.exists():
                logger.info("Loading ML models from artifacts...")
                self.scaler = joblib.load(SCALER_PATH)
                self.kmeans = joblib.load(KMEANS_MODEL_PATH)
                self.rf = joblib.load(RF_MODEL_PATH)

                if METADATA_PATH.exists():
                    with open(METADATA_PATH, "r", encoding="utf-8") as f:
                        self.metadata = json.load(f)
                        self.model_version = self.metadata.get("version", "v1.0.0")

                # Initialize SHAP TreeExplainer on Random Forest
                logger.info("Initializing SHAP TreeExplainer...")
                self.explainer = shap.TreeExplainer(self.rf)
                self.is_ready = True
                logger.info(f"ML Models loaded successfully (Version {self.model_version}).")
            else:
                logger.warning("ML Artifacts not found. Run training script first.")
        except Exception as e:
            logger.error(f"Failed to load ML artifacts: {e}", exc_info=True)
            self.is_ready = False

    def cluster(self, features: FeatureVector) -> ClusterResult:
        raw_values = np.array([features.to_array()])
        scaled_values = self.scaler.transform(raw_values)

        cluster_id = int(self.kmeans.predict(scaled_values)[0])
        cluster_info = CLUSTER_LABELS.get(cluster_id, {
            "title": f"Cluster {cluster_id}",
            "description": "Profil pengelompokan pola kognitif dan kepribadian.",
        })

        # Calculate Euclidean distance to cluster centroid
        center = self.kmeans.cluster_centers_[cluster_id]
        distance = float(np.linalg.norm(scaled_values[0] - center))

        return ClusterResult(
            cluster_id=cluster_id,
            cluster_label=cluster_info["title"],
            description=cluster_info["description"],
            distance_to_center=round(distance, 4),
            model_version=self.model_version,
        )

    def classify(self, features: FeatureVector) -> PredictionResult:
        raw_values = np.array([features.to_array()])
        scaled_values = self.scaler.transform(raw_values)

        predicted_category = str(self.rf.predict(scaled_values)[0])
        probabilities = self.rf.predict_proba(scaled_values)[0]
        classes = self.rf.classes_

        prob_dict = {str(cls): round(float(prob), 4) for cls, prob in zip(classes, probabilities)}
        confidence = round(float(max(probabilities)) * 100, 2)

        return PredictionResult(
            predicted_category=predicted_category,
            confidence=confidence,
            probabilities=prob_dict,
            model_version=self.model_version,
        )

    def explain(self, features: FeatureVector, predicted_category: Optional[str] = None) -> ExplainabilityResult:
        raw_values = np.array([features.to_array()])
        scaled_values = self.scaler.transform(raw_values)

        if predicted_category is None:
            predicted_category = str(self.rf.predict(scaled_values)[0])

        method = "TreeSHAP"
        base_value = None
        top_impacts: List[FeatureImpact] = []

        try:
            # TreeExplainer inference
            shap_values = self.explainer.shap_values(scaled_values)
            class_index = list(self.rf.classes_).index(predicted_category)

            # shap_values format can be list of arrays [n_classes, (n_samples, n_features)]
            # or 3D array (n_samples, n_features, n_classes)
            if isinstance(shap_values, list):
                class_shap = shap_values[class_index][0]
            elif isinstance(shap_values, np.ndarray) and len(shap_values.shape) == 3:
                class_shap = shap_values[0, :, class_index]
            else:
                class_shap = shap_values[0]

            if hasattr(self.explainer, "expected_value"):
                ev = self.explainer.expected_value
                if isinstance(ev, (list, np.ndarray)):
                    base_value = float(ev[class_index])
                else:
                    base_value = float(ev)

            feature_raw_dict = dict(zip(FEATURE_NAMES, features.to_array()))

            # Pair features with SHAP impacts
            impacts = []
            for i, feat_name in enumerate(FEATURE_NAMES):
                impact = float(class_shap[i])
                raw_val = float(feature_raw_dict[feat_name])
                direction = "positive" if impact > 0 else ("negative" if impact < 0 else "neutral")
                impacts.append(
                    FeatureImpact(
                        feature=feat_name,
                        feature_name_id=FEATURE_DISPLAY_NAMES_ID.get(feat_name, feat_name),
                        raw_value=round(raw_val, 2),
                        impact_score=round(impact, 4),
                        direction=direction,
                    )
                )

            # Sort by absolute SHAP impact magnitude descending
            impacts.sort(key=lambda x: abs(x.impact_score), reverse=True)
            top_impacts = impacts[:5]

        except Exception as e:
            logger.warning(f"SHAP explanation fallback triggered: {e}")
            method = "RandomForestFeatureImportance"
            feature_raw_dict = dict(zip(FEATURE_NAMES, features.to_array()))
            importances = dict(zip(FEATURE_NAMES, self.rf.feature_importances_))
            sorted_feats = sorted(importances.items(), key=lambda x: x[1], reverse=True)[:5]

            for feat_name, imp in sorted_feats:
                top_impacts.append(
                    FeatureImpact(
                        feature=feat_name,
                        feature_name_id=FEATURE_DISPLAY_NAMES_ID.get(feat_name, feat_name),
                        raw_value=round(float(feature_raw_dict[feat_name]), 2),
                        impact_score=round(float(imp), 4),
                        direction="positive",
                    )
                )

        return ExplainabilityResult(
            predicted_category=predicted_category,
            top_features=top_impacts,
            method=method,
            base_value=base_value,
        )

    def full_inference(self, anonymous_user_id: str, features: FeatureVector) -> FullInferenceResponse:
        cluster_res = self.cluster(features)
        pred_res = self.classify(features)
        explain_res = self.explain(features, pred_res.predicted_category)

        return FullInferenceResponse(
            anonymous_user_id=anonymous_user_id,
            cluster=cluster_res,
            prediction=pred_res,
            explainability=explain_res,
        )


# Singleton Predictor Instance
predictor = PsychometricPredictor()
