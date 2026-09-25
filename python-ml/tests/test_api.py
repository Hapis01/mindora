import sys
from pathlib import Path

sys.path.append(str(Path(__file__).resolve().parent.parent))

from fastapi.testclient import TestClient
from app.main import app
from app.config import API_AUTH_TOKEN

client = TestClient(app)

def test_health():
    res = client.get("/health")
    assert res.status_code == 200
    data = res.json()
    assert data["status"] == "healthy"
    assert data["models_loaded"] is True
    assert data["shap_available"] is True
    print("[OK] GET /health PASSED")

def test_auth_failure():
    payload = {
        "anonymous_user_id": "ANON-TEST123",
        "features": {
            "logical": 80.0,
            "numerical": 85.0,
            "verbal": 70.0,
            "openness": 60.0,
            "conscientiousness": 75.0,
            "extraversion": 50.0,
            "agreeableness": 65.0,
            "neuroticism": 40.0,
            "consistency": 95.0,
            "avg_response_time_ms": 2500.0
        }
    }
    # No token
    res = client.post("/cluster", json=payload)
    assert res.status_code == 401
    
    # Invalid token
    res = client.post("/cluster", json=payload, headers={"Authorization": "Bearer wrong-token"})
    assert res.status_code == 403
    print("[OK] Auth rejection tests PASSED")

def test_cluster():
    payload = {
        "anonymous_user_id": "ANON-TEST123",
        "features": {
            "logical": 85.0,
            "numerical": 90.0,
            "verbal": 65.0,
            "openness": 55.0,
            "conscientiousness": 85.0,
            "extraversion": 45.0,
            "agreeableness": 60.0,
            "neuroticism": 35.0,
            "consistency": 95.0,
            "avg_response_time_ms": 3000.0
        }
    }
    res = client.post("/cluster", json=payload, headers={"Authorization": f"Bearer {API_AUTH_TOKEN}"})
    assert res.status_code == 200
    data = res.json()
    assert "cluster_id" in data
    assert "cluster_label" in data
    assert "distance_to_center" in data
    print(f"[OK] POST /cluster PASSED -> Cluster: {data['cluster_label']} (id={data['cluster_id']})")

def test_predict():
    payload = {
        "anonymous_user_id": "ANON-TEST123",
        "features": {
            "logical": 85.0,
            "numerical": 90.0,
            "verbal": 65.0,
            "openness": 55.0,
            "conscientiousness": 85.0,
            "extraversion": 45.0,
            "agreeableness": 60.0,
            "neuroticism": 35.0,
            "consistency": 95.0,
            "avg_response_time_ms": 3000.0
        }
    }
    res = client.post("/predict", json=payload, headers={"Authorization": f"Bearer {API_AUTH_TOKEN}"})
    assert res.status_code == 200
    data = res.json()
    assert "predicted_category" in data
    assert "confidence" in data
    print(f"[OK] POST /predict PASSED -> Category: {data['predicted_category']} ({data['confidence']}%)")

def test_explain():
    payload = {
        "anonymous_user_id": "ANON-TEST123",
        "features": {
            "logical": 85.0,
            "numerical": 90.0,
            "verbal": 65.0,
            "openness": 55.0,
            "conscientiousness": 85.0,
            "extraversion": 45.0,
            "agreeableness": 60.0,
            "neuroticism": 35.0,
            "consistency": 95.0,
            "avg_response_time_ms": 3000.0
        }
    }
    res = client.post("/explain", json=payload, headers={"Authorization": f"Bearer {API_AUTH_TOKEN}"})
    assert res.status_code == 200
    data = res.json()
    assert len(data["top_features"]) > 0
    print(f"[OK] POST /explain (SHAP) PASSED -> Top feature: {data['top_features'][0]['feature']} (impact={data['top_features'][0]['impact_score']})")

def test_full_analysis():
    payload = {
        "anonymous_user_id": "ANON-FULL-TEST",
        "features": {
            "logical": 65.0,
            "numerical": 60.0,
            "verbal": 88.0,
            "openness": 75.0,
            "conscientiousness": 65.0,
            "extraversion": 85.0,
            "agreeableness": 88.0,
            "neuroticism": 35.0,
            "consistency": 92.0,
            "avg_response_time_ms": 2100.0
        }
    }
    res = client.post("/analyze", json=payload, headers={"Authorization": f"Bearer {API_AUTH_TOKEN}"})
    assert res.status_code == 200
    data = res.json()
    assert data["status"] == "success"
    assert "cluster" in data
    assert "prediction" in data
    assert "explainability" in data
    print(f"[OK] POST /analyze PASSED -> Cluster: {data['cluster']['cluster_label']}, Category: {data['prediction']['predicted_category']}")

if __name__ == "__main__":
    test_health()
    test_auth_failure()
    test_cluster()
    test_predict()
    test_explain()
    test_full_analysis()
    print("\nALL PYTHON ML MICROSERVICE TESTS COMPLETED SUCCESSFULLY!")
