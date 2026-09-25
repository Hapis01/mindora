import os
from pathlib import Path
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import inch
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, Image, KeepTogether, PageBreak, HRFlowable
)
from reportlab.pdfgen import canvas

DOCS_DIR = Path(r"c:\xampp\htdocs\psikotes online\docs")
IMG_DIR = DOCS_DIR / "diagrams"
OUTPUT_PDF = DOCS_DIR / "Dokumentasi_Sistem_Mindora_Metode_ML_Security.pdf"

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        canvas.Canvas.__init__(self, *args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_header_footer(num_pages)
            canvas.Canvas.showPage(self)
        canvas.Canvas.save(self)

    def draw_header_footer(self, page_count):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748b"))
        
        # Draw running header on page 2 and above
        if self._pageNumber > 1:
            self.drawString(54, 800, "MINDORA — Dokumentasi Metodologi, Machine Learning & Keamanan Siber")
            self.setStrokeColor(colors.HexColor("#e2e8f0"))
            self.setLineWidth(0.5)
            self.line(54, 792, 541, 792)

        # Draw running footer on all pages
        page_str = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawRightString(541, 36, page_str)
        self.drawString(54, 36, "Spesifikasi Teknis & Metodologi Mindora (v1.0.0 Enterprise)")
        self.setStrokeColor(colors.HexColor("#e2e8f0"))
        self.setLineWidth(0.5)
        self.line(54, 48, 541, 48)
        
        self.restoreState()

def build_pdf():
    doc = SimpleDocTemplate(
        str(OUTPUT_PDF),
        pagesize=A4,
        leftMargin=54,
        rightMargin=54,
        topMargin=54,
        bottomMargin=54
    )

    styles = getSampleStyleSheet()
    
    # Custom Typography Styles
    title_badge = ParagraphStyle(
        'TitleBadge',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9,
        leading=12,
        textColor=colors.HexColor("#0284c7"),
        alignment=1,
        spaceAfter=4
    )
    
    title_main = ParagraphStyle(
        'TitleMain',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=18,
        leading=22,
        textColor=colors.HexColor("#0f172a"),
        alignment=1,
        spaceAfter=6
    )
    
    title_sub = ParagraphStyle(
        'TitleSub',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=10,
        leading=14,
        textColor=colors.HexColor("#475569"),
        alignment=1,
        spaceAfter=14
    )
    
    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=13,
        leading=16,
        textColor=colors.HexColor("#0f172a"),
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )
    
    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=10.5,
        leading=14,
        textColor=colors.HexColor("#1e293b"),
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=colors.HexColor("#1e293b"),
        spaceAfter=6
    )

    caption_style = ParagraphStyle(
        'Caption_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=7.5,
        leading=10,
        textColor=colors.HexColor("#64748b"),
        alignment=1,
        spaceBefore=3,
        spaceAfter=8
    )

    cell_style = ParagraphStyle(
        'Cell_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=7.5,
        leading=10,
        textColor=colors.HexColor("#1e293b")
    )

    cell_bold = ParagraphStyle(
        'Cell_Bold',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=10,
        textColor=colors.HexColor("#ffffff")
    )

    callout_title = ParagraphStyle(
        'CalloutTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9,
        leading=12,
        textColor=colors.HexColor("#0f172a"),
        spaceAfter=3
    )

    story = []

    # -------------------------------------------------------------
    # HEADER / TITLE SECTION
    # -------------------------------------------------------------
    story.append(Paragraph("DOKUMEN SPESIFIKASI &amp; METODOLOGI SISTEM", title_badge))
    story.append(Paragraph("MINDORA PSYCHOMETRIC &amp; AI INSIGHTS", title_main))
    story.append(Paragraph("Kajian Lengkap Teori Psikometri, Algoritma Machine Learning (XAI), Integrasi Generative AI, dan Arsitektur Keamanan Data", title_sub))
    story.append(HRFlowable(width="100%", thickness=1, color=colors.HexColor("#e2e8f0"), spaceAfter=10))

    # Meta Table
    meta_data = [
        [Paragraph("<b>Klasifikasi Dokumen</b>", cell_style), Paragraph("Dokumentasi Teknis &amp; Metodologis (Whitepaper / Engineering Spec)", cell_style)],
        [Paragraph("<b>Versi Sistem</b>", cell_style), Paragraph("Mindora Platform v1.0.0 Enterprise Release", cell_style)],
        [Paragraph("<b>Tanggal Publikasi</b>", cell_style), Paragraph("September 2026", cell_style)],
        [Paragraph("<b>Tim Penyusun</b>", cell_style), Paragraph("Mindora Core Engineering &amp; Applied Psychometrics Lab", cell_style)],
        [Paragraph("<b>Lingkup Bahasan</b>", cell_style), Paragraph("Asesmen Kognitif, Big Five OCEAN, K-Means (k=4), Random Forest (120 Trees), TreeSHAP (XAI), Google Gemini Live, Envelope Encryption (AES-256-GCM), Argon2id, RBAC &amp; Audit Trails", cell_style)],
    ]
    t_meta = Table(meta_data, colWidths=[1.8 * inch, 5.0 * inch])
    t_meta.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f8fafc")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_meta)
    story.append(Spacer(1, 10))

    # Executive Summary Callout
    exec_content = [
        [Paragraph("<b>📌 Ringkasan Eksekutif (Executive Summary)</b>", callout_title)],
        [Paragraph(
            "Mindora adalah platform asesmen psikometri generasi baru yang mengintegrasikan pengujian kognitif adaptif, pemetaan kepribadian Big Five (OCEAN), serta analisis prediktif berbasis Machine Learning dengan prinsip Explainable AI (XAI) dan Zero-PII. Dokumen ini menyajikan uraian komprehensif mengenai metode ilmiah, formulasi matematis, pipeline machine learning, orkestrasi model bahasa besar (LLM Google Gemini), serta lapisan keamanan bertingkat (Defense-in-Depth) yang mencakup Enkripsi Amplop AES-256-GCM, hashing Argon2id, dan mekanisme re-autentikasi.",
            body_style
        )]
    ]
    t_exec = Table(exec_content, colWidths=[6.8 * inch])
    t_exec.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f0fdf4")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#16a34a")),
        ('TOPPADDING', (0,0), (-1,-1), 6),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ('LEFTPADDING', (0,0), (-1,-1), 10),
        ('RIGHTPADDING', (0,0), (-1,-1), 10),
    ]))
    story.append(t_exec)
    story.append(Spacer(1, 12))

    # -------------------------------------------------------------
    # BAB 1: ARSITEKTUR SISTEM & ALIRAN DATA
    # -------------------------------------------------------------
    story.append(Paragraph("1. Arsitektur Sistem &amp; Aliran Data End-to-End", h1_style))
    story.append(Paragraph(
        "Platform Mindora dirancang dengan arsitektur Decoupled Microservice &amp; Monolith Hybrid yang memisahkan antara Presentation &amp; Business Logic (Laravel 11) dengan Machine Learning Inference Engine (FastAPI Python). Pendekatan ini memastikan skalabilitas horisontal tinggi, konkurensi komputasi matematis yang optimal, serta pemenuhan isolasi keamanan data secara ketat.",
        body_style
    ))

    # Diagram 1
    diag1_path = IMG_DIR / "diagram_1_architecture.png"
    if diag1_path.exists():
        story.append(Image(str(diag1_path), width=6.6 * inch, height=3.3 * inch))
        story.append(Paragraph("Gambar 1.1: Diagram Topologi Arsitektur &amp; Aliran Data End-to-End Mindora", caption_style))

    story.append(Paragraph(
        "Alur komputasi data terbagi menjadi 5 fase operasional:<br/>"
        "<b>1. Pengumpulan Respon Asesmen:</b> Peserta menyelesaikan soal kognitif dan inventori kepribadian melalui web client yang aman (HTTPS, proteksi CSRF, session state encrypted).<br/>"
        "<b>2. Skoring Psikometri Lokal:</b> Laravel memproses jawaban, menerapkan skoring benar-salah untuk kognitif, pembobotan skala Likert 5-poin dengan inversi otomatis untuk butir negatif pada Big Five, serta menghitung telemetri latensi respon.<br/>"
        "<b>3. Ekstraksi Vektor Fitur Anonim (Zero-PII):</b> Data psikometri disarikan menjadi Vektor Fitur 10-Dimensi murni tanpa menyertakan nama asli, email, atau kredensial pengguna.<br/>"
        "<b>4. Inferensi Asinkron ML Microservice:</b> Vektor fitur dikirim melalui HTTP Bearer Token ke FastAPI (Python) untuk dievaluasi oleh K-Means, Random Forest, dan TreeSHAP.<br/>"
        "<b>5. Pengayaan Hasil &amp; Dialog Interaktif:</b> Hasil klasifikasi arketipe, klaster, dan kontribusi fitur (SHAP) disimpan di database terenkripsi dan dijadikan basis konteks pada Asisten AI Google Gemini.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # -------------------------------------------------------------
    # BAB 2: TEORI & METODOLOGI PSIKOMETRI
    # -------------------------------------------------------------
    story.append(Paragraph("2. Landasan Teori &amp; Metodologi Psikometri", h1_style))
    story.append(Paragraph("2.1 Teori Baterai Kognitif (Cattell-Horn-Carroll / CHC Theory)", h2_style))
    story.append(Paragraph(
        "Teori CHC merupakan taksonomi inteligensi manusia paling komprehensif. Mindora mengadaptasi 3 pilar domain kemampuan kognitif primer:",
        body_style
    ))

    cog_data = [
        [Paragraph("Domain Kemampuan", cell_bold), Paragraph("Pilar Teori CHC", cell_bold), Paragraph("Definisi Operasional &amp; Pengukuran", cell_bold)],
        [Paragraph("<b>Penalaran Logis (Logical)</b>", cell_style), Paragraph("Fluid Intelligence (Gf)", cell_style), Paragraph("Kemampuan deduktif, silogisme kondisional, dan inferensi pola aturan tanpa bergantung pada pengetahuan sebelumnya.", cell_style)],
        [Paragraph("<b>Penalaran Numerik (Numerical)</b>", cell_style), Paragraph("Quantitative Knowledge (Gq)", cell_style), Paragraph("Ketajaman kalkulasi aritmatika praktis, identifikasi deret bilangan matematika, dan interpretasi relasi proporsional.", cell_style)],
        [Paragraph("<b>Penalaran Verbal (Verbal)</b>", cell_style), Paragraph("Crystallized Intelligence (Gc)", cell_style), Paragraph("Kemampuan pemahaman bahasa, analogi semantik, diferensiasi makna kontekstual, dan logika silogisme tekstual.", cell_style)],
    ]
    t_cog = Table(cog_data, colWidths=[1.8 * inch, 1.8 * inch, 3.2 * inch])
    t_cog.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#0f172a")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_cog)
    story.append(Spacer(1, 8))

    story.append(Paragraph("2.2 Teori Kepribadian Big Five (Five-Factor Model - OCEAN)", h2_style))
    story.append(Paragraph(
        "Model Lima Faktor (Goldberg, 1990; McCrae &amp; Costa, 1992) memetakan struktur kepribadian universal ke dalam 5 dimensi independen non-klinis:",
        body_style
    ))

    ocean_data = [
        [Paragraph("Dimensi OCEAN", cell_bold), Paragraph("Fokus Perilaku", cell_bold), Paragraph("Deskripsi Karakteristik &amp; Implikasi Kerja", cell_bold)],
        [Paragraph("<b>Openness (O)</b>", cell_style), Paragraph("Keterbukaan Ide &amp; Eksplorasi", cell_style), Paragraph("Mengukur rasa ingin tahu intelektual, apresiasi imajinasi, toleransi kebaruan, dan fleksibilitas berpikir.", cell_style)],
        [Paragraph("<b>Conscientiousness (C)</b>", cell_style), Paragraph("Ketelitian &amp; Disiplin", cell_style), Paragraph("Mengukur derajat organisasi kerja, ketekunan mencapai tujuan, kehati-hatian dalam mengambil keputusan, dan keandalan komitmen.", cell_style)],
        [Paragraph("<b>Extraversion (E)</b>", cell_style), Paragraph("Dinamisme Sosial &amp; Asertivitas", cell_style), Paragraph("Mengukur antusiasme interaksi interpersonal, tingkat energi sosial, ketegasan memimpin, dan ekspresi afektif positif.", cell_style)],
        [Paragraph("<b>Agreeableness (A)</b>", cell_style), Paragraph("Empati &amp; Kerja Sama", cell_style), Paragraph("Mengukur orientasi keharmonisan sosial, keramahan, kepedulian tulus terhadap sesama, dan kecenderungan berkolaborasi.", cell_style)],
        [Paragraph("<b>Neuroticism (N)</b>", cell_style), Paragraph("Sensitivitas Stres &amp; Emosi", cell_style), Paragraph("Mengukur derajat stabilitas emosi, ambang kepekaan terhadap stresor kerja, dan regulasi ketenangan di bawah tekanan.", cell_style)],
    ]
    t_ocean = Table(ocean_data, colWidths=[1.8 * inch, 1.8 * inch, 3.2 * inch])
    t_ocean.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#0f172a")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_ocean)
    story.append(Spacer(1, 8))

    story.append(Paragraph(
        "<b>Teknik Eliminasi Bias Respons (Reverse-Scored Items):</b><br/>"
        "Untuk mencegah bias persetujuan sepihak (acquiescence bias), instrumen kepribadian memadukan butir pernyataan positif dan negatif. Sistem menerapkan algoritma inversi: <code>Skor_Ternormalisasi = 6 - Skor_Mentah</code>.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # -------------------------------------------------------------
    # BAB 3: MACHINE LEARNING & XAI
    # -------------------------------------------------------------
    story.append(Paragraph("3. Teori, Algoritma &amp; Pipeline Machine Learning", h1_style))
    story.append(Paragraph(
        "Pipeline Machine Learning Mindora menggabungkan unsupervised clustering, supervised ensemble learning, dan Explainable AI (XAI) berbasis Teori Permainan Koperasi:",
        body_style
    ))

    # Diagram 2
    diag2_path = IMG_DIR / "diagram_2_ml_pipeline.png"
    if diag2_path.exists():
        story.append(Image(str(diag2_path), width=6.6 * inch, height=3.2 * inch))
        story.append(Paragraph("Gambar 3.1: Alur Pemrosesan Normalisasi, K-Means Clustering, Random Forest &amp; TreeSHAP", caption_style))

    story.append(Paragraph("3.1 Vektor Fitur 10-Dimensi &amp; StandardScaler (Z-Score)", h2_style))
    story.append(Paragraph(
        "Vektor Fitur 10-Dimensi: <code>X = [logical, numerical, verbal, openness, conscientiousness, extraversion, agreeableness, neuroticism, consistency, avg_response_time_ms]</code>.<br/>"
        "Algoritma <b>StandardScaler</b> menstandarisasi fitur melalui rumus Z-Score: <code>z = (x - μ) / σ</code> agar parameter waktu respons (ribuan ms) tidak mendominasi skor psikometri (skala 0–100).",
        body_style
    ))

    story.append(Paragraph("3.2 K-Means Clustering (k=4) — Unsupervised Learning", h2_style))
    story.append(Paragraph(
        "Mengelompokkan pola karakter ke dalam 4 klaster alami dengan meminimalkan inertia jarak Euclidean: <code>arg min_S ∑ ||x - μ_j||²</code>.<br/>"
        "• <b>Klaster 0: Analytical &amp; Structured</b> (Kognitif Logika-Numerik Tinggi + Ketelitian C Tinggi).<br/>"
        "• <b>Klaster 1: Independent &amp; Task-Oriented</b> (Fokus target mandiri, disiplin tinggi).<br/>"
        "• <b>Klaster 2: Social &amp; Collaborative</b> (Extraversion E Tinggi + Agreeableness A Tinggi).<br/>"
        "• <b>Klaster 3: Creative &amp; Explorative</b> (Openness O Tinggi + Penalaran Abstrak Inovatif).<br/>"
        "• <b>Silhouette Score (0.1696):</b> Menunjukkan spektrum kontinum alami kepribadian manusia dalam ruang 10-dimensi.",
        body_style
    ))

    story.append(Paragraph("3.3 Random Forest Classifier (n=120) — Supervised Learning", h2_style))
    story.append(Paragraph(
        "Ensemble 120 Pohon Keputusan (Decision Trees) dengan parameter <code>n_estimators=120</code>, <code>max_depth=7</code>, <code>class_weight='balanced'</code>.<br/>"
        "• <b>Akurasi Model: 91.75%</b> pada data normatif terstandarisasi.<br/>"
        "• <b>Confidence Score:</b> Dihitung dari proporsi persentase voting pohon mayoritas terhadap arketipe pemenang.",
        body_style
    ))

    story.append(Paragraph("3.4 Explainable AI (XAI): TreeSHAP (Shapley Additive exPlanations)", h2_style))
    story.append(Paragraph(
        "Berdasarkan Teori Permainan Koperasi (Lloyd Shapley, Nobel 1953; Scott Lundberg, 2017). Menghitung kontribusi marjinal setiap fitur terhadap deviasi prediksi dari nilai dasar (base value): <code>f(x) = φ_0 + ∑ φ_i</code>.<br/>"
        "Transparansi Penuh: Pengguna dapat memahami secara gamblang bahwa profil kerja mereka terbentuk karena misalnya <i>'Penalaran Logika berkontribusi +14.2% dan Ketelitian +8.5%'</i>.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # -------------------------------------------------------------
    # BAB 4: GENERATIVE AI (GEMINI LIVE)
    # -------------------------------------------------------------
    story.append(Paragraph("4. Integrasi Generative AI (Google Gemini Live)", h1_style))
    story.append(Paragraph(
        "Mindora mengintegrasikan Google Gemini Live API (<code>gemini-3.6-flash</code>) melalui paradigma <b>Context-Conditioned Grounding</b>:<br/>"
        "• <b>Context Injection:</b> Skor 10 dimensi, arketipe ML, klaster perilaku, serta faktor pendorong TreeSHAP disuntikkan secara dinamis ke System Prompt.<br/>"
        "• <b>Alokasi 4096 Tokens:</b> Memastikan proses penalaran internal (thinking process) model tuntas tanpa memotong respons pengguna.<br/>"
        "• <b>Sanitasi Output Teks Biasa:</b> Regex filter otomatis membersihkan tanda bintang tebal (**), menghasilkan respons bersih dan nyaman dibaca.<br/>"
        "• <b>Guardrails Etis:</b> Larangan keras memberikan vonis klinis/psikiatri; fokus pada rekomendasi pengembangan karir dan potensi diri.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # -------------------------------------------------------------
    # BAB 5: KEAMANAN SIBER & PROTEKSI DATA
    # -------------------------------------------------------------
    story.append(Paragraph("5. Arsitektur Keamanan Siber &amp; Proteksi Data", h1_style))
    story.append(Paragraph(
        "Kepatuhan ketat terhadap standar ISO/IEC 27001 dan UU Pelindungan Data Pribadi (UU PDP No. 27/2022):",
        body_style
    ))

    # Diagram 3
    diag3_path = IMG_DIR / "diagram_3_envelope_encryption.png"
    if diag3_path.exists():
        story.append(Image(str(diag3_path), width=6.6 * inch, height=3.0 * inch))
        story.append(Paragraph("Gambar 5.1: Topologi Enkripsi Amplop (Envelope Encryption) AES-256-GCM", caption_style))

    story.append(Paragraph(
        "<b>1. Enkripsi Amplop (Envelope Encryption) AES-256-GCM:</b><br/>"
        "• Master Key (MK) 256-bit terisolasi di file environment KMS.<br/>"
        "• Key Encryption Key (KEK) diturunkan via HKDF (RFC 5869) SHA-256.<br/>"
        "• Data Encryption Key (DEK) 256-bit di-generate acak per baris data via <code>random_bytes(32)</code>.<br/>"
        "• AES-256-GCM (AEAD) menyediakan 96-bit Nonce IV dan 128-bit Authentication Tag yang menjamin kerahasiaan sekaligus integritas anti-tamper.<br/><br/>"
        "<b>2. Hashing Kata Sandi Argon2id:</b><br/>"
        "Pemenang Password Hashing Competition (PHC). Konfigurasi: <code>memory = 65536 KB (64 MB)</code>, <code>time = 4 iterasi</code>, <code>threads = 1</code>. Kebal terhadap serangan brute-force berbasis akselerasi GPU/ASIC/FPGA.<br/><br/>"
        "<b>3. Penyamaran Data (Profile Masking) &amp; Gerbang Re-Authentication:</b><br/>"
        "Data sensitif disamarkan secara default (misal: <code>0812-****-5678</code>). Pembukaan data mewajibkan input ulang kata sandi akun.<br/><br/>"
        "<b>4. Role-Based Access Control (RBAC) &amp; Security Audit Trail:</b><br/>"
        "Hak akses dipisah tegas (User, Admin, Super Admin) via middleware Laravel. Seluruh aksi dicatat kekal di tabel <code>audit_logs</code> bersama IP address, user-agent, dan stempel waktu ISO 8601.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # -------------------------------------------------------------
    # BAB 6: MATRIKS METODE & SPESIFIKASI
    # -------------------------------------------------------------
    story.append(Paragraph("6. Matriks Komprehensif Seluruh Metode &amp; Spesifikasi", h1_style))
    story.append(Paragraph(
        "Tabel berikut merangkum seluruh metode, algoritma, parameter teknis, dan fungsi utama sistem Mindora:",
        body_style
    ))

    matrix_data = [
        [Paragraph("Komponen Sistem", cell_bold), Paragraph("Nama Metode / Algoritma", cell_bold), Paragraph("Parameter / Spesifikasi", cell_bold), Paragraph("Fungsi Utama &amp; Output", cell_bold)],
        [Paragraph("<b>Psikometri Kognitif</b>", cell_style), Paragraph("Cattell-Horn-Carroll (CHC)", cell_style), Paragraph("3 Sub-tes: Logika, Numerik, Verbal", cell_style), Paragraph("Memetakan kapasitas pemrosesan intelektual &amp; pemecahan masalah.", cell_style)],
        [Paragraph("<b>Psikometri Karakter</b>", cell_style), Paragraph("Big Five Model (OCEAN)", cell_style), Paragraph("5 Dimensi, Likert 5-poin, Inversi Butir", cell_style), Paragraph("Mengukur dinamika kepribadian, gaya komunikasi, dan perilaku kerja.", cell_style)],
        [Paragraph("<b>Telemetri Perilaku</b>", cell_style), Paragraph("Reaction Time &amp; Consistency", cell_style), Paragraph("Milidetik per butir soal", cell_style), Paragraph("Mendeteksi konsistensi responden dan gaya kognitif (reflektif/impulsif).", cell_style)],
        [Paragraph("<b>Normalisasi Data</b>", cell_style), Paragraph("StandardScaler (Z-Score)", cell_style), Paragraph("Mean μ=0, Simpangan Baku σ=1", cell_style), Paragraph("Menghilangkan bias skala antara waktu respons dan skor psikometri.", cell_style)],
        [Paragraph("<b>Unsupervised ML</b>", cell_style), Paragraph("K-Means Clustering", cell_style), Paragraph("k=4 Klaster, Jarak Euclidean", cell_style), Paragraph("Mengelompokkan pola karakter ke dalam 4 klaster alami (Silhouette: 0.1696).", cell_style)],
        [Paragraph("<b>Supervised ML</b>", cell_style), Paragraph("Random Forest Classifier", cell_style), Paragraph("120 Estimator, Max Depth 7", cell_style), Paragraph("Memprediksi arketipe profil kerja dominan dengan Akurasi 91.75%.", cell_style)],
        [Paragraph("<b>Explainable AI (XAI)</b>", cell_style), Paragraph("TreeSHAP (Shapley)", cell_style), Paragraph("Nilai Shapley Game Theory", cell_style), Paragraph("Menghitung kontribusi persentase pendorong skor secara transparan.", cell_style)],
        [Paragraph("<b>Generative AI</b>", cell_style), Paragraph("Google Gemini Live Engine", cell_style), Paragraph("gemini-3.6-flash, 4096 Tokens", cell_style), Paragraph("Memberikan konsultasi karir &amp; pengembangan diri terpersonalisasi.", cell_style)],
        [Paragraph("<b>Enkripsi Data Sensitif</b>", cell_style), Paragraph("Envelope Encryption", cell_style), Paragraph("HKDF SHA-256 + AES-256-GCM", cell_style), Paragraph("Mengamankan data profil sensitif dengan jaminan anti-tamper (AEAD).", cell_style)],
        [Paragraph("<b>Hashing Password</b>", cell_style), Paragraph("Argon2id Algorithm", cell_style), Paragraph("Memori 64MB, Waktu 4 Putaran", cell_style), Paragraph("Melindungi kredensial terhadap serangan brute-force GPU/ASIC.", cell_style)],
        [Paragraph("<b>Proteksi Privasi</b>", cell_style), Paragraph("Zero-PII Feature Vector", cell_style), Paragraph("Pseudo-Anonymized Hash ID", cell_style), Paragraph("Menjamin model ML dan AI sama sekali tidak mengakses identitas pribadi.", cell_style)],
    ]
    t_matrix = Table(matrix_data, colWidths=[1.5 * inch, 1.7 * inch, 1.8 * inch, 1.8 * inch])
    t_matrix.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#0f172a")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#cbd5e1")),
        ('TOPPADDING', (0,0), (-1,-1), 3.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3.5),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_matrix)
    story.append(Spacer(1, 12))

    # Referensi Ilmiah
    story.append(Paragraph("Daftar Pustaka &amp; Referensi Ilmiah", h1_style))
    refs = [
        "1. Breiman, L. (2001). Random Forests. Machine Learning, 45(1), 5-32.",
        "2. Goldberg, L. R. (1990). An alternative 'description of personality': The Big-Five factor structure. Journal of Personality and Social Psychology, 59(6), 1216-1229.",
        "3. Lundberg, S. M., &amp; Lee, S. I. (2017). A Unified Approach to Interpreting Model Predictions. Advances in Neural Information Processing Systems (NeurIPS 2017), 30.",
        "4. McCrae, R. R., &amp; Costa, P. T. (1992). An introduction to the five-factor model and its applications. Journal of Personality, 60(2), 175-215.",
        "5. Krawczyk, H., &amp; Eronen, P. (2010). HMAC-based Extract-and-Expand Key Derivation Function (HKDF). RFC 5869, IETF.",
        "6. Dworkin, M. (2007). Recommendation for Block Cipher Modes of Operation: Galois/Counter Mode (GCM). NIST SP 800-38D.",
        "7. Biryukov, A., Dinu, D., &amp; Khovratovich, A. (2016). Argon2: the memory-hard function for password hashing. Password Hashing Competition."
    ]
    for r in refs:
        story.append(Paragraph(r, caption_style))

    # Build Document with NumberedCanvas
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"PDF successfully created at: {OUTPUT_PDF}")

if __name__ == "__main__":
    build_pdf()
