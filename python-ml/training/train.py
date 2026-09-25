import json
import sys
from datetime import datetime
from pathlib import Path

# Add parent directory to sys.path
sys.path.append(str(Path(__file__).resolve().parent.parent))

import joblib
import numpy as np
from sklearn.cluster import KMeans
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, classification_report, silhouette_score
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler

from app.config import ARTIFACTS_DIR, FEATURE_NAMES, KMEANS_MODEL_PATH, METADATA_PATH, RF_MODEL_PATH, SCALER_PATH
from training.dataset import generate_synthetic_normative_dataset


def train_models():
    print("=" * 60)
    print("PSYCHOAI MACHINE LEARNING MODEL TRAINING PIPELINE")
    print("Notice: DEVELOPMENT / DEMONSTRATION MODEL (Synthetic Normative Sample)")
    print("=" * 60)

    # 1. Generate Dataset
    print("\n1. Generating synthetic normative psychometric dataset...")
    df = generate_synthetic_normative_dataset(n_samples=2000, random_state=42)
    print(f"   Generated {len(df)} sample profiles across 4 archetype clusters.")

    X = df[FEATURE_NAMES].values
    y_category = df["target_category"].values

    # 2. Fit Scaler
    print("\n2. Fitting StandardScaler on psychometric feature vector...")
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)

    # 3. Train K-Means Clustering
    print("\n3. Training K-Means Clusterer (k=4)...")
    kmeans = KMeans(n_clusters=4, random_state=42, n_init=15)
    kmeans.fit(X_scaled)
    cluster_labels = kmeans.labels_

    sil_score = silhouette_score(X_scaled, cluster_labels)
    print(f"   K-Means Silhouette Score: {sil_score:.4f}")
    print(f"   K-Means Inertia: {kmeans.inertia_:.2f}")

    # 4. Train Random Forest Classifier
    print("\n4. Training Random Forest Classifier for Profile Categories...")
    X_train, X_test, y_train, y_test = train_test_split(
        X_scaled, y_category, test_size=0.2, random_state=42, stratify=y_category
    )

    rf = RandomForestClassifier(
        n_estimators=120,
        max_depth=7,
        min_samples_split=4,
        random_state=42,
        class_weight="balanced"
    )
    rf.fit(X_train, y_train)

    y_pred = rf.predict(X_test)
    acc = accuracy_score(y_test, y_pred)
    print(f"   Random Forest Accuracy: {acc * 100:.2f}%")
    print("\nClassification Report:")
    print(classification_report(y_test, y_pred))

    # 5. Feature Importances
    importances = dict(zip(FEATURE_NAMES, rf.feature_importances_))
    sorted_importances = sorted(importances.items(), key=lambda x: x[1], reverse=True)
    print("Top Feature Importances in Random Forest:")
    for feat, imp in sorted_importances[:5]:
        print(f"   - {feat}: {imp * 100:.2f}%")

    # 6. Save Model Artifacts
    print(f"\n6. Serializing model artifacts to {ARTIFACTS_DIR}...")
    ARTIFACTS_DIR.mkdir(parents=True, exist_ok=True)

    joblib.dump(kmeans, KMEANS_MODEL_PATH)
    joblib.dump(rf, RF_MODEL_PATH)
    joblib.dump(scaler, SCALER_PATH)

    metadata = {
        "version": "v1.0.0",
        "training_date": datetime.now().isoformat(),
        "algorithm_clusterer": "K-Means (k=4)",
        "algorithm_classifier": "Random Forest Classifier (n=120)",
        "dataset_version": "normative-synthetic-v1.0",
        "samples_count": len(df),
        "features": FEATURE_NAMES,
        "metrics": {
            "kmeans_silhouette_score": round(float(sil_score), 4),
            "kmeans_inertia": round(float(kmeans.inertia_), 2),
            "rf_accuracy": round(float(acc), 4),
            "top_features": sorted_importances[:5]
        },
        "status": "active",
        "disclaimer": "DEVELOPMENT / DEMONSTRATION MODEL: Trained on synthetic normative psychometric data for demonstration purposes."
    }

    with open(METADATA_PATH, "w", encoding="utf-8") as f:
        json.dump(metadata, f, indent=2)

    print("Metadata written successfully to metadata.json.")
    print("=" * 60)
    print("TRAINING PIPELINE COMPLETED SUCCESSFULLY!")
    print("=" * 60)
    return metadata


if __name__ == "__main__":
    train_models()
