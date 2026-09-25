import numpy as np
import pandas as pd


def generate_synthetic_normative_dataset(n_samples: int = 1500, random_state: int = 42) -> pd.DataFrame:
    """
    Generate synthetic normative dataset for training psychometric demonstration models.
    Marked explicitly as: DEVELOPMENT / DEMONSTRATION MODEL (Synthetic Normative Sample).
    
    Generates 4 distinct archetype clusters with realistic noise and covariance:
    1. Analytical & Structured (High logical/numerical, high conscientiousness)
    2. Creative & Explorative (High openness, balanced verbal/logical)
    3. Social & Collaborative (High extraversion, high agreeableness, high verbal)
    4. Independent & Task-Oriented (High conscientiousness, moderate-low extraversion, high consistency)
    """
    np.random.seed(random_state)
    n_per_cluster = n_samples // 4

    # Cluster 1: Analytical & Structured
    c1 = {
        "logical": np.clip(np.random.normal(78, 9, n_per_cluster), 40, 100),
        "numerical": np.clip(np.random.normal(82, 8, n_per_cluster), 45, 100),
        "verbal": np.clip(np.random.normal(68, 11, n_per_cluster), 35, 95),
        "openness": np.clip(np.random.normal(60, 12, n_per_cluster), 25, 90),
        "conscientiousness": np.clip(np.random.normal(80, 8, n_per_cluster), 50, 100),
        "extraversion": np.clip(np.random.normal(48, 12, n_per_cluster), 20, 80),
        "agreeableness": np.clip(np.random.normal(65, 10, n_per_cluster), 35, 90),
        "neuroticism": np.clip(np.random.normal(40, 10, n_per_cluster), 15, 75),
        "consistency": np.clip(np.random.normal(92, 4, n_per_cluster), 75, 100),
        "avg_response_time_ms": np.clip(np.random.normal(3200, 500, n_per_cluster), 1500, 6000),
        "target_category": "Systematic Executor",
        "cluster_id": 0
    }

    # Cluster 2: Creative & Explorative
    c2 = {
        "logical": np.clip(np.random.normal(68, 10, n_per_cluster), 35, 95),
        "numerical": np.clip(np.random.normal(58, 12, n_per_cluster), 25, 88),
        "verbal": np.clip(np.random.normal(76, 9, n_per_cluster), 45, 98),
        "openness": np.clip(np.random.normal(85, 7, n_per_cluster), 65, 100),
        "conscientiousness": np.clip(np.random.normal(55, 11, n_per_cluster), 30, 85),
        "extraversion": np.clip(np.random.normal(68, 10, n_per_cluster), 40, 95),
        "agreeableness": np.clip(np.random.normal(70, 9, n_per_cluster), 45, 95),
        "neuroticism": np.clip(np.random.normal(50, 12, n_per_cluster), 20, 85),
        "consistency": np.clip(np.random.normal(88, 5, n_per_cluster), 70, 100),
        "avg_response_time_ms": np.clip(np.random.normal(2400, 450, n_per_cluster), 1200, 5000),
        "target_category": "Strategic Innovator",
        "cluster_id": 1
    }

    # Cluster 3: Social & Collaborative
    c3 = {
        "logical": np.clip(np.random.normal(62, 11, n_per_cluster), 30, 90),
        "numerical": np.clip(np.random.normal(54, 12, n_per_cluster), 25, 85),
        "verbal": np.clip(np.random.normal(82, 7, n_per_cluster), 55, 100),
        "openness": np.clip(np.random.normal(70, 10, n_per_cluster), 45, 95),
        "conscientiousness": np.clip(np.random.normal(66, 9, n_per_cluster), 40, 90),
        "extraversion": np.clip(np.random.normal(84, 7, n_per_cluster), 60, 100),
        "agreeableness": np.clip(np.random.normal(86, 6, n_per_cluster), 65, 100),
        "neuroticism": np.clip(np.random.normal(38, 9, n_per_cluster), 15, 70),
        "consistency": np.clip(np.random.normal(90, 4, n_per_cluster), 72, 100),
        "avg_response_time_ms": np.clip(np.random.normal(2100, 400, n_per_cluster), 1000, 4500),
        "target_category": "Collaborative Facilitator",
        "cluster_id": 2
    }

    # Cluster 4: Independent & Task-Oriented
    c4 = {
        "logical": np.clip(np.random.normal(72, 9, n_per_cluster), 40, 95),
        "numerical": np.clip(np.random.normal(70, 10, n_per_cluster), 38, 95),
        "verbal": np.clip(np.random.normal(60, 11, n_per_cluster), 30, 88),
        "openness": np.clip(np.random.normal(52, 11, n_per_cluster), 25, 80),
        "conscientiousness": np.clip(np.random.normal(76, 8, n_per_cluster), 45, 98),
        "extraversion": np.clip(np.random.normal(40, 10, n_per_cluster), 15, 70),
        "agreeableness": np.clip(np.random.normal(58, 10, n_per_cluster), 30, 85),
        "neuroticism": np.clip(np.random.normal(35, 9, n_per_cluster), 10, 65),
        "consistency": np.clip(np.random.normal(95, 3, n_per_cluster), 80, 100),
        "avg_response_time_ms": np.clip(np.random.normal(2800, 500, n_per_cluster), 1400, 5500),
        "target_category": "Pragmatic Problem-Solver",
        "cluster_id": 3
    }

    df1 = pd.DataFrame(c1)
    df2 = pd.DataFrame(c2)
    df3 = pd.DataFrame(c3)
    df4 = pd.DataFrame(c4)

    df = pd.concat([df1, df2, df3, df4], ignore_index=True)
    df = df.sample(frac=1.0, random_state=random_state).reset_index(drop=True)
    return df
