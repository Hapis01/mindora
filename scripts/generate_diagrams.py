import os
import sys
from pathlib import Path
import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Base output paths
DOCS_DIR = Path(r"c:\xampp\htdocs\psikotes online\docs")
DOCS_DIR.mkdir(parents=True, exist_ok=True)
IMG_DIR = DOCS_DIR / "diagrams"
IMG_DIR.mkdir(parents=True, exist_ok=True)

# -------------------------------------------------------------
# 1. GENERATE DIAGRAM 1: ARSITEKTUR SISTEM END-TO-END
# -------------------------------------------------------------
def generate_diagram_1():
    fig, ax = plt.subplots(figsize=(10, 5), dpi=300)
    ax.set_facecolor("#f8fafc")
    fig.patch.set_facecolor("#ffffff")
    
    # Title
    plt.title("Arsitektur Sistem & Alur Data End-to-End Mindora", fontsize=13, fontweight="bold", pad=15, color="#0f172a")
    
    # Boxes
    boxes = [
        {"xy": (0.05, 0.35), "w": 0.18, "h": 0.45, "title": "Web Client\n(Browser)", "sub": "Tailwind / Blade\nChart.js Radar & Bar\nCSRF Protected", "color": "#e0f2fe", "edge": "#0284c7"},
        {"xy": (0.29, 0.20), "w": 0.22, "h": 0.70, "title": "Core Application\n(Laravel 11 / PHP 8.2)", "sub": "• Psychometric Engine\n• RBAC & Security\n• AES-256-GCM\n• Argon2id Hashing\n• Context Orchestrator", "color": "#ede9fe", "edge": "#7c3aed"},
        {"xy": (0.57, 0.50), "w": 0.20, "h": 0.40, "title": "ML Microservice\n(FastAPI / Python)", "sub": "• StandardScaler\n• K-Means (k=4)\n• Random Forest (120)\n• TreeSHAP (XAI)", "color": "#dcfce7", "edge": "#16a34a"},
        {"xy": (0.57, 0.08), "w": 0.20, "h": 0.35, "title": "Database Server\n(MySQL Relational)", "sub": "• Encrypted PII (GCM)\n• Normative Scores\n• ML Predictions\n• Audit Trails", "color": "#fef3c7", "edge": "#d97706"},
        {"xy": (0.83, 0.45), "w": 0.14, "h": 0.45, "title": "Generative AI\n(Google Gemini)", "sub": "gemini-3.6-flash\n4096 Tokens\nPersonal Insight\nChat Engine", "color": "#fae8ff", "edge": "#c026d3"},
    ]
    
    for b in boxes:
        rect = patches.FancyBboxPatch(b["xy"], b["w"], b["h"], boxstyle="round,pad=0.02,rounding_size=0.03",
                                      facecolor=b["color"], edgecolor=b["edge"], linewidth=1.8)
        ax.add_patch(rect)
        cx = b["xy"][0] + b["w"] / 2
        cy = b["xy"][1] + b["h"] - 0.10
        ax.text(cx, cy, b["title"], ha="center", va="center", fontsize=9, fontweight="bold", color="#0f172a")
        ax.text(cx, b["xy"][1] + (b["h"] - 0.10) / 2, b["sub"], ha="center", va="center", fontsize=7.5, color="#334155")
        
    # Arrows
    arrow_props = dict(arrowstyle="->", lw=1.6, color="#475569")
    # Client -> Core
    ax.annotate("", xy=(0.29, 0.57), xytext=(0.23, 0.57), arrowprops=arrow_props)
    ax.text(0.26, 0.60, "HTTPS", ha="center", fontsize=7, color="#0369a1", fontweight="bold")
    
    # Core -> ML Service
    ax.annotate("", xy=(0.57, 0.70), xytext=(0.51, 0.70), arrowprops=arrow_props)
    ax.text(0.54, 0.73, "10-D Vector", ha="center", fontsize=7, color="#15803d", fontweight="bold")
    
    # Core -> Database
    ax.annotate("", xy=(0.57, 0.25), xytext=(0.51, 0.35), arrowprops=arrow_props)
    ax.text(0.54, 0.31, "Ciphertext", ha="center", fontsize=7, color="#b45309", fontweight="bold")
    
    # Core -> Gemini
    ax.annotate("", xy=(0.83, 0.67), xytext=(0.51, 0.82), arrowprops=dict(arrowstyle="->", lw=1.6, color="#c026d3", linestyle="--"))
    ax.text(0.67, 0.85, "Context-Conditioned Prompt", ha="center", fontsize=7, color="#a21caf", fontweight="bold")
    
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    plt.tight_layout()
    p = IMG_DIR / "diagram_1_architecture.png"
    plt.savefig(p, dpi=300, bbox_inches="tight")
    plt.close()
    print(f"Generated {p}")

# -------------------------------------------------------------
# 2. GENERATE DIAGRAM 2: PIPELINE MACHINE LEARNING & XAI
# -------------------------------------------------------------
def generate_diagram_2():
    fig, ax = plt.subplots(figsize=(10, 4.8), dpi=300)
    ax.set_facecolor("#f8fafc")
    fig.patch.set_facecolor("#ffffff")
    
    plt.title("Pipeline Machine Learning, Clustering, Prediksi & Explainable AI (SHAP)", fontsize=13, fontweight="bold", pad=15, color="#0f172a")
    
    # Step 1: Input Vector
    rect1 = patches.FancyBboxPatch((0.03, 0.25), 0.20, 0.55, boxstyle="round,pad=0.02", facecolor="#eff6ff", edgecolor="#3b82f6", lw=1.5)
    ax.add_patch(rect1)
    ax.text(0.13, 0.72, "Vektor Fitur (10-D)\nZero-PII", ha="center", va="center", fontsize=9, fontweight="bold", color="#1e3a8a")
    desc1 = "• 3 Skor Kognitif (L,N,V)\n• 5 Dimensi Big Five\n• Konsistensi Respons\n• Waktu Respons (ms)\n[Zero Identitas Pribadi]"
    ax.text(0.13, 0.46, desc1, ha="center", va="center", fontsize=7.5, color="#1e293b")
    
    # Step 2: Normalization
    rect2 = patches.FancyBboxPatch((0.28, 0.38), 0.16, 0.32, boxstyle="round,pad=0.02", facecolor="#f1f5f9", edgecolor="#64748b", lw=1.5)
    ax.add_patch(rect2)
    ax.text(0.36, 0.62, "StandardScaler", ha="center", va="center", fontsize=9, fontweight="bold", color="#0f172a")
    ax.text(0.36, 0.48, "Z-Score Normalisasi\nz = (x - μ) / σ\n(Bebas Bias Skala)", ha="center", va="center", fontsize=7.5, color="#334155")
    
    # Step 3A: K-Means
    rect3a = patches.FancyBboxPatch((0.50, 0.60), 0.22, 0.32, boxstyle="round,pad=0.02", facecolor="#fef3c7", edgecolor="#d97706", lw=1.5)
    ax.add_patch(rect3a)
    ax.text(0.61, 0.85, "K-Means (k=4)", ha="center", va="center", fontsize=9, fontweight="bold", color="#78350f")
    ax.text(0.61, 0.72, "Unsupervised Learning\n• Jarak Euclidean Centroid\n• Klaster Karakter Alami\nSilhouette: 0.1696", ha="center", va="center", fontsize=7.5, color="#451a03")
    
    # Step 3B: Random Forest
    rect3b = patches.FancyBboxPatch((0.50, 0.12), 0.22, 0.38, boxstyle="round,pad=0.02", facecolor="#dcfce7", edgecolor="#16a34a", lw=1.5)
    ax.add_patch(rect3b)
    ax.text(0.61, 0.42, "Random Forest (n=120)", ha="center", va="center", fontsize=9, fontweight="bold", color="#14532d")
    ax.text(0.61, 0.26, "Supervised Ensemble\n• 120 Decision Trees\n• Majority Voting Arketipe\n• Confidence Score (%)\nAkurasi: 91.75%", ha="center", va="center", fontsize=7.5, color="#166534")
    
    # Step 4: TreeSHAP
    rect4 = patches.FancyBboxPatch((0.78, 0.25), 0.19, 0.55, boxstyle="round,pad=0.02", facecolor="#fae8ff", edgecolor="#a855f7", lw=1.5)
    ax.add_patch(rect4)
    ax.text(0.875, 0.72, "TreeSHAP (XAI)\nExplainable AI", ha="center", va="center", fontsize=9, fontweight="bold", color="#581c87")
    ax.text(0.875, 0.46, "Teori Permainan Koperasi\n• Nilai Shapley per fitur\n• Kontribusi Marjinal (±%)\n• Transparansi Hasil\n(Glass-Box Decision)", ha="center", va="center", fontsize=7.5, color="#3b0764")
    
    # Connecting Arrows
    arrow = dict(arrowstyle="->", lw=1.6, color="#475569")
    ax.annotate("", xy=(0.28, 0.54), xytext=(0.23, 0.54), arrowprops=arrow)
    ax.annotate("", xy=(0.50, 0.75), xytext=(0.44, 0.58), arrowprops=arrow)
    ax.annotate("", xy=(0.50, 0.32), xytext=(0.44, 0.48), arrowprops=arrow)
    ax.annotate("", xy=(0.78, 0.32), xytext=(0.72, 0.32), arrowprops=arrow)
    
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    plt.tight_layout()
    p = IMG_DIR / "diagram_2_ml_pipeline.png"
    plt.savefig(p, dpi=300, bbox_inches="tight")
    plt.close()
    print(f"Generated {p}")

# -------------------------------------------------------------
# 3. GENERATE DIAGRAM 3: ENVELOPE ENCRYPTION & SECURITY
# -------------------------------------------------------------
def generate_diagram_3():
    fig, ax = plt.subplots(figsize=(10, 4.6), dpi=300)
    ax.set_facecolor("#f8fafc")
    fig.patch.set_facecolor("#ffffff")
    
    plt.title("Arsitektur Enkripsi Amplop (Envelope Encryption) & Keamanan Data", fontsize=13, fontweight="bold", pad=15, color="#0f172a")
    
    # Master Key box
    r_mk = patches.FancyBboxPatch((0.05, 0.55), 0.22, 0.32, boxstyle="round,pad=0.02", facecolor="#fee2e2", edgecolor="#dc2626", lw=1.5)
    ax.add_patch(r_mk)
    ax.text(0.16, 0.78, "1. Master Key (MK)", ha="center", va="center", fontsize=9, fontweight="bold", color="#7f1d1d")
    ax.text(0.16, 0.65, "256-bit Key Simetris\nTersimpan di Environtment\n(.env Aman / KMS)", ha="center", va="center", fontsize=7.5, color="#450a0a")
    
    # HKDF Box
    r_hkdf = patches.FancyBboxPatch((0.34, 0.55), 0.24, 0.32, boxstyle="round,pad=0.02", facecolor="#fef3c7", edgecolor="#d97706", lw=1.5)
    ax.add_patch(r_hkdf)
    ax.text(0.46, 0.78, "2. HKDF (RFC 5869)", ha="center", va="center", fontsize=9, fontweight="bold", color="#78350f")
    ax.text(0.46, 0.65, "Key Derivation Function\nDerive Key Encryption Key (KEK)\nInfo: Mindora_envelope_kek_v1", ha="center", va="center", fontsize=7.5, color="#451a03")
    
    # Data Key Box
    r_dek = patches.FancyBboxPatch((0.05, 0.10), 0.22, 0.34, boxstyle="round,pad=0.02", facecolor="#e0f2fe", edgecolor="#0284c7", lw=1.5)
    ax.add_patch(r_dek)
    ax.text(0.16, 0.35, "3. Data Key (DEK)", ha="center", va="center", fontsize=9, fontweight="bold", color="#0c4a6e")
    ax.text(0.16, 0.22, "Random 256-bit per baris\nGenerated via random_bytes(32)\nUnik untuk setiap rekaman", ha="center", va="center", fontsize=7.5, color="#082f49")
    
    # AES-256-GCM Box
    r_gcm = patches.FancyBboxPatch((0.34, 0.10), 0.24, 0.34, boxstyle="round,pad=0.02", facecolor="#ede9fe", edgecolor="#7c3aed", lw=1.5)
    ax.add_patch(r_gcm)
    ax.text(0.46, 0.35, "4. AES-256-GCM (AEAD)", ha="center", va="center", fontsize=9, fontweight="bold", color="#4c1d95")
    ax.text(0.46, 0.22, "Enkripsi Data Sensitif\n• 96-bit Nonce IV\n• 128-bit Auth Tag (Anti-Tamper)\nKerahasiaan + Integritas", ha="center", va="center", fontsize=7.5, color="#2e1065")
    
    # Database Envelope Storage
    r_db = patches.FancyBboxPatch((0.68, 0.20), 0.27, 0.60, boxstyle="round,pad=0.02", facecolor="#f1f5f9", edgecolor="#475569", lw=1.8)
    ax.add_patch(r_db)
    ax.text(0.815, 0.72, "Envelope Metadata di DB", ha="center", va="center", fontsize=9, fontweight="bold", color="#0f172a")
    envelope_text = "Tersimpan di MySQL:\n{\n  'v': 'v1',\n  'alg': 'AES-256-GCM',\n  'iv': <base64_data_iv>,\n  'tag': <base64_auth_tag>,\n  'ciphertext': <encrypted_data>,\n  'edek': {\n    'key': <encrypted_dek>,\n    'iv': <kek_iv>,\n    'tag': <kek_tag>\n  }\n}"
    ax.text(0.815, 0.44, envelope_text, ha="center", va="center", fontsize=7, fontfamily="monospace", color="#1e293b")
    
    # Arrows
    arrow = dict(arrowstyle="->", lw=1.6, color="#475569")
    ax.annotate("", xy=(0.34, 0.71), xytext=(0.27, 0.71), arrowprops=arrow)
    ax.annotate("", xy=(0.34, 0.27), xytext=(0.27, 0.27), arrowprops=arrow)
    ax.annotate("", xy=(0.46, 0.44), xytext=(0.46, 0.55), arrowprops=dict(arrowstyle="->", lw=1.6, color="#d97706", linestyle="--"))
    ax.text(0.50, 0.49, "KEK Enkripsi DEK", fontsize=7, color="#b45309", fontweight="bold")
    ax.annotate("", xy=(0.68, 0.50), xytext=(0.58, 0.50), arrowprops=arrow)
    
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    plt.tight_layout()
    p = IMG_DIR / "diagram_3_envelope_encryption.png"
    plt.savefig(p, dpi=300, bbox_inches="tight")
    plt.close()
    print(f"Generated {p}")

if __name__ == "__main__":
    generate_diagram_1()
    generate_diagram_2()
    generate_diagram_3()
