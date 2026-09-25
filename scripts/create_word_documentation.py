import os
from pathlib import Path
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import qn, nsdecls

DOCS_DIR = Path(r"c:\xampp\htdocs\psikotes online\docs")
IMG_DIR = DOCS_DIR / "diagrams"
OUTPUT_FILE = DOCS_DIR / "Dokumentasi_Sistem_Mindora_Metode_ML_Security.docx"

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=120, bottom=120, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def set_cell_borders(cell, color="CBD5E1", sz="4", val="single"):
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:left w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:right w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

def create_styled_table(doc, headers, rows_data, col_widths=None):
    table = doc.add_table(rows=len(rows_data) + 1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False

    # Header Row
    hdr_cells = table.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].text = title
        set_cell_background(hdr_cells[i], "0F172A") # Dark Navy
        set_cell_margins(hdr_cells[i], top=140, bottom=140, left=160, right=160)
        set_cell_borders(hdr_cells[i], color="334155", sz="6")
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        for run in p.runs:
            run.font.bold = True
            run.font.size = Pt(9.5)
            run.font.color.rgb = RGBColor(255, 255, 255)
            run.font.name = "Segoe UI"

    # Data Rows
    for r_idx, row in enumerate(rows_data):
        row_cells = table.rows[r_idx + 1].cells
        bg_color = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row):
            row_cells[c_idx].text = str(val)
            set_cell_background(row_cells[c_idx], bg_color)
            set_cell_margins(row_cells[c_idx], top=100, bottom=100, left=140, right=140)
            set_cell_borders(row_cells[c_idx], color="E2E8F0", sz="4")
            p = row_cells[c_idx].paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for run in p.runs:
                run.font.size = Pt(9)
                run.font.color.rgb = RGBColor(30, 41, 59)
                run.font.name = "Segoe UI"

    # Column widths
    if col_widths:
        for row in table.rows:
            for idx, width in enumerate(col_widths):
                row.cells[idx].width = width

    doc.add_paragraph() # Spacing
    return table

def add_callout(doc, title, text, bg_hex="EFF6FF", border_hex="2563EB"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    
    set_cell_background(cell, bg_hex)
    set_cell_margins(cell, top=140, bottom=140, left=180, right=180)
    
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="none"/>'
        f'<w:bottom w:val="none"/>'
        f'<w:left w:val="single" w:sz="24" w:space="0" w:color="{border_hex}"/>'
        f'<w:right w:val="none"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(4)
    run_title = p.add_run(f"📌 {title}\n")
    run_title.font.bold = True
    run_title.font.size = Pt(10)
    run_title.font.color.rgb = RGBColor(15, 23, 42)
    run_title.font.name = "Segoe UI"
    
    run_text = p.add_run(text)
    run_text.font.size = Pt(9)
    run_text.font.color.rgb = RGBColor(51, 65, 85)
    run_text.font.name = "Segoe UI"
    
    doc.add_paragraph()

def build_word_document():
    doc = Document()

    # Set Margins (A4)
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)
        section.page_width = Inches(8.27)
        section.page_height = Inches(11.69)
        
        # Header & Footer
        header = section.header
        hp = header.paragraphs[0]
        hp.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        hrun = hp.add_run("MINDORA — Dokumentasi Metodologi, Machine Learning & Keamanan Siber")
        hrun.font.size = Pt(8)
        hrun.font.color.rgb = RGBColor(148, 163, 184)
        hrun.font.name = "Segoe UI"
        
        footer = section.footer
        fp = footer.paragraphs[0]
        fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
        frun = fp.add_run("Rahasia & Dilindungi — Dokumen Spesifikasi Teknis Mindora (v1.0.0)")
        frun.font.size = Pt(8)
        frun.font.color.rgb = RGBColor(148, 163, 184)
        frun.font.name = "Segoe UI"

    # Set Default Font
    style_normal = doc.styles['Normal']
    style_normal.font.name = 'Segoe UI'
    style_normal.font.size = Pt(10)
    style_normal.font.color.rgb = RGBColor(30, 41, 59)

    # -------------------------------------------------------------
    # COVER / HEADER TITLE
    # -------------------------------------------------------------
    title_p = doc.add_paragraph()
    title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    title_p.paragraph_format.space_before = Pt(12)
    title_p.paragraph_format.space_after = Pt(2)
    
    badge = title_p.add_run("DOKUMEN SPESIFIKASI & METODOLOGI SISTEM\n")
    badge.font.size = Pt(10)
    badge.font.bold = True
    badge.font.color.rgb = RGBColor(2, 132, 199)
    
    run_main = title_p.add_run("MINDORA PSYCHOMETRIC & AI INSIGHTS\n")
    run_main.font.size = Pt(22)
    run_main.font.bold = True
    run_main.font.color.rgb = RGBColor(15, 23, 42)
    
    run_sub = title_p.add_run("Kajian Lengkap Teori Psikometri, Algoritma Machine Learning (XAI),\nIntegrasi Generative AI, dan Arsitektur Keamanan Data")
    run_sub.font.size = Pt(11.5)
    run_sub.font.color.rgb = RGBColor(100, 116, 139)
    
    doc.add_paragraph()

    # Meta Info Box
    meta_box_data = [
        ["Klasifikasi Dokumen", "Dokumentasi Teknis & Metodologis (Whitepaper / Engineering Spec)"],
        ["Versi Sistem", "Mindora Platform v1.0.0 Enterprise Release"],
        ["Tanggal Publikasi", "September 2026"],
        ["Tim Penyusun", "Mindora Core Engineering & Applied Psychometrics Lab"],
        ["Lingkup Bahasan", "Asesmen Kognitif, Big Five OCEAN, K-Means (k=4), Random Forest (120 Trees), TreeSHAP (XAI), Google Gemini Live, Envelope Encryption (AES-256-GCM), Argon2id, RBAC & Audit Trails"]
    ]
    create_styled_table(doc, ["Parameter", "Keterangan Spesifikasi"], meta_box_data, [Inches(2.0), Inches(4.5)])

    # Executive Summary Callout
    add_callout(
        doc,
        "Ringkasan Eksekutif (Executive Summary)",
        "Mindora adalah platform asesmen psikometri generasi baru yang mengintegrasikan pengujian kognitif adaptif, pemetaan kepribadian Big Five (OCEAN), serta analisis prediktif berbasis Machine Learning dengan prinsip Explainable AI (XAI) dan Zero-PII. Dokumen ini menyajikan uraian komprehensif mengenai metode ilmiah, formulasi matematis, pipeline machine learning, orkestrasi model bahasa besar (LLM Google Gemini), serta lapisan keamanan bertingkat (Defense-in-Depth) yang mencakup Enkripsi Amplop AES-256-GCM, hashing Argon2id, dan mekanisme re-autentikasi.",
        bg_hex="F0FDF4",
        border_hex="16A34A"
    )

    # -------------------------------------------------------------
    # BAB 1: ARSITEKTUR SISTEM & ALIRAN DATA END-TO-END
    # -------------------------------------------------------------
    h1 = doc.add_heading(level=1)
    run_h1 = h1.add_run("1. Arsitektur Sistem & Aliran Data End-to-End")
    run_h1.font.color.rgb = RGBColor(15, 23, 42)
    run_h1.font.size = Pt(15)
    
    p = doc.add_paragraph(
        "Platform Mindora dirancang dengan arsitektur Decoupled Microservice & Monolith Hybrid yang memisahkan antara Presentation & Business Logic (Laravel 11) dengan Machine Learning Inference Engine (FastAPI Python). Pendekatan ini memastikan skalabilitas horisontal tinggi, konkurensi komputasi matematis yang optimal, serta pemenuhan isolasi keamanan data secara ketat."
    )

    # Add Diagram 1
    diag1_path = IMG_DIR / "diagram_1_architecture.png"
    if diag1_path.exists():
        doc.add_paragraph()
        img_p = doc.add_paragraph()
        img_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        img_p.paragraph_format.space_before = Pt(6)
        img_p.paragraph_format.space_after = Pt(2)
        doc.add_picture(str(diag1_path), width=Inches(6.3))
        
        cap = doc.add_paragraph()
        cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        cap.paragraph_format.space_after = Pt(12)
        r_cap = cap.add_run("Gambar 1.1: Diagram Topologi Arsitektur & Aliran Data End-to-End Mindora")
        r_cap.font.italic = True
        r_cap.font.size = Pt(8.5)
        r_cap.font.color.rgb = RGBColor(100, 116, 139)

    doc.add_paragraph(
        "Alur komputasi data terbagi menjadi 5 fase operasional:\n"
        "1. Pengumpulan Respon Asesmen: Peserta menyelesaikan soal kognitif dan inventori kepribadian melalui web client yang aman (HTTPS, proteksi CSRF, session state encrypted).\n"
        "2. Skoring Psikometri Lokal: Laravel memproses jawaban, menerapkan skoring benar-salah untuk kognitif, pembobotan skala Likert 5-poin dengan inversi otomatis untuk butir negatif pada Big Five, serta menghitung telemetri latensi respon.\n"
        "3. Ekstraksi Vektor Fitur Anonim (Zero-PII): Data psikometri disarikan menjadi Vektor Fitur 10-Dimensi murni tanpa menyertakan nama asli, email, atau kredensial pengguna.\n"
        "4. Inferensi Asinkron ML Microservice: Vektor fitur dikirim melalui HTTP Bearer Token ke FastAPI (Python) untuk dievaluasi oleh K-Means, Random Forest, dan TreeSHAP.\n"
        "5. Pengayaan Hasil & Dialog Interaktif: Hasil klasifikasi arketipe, klaster, dan kontribusi fitur (SHAP) disimpan di database terenkripsi dan dijadikan basis konteks pada Asisten AI Google Gemini."
    )

    # -------------------------------------------------------------
    # BAB 2: LANDASAN TEORI & METODOLOGI PSIKOMETRI
    # -------------------------------------------------------------
    h2 = doc.add_heading(level=1)
    run_h2 = h2.add_run("2. Landasan Teori & Metodologi Psikometri")
    run_h2.font.color.rgb = RGBColor(15, 23, 42)
    run_h2.font.size = Pt(15)

    doc.add_paragraph(
        "Instrumen pengukuran pada Mindora didasarkan pada dua kerangka teoritis psikologi yang telah divalidasi secara luas dalam literatur ilmiah internasional:"
    )

    doc.add_heading(level=2).add_run("2.1 Teori Baterai Kognitif (Cattell-Horn-Carroll / CHC Theory)")
    doc.add_paragraph(
        "Teori CHC merupakan taksonomi inteligensi manusia paling komprehensif. Mindora mengadaptasi 3 pilar domain kemampuan kognitif primer:"
    )

    cog_table = [
        ["Penalaran Logis (Logical Reasoning)", "Fluid Intelligence (Gf)", "Kemampuan deduktif, silogisme kondisional, dan inferensi pola aturan tanpa bergantung pada pengetahuan sebelumnya."],
        ["Penalaran Numerik (Numerical Reasoning)", "Quantitative Knowledge (Gq)", "Ketajaman kalkulasi aritmatika praktis, identifikasi deret bilangan matematika, dan interpretasi relasi proporsional."],
        ["Penalaran Verbal (Verbal Reasoning)", "Crystallized Intelligence (Gc)", "Kemampuan pemahaman bahasa, analogi semantik, diferensiasi makna kontekstual, dan logika silogisme tekstual."]
    ]
    create_styled_table(doc, ["Domain Kemampuan", "Pilar Teori CHC", "Definisi Operasional & Pengukuran"], cog_table, [Inches(1.8), Inches(1.7), Inches(3.0)])

    doc.add_heading(level=2).add_run("2.2 Teori Kepribadian Big Five (Five-Factor Model - OCEAN)")
    doc.add_paragraph(
        "Model Lima Faktor (Goldberg, 1990; McCrae & Costa, 1992) memetakan struktur kepribadian universal ke dalam 5 dimensi independen non-klinis:"
    )

    ocean_table = [
        ["Openness to Experience (O)", "Keterbukaan Ide & Eksplorasi", "Mengukur rasa ingin tahu intelektual, apresiasi imajinasi, toleransi kebaruan, dan fleksibilitas berpikir."],
        ["Conscientiousness (C)", "Ketelitian, Regulasi & Disiplin", "Mengukur derajat organisasi kerja, ketekunan mencapai tujuan, kehati-hatian dalam mengambil keputusan, dan keandalan komitmen."],
        ["Extraversion (E)", "Dinamisme Sosial & Asertivitas", "Mengukur antusiasme interaksi interpersonal, tingkat energi sosial, ketegasan memimpin, dan ekspresi afektif positif."],
        ["Agreeableness (A)", "Empati, Kepercayaan & Kooperatif", "Mengukur orientasi keharmonisan sosial, keramahan, kepedulian tulus terhadap sesama, dan kecenderungan berkolaborasi."],
        ["Neuroticism (N)", "Sensitivitas Responsif & Emosionalitas", "Mengukur derajat stabilitas emosi, ambang kepekaan terhadap stresor kerja, dan regulasi ketenangan di bawah tekanan."]
    ]
    create_styled_table(doc, ["Dimensi OCEAN", "Fokus Perilaku", "Deskripsi Karakteristik & Implikasi Kerja"], ocean_table, [Inches(1.8), Inches(1.8), Inches(2.9)])

    doc.add_paragraph(
        "Teknik Eliminasi Bias Respons (Reverse-Scored Items):\n"
        "Untuk mencegah bias persetujuan sepihak (acquiescence bias), instrumen kepribadian memadukan butir pernyataan positif dan negatif. Sistem secara otomatis menerapkan algoritma pembalikan nilai pada butir terbalik:"
    )
    doc.add_paragraph(
        "Skor_Ternormalisasi = (Skor_Maksimum + 1) - Skor_Mentah = 6 - Skor_Mentah",
        style='Normal'
    )

    doc.add_heading(level=2).add_run("2.3 Telemetri Waktu Respons & Indeks Konsistensi")
    doc.add_paragraph(
        "Mindora mengumpulkan data perilaku implisit (implicit behavioral signals) selama proses pengujian:\n"
        "• Latensi Respons Rata-rata (avg_response_time_ms): Menangkap kecepatan pemrosesan mental dan gaya kognitif (reflektif vs impulsif).\n"
        "• Indeks Konsistensi Respons: Mengukur variansi simpangan baku pada butir-butir serupa untuk mendeteksi pola anomali seperti menjawab acak atau pola seragam."
    )

    # -------------------------------------------------------------
    # BAB 3: TEORI, ALGORITMA & PIPELINE MACHINE LEARNING
    # -------------------------------------------------------------
    h3 = doc.add_heading(level=1)
    run_h3 = h3.add_run("3. Teori, Algoritma & Pipeline Machine Learning")
    run_h3.font.color.rgb = RGBColor(15, 23, 42)
    run_h3.font.size = Pt(15)

    doc.add_paragraph(
        "Pipeline Machine Learning Mindora dirancang secara end-to-end dengan menggabungkan pemrosesan unsupervised (K-Means), supervised ensemble learning (Random Forest), dan Explainable AI (TreeSHAP)."
    )

    # Add Diagram 2
    diag2_path = IMG_DIR / "diagram_2_ml_pipeline.png"
    if diag2_path.exists():
        doc.add_paragraph()
        img_p = doc.add_paragraph()
        img_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        img_p.paragraph_format.space_before = Pt(6)
        img_p.paragraph_format.space_after = Pt(2)
        doc.add_picture(str(diag2_path), width=Inches(6.3))
        
        cap = doc.add_paragraph()
        cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        cap.paragraph_format.space_after = Pt(12)
        r_cap = cap.add_run("Gambar 3.1: Alur Pemrosesan Normalisasi, K-Means Clustering, Random Forest & TreeSHAP")
        r_cap.font.italic = True
        r_cap.font.size = Pt(8.5)
        r_cap.font.color.rgb = RGBColor(100, 116, 139)

    doc.add_heading(level=2).add_run("3.1 Vektor Fitur 10-Dimensi & Standarisasi Z-Score")
    doc.add_paragraph(
        "Setiap asesmen yang selesai dievaluasi dipetakan ke dalam Vektor Fitur 10-Dimensi:\n"
        "X = [logical, numerical, verbal, openness, conscientiousness, extraversion, agreeableness, neuroticism, consistency, avg_response_time_ms]\n\n"
        "Untuk mencegah dominasi atribut waktu (ribuan milidetik) terhadap atribut psikometri (0-100), diterapkan algoritma StandardScaler (Z-score normalization):"
    )
    doc.add_paragraph(
        "z_i = (x_i - μ_i) / σ_i\n"
        "di mana μ_i adalah nilai rata-rata normatif dan σ_i adalah simpangan baku dari data acuan.",
        style='Normal'
    )

    doc.add_heading(level=2).add_run("3.2 K-Means Clustering (k=4) — Unsupervised Learning")
    doc.add_paragraph(
        "Algoritma K-Means digunakan untuk mendeteksi pola pengelompokan alami dalam populasi tanpa intervensi label apriori. K-Means mempartisi ruang 10-dimensi ke dalam 4 klaster dengan meminimalkan inertia (within-cluster sum of squares):"
    )
    doc.add_paragraph(
        "Inertia = argmin_S ∑_{j=1}^{k} ∑_{x ∈ S_j} ||x - μ_j||²\n"
        "di mana μ_j merepresentasikan titik pusat (centroid) klaster ke-j.",
        style='Normal'
    )

    kmeans_table = [
        ["Klaster 0: Analytical & Structured", "Kognitif Logika-Numerik Tinggi + Ketelitian (C) Tinggi", "Profil pemikir sistematis, teliti, berbasis data, dan terstruktur."],
        ["Klaster 1: Independent & Task-Oriented", "Kognitif Stabil + Ketekunan Tinggi + Mandiri", "Profil pelaksana independen dengan fokus target terarah dan disiplin mandiri."],
        ["Klaster 2: Social & Collaborative", "Extraversion (E) Tinggi + Agreeableness (A) Tinggi", "Profil fasilitator relasi sosial yang unggul dalam komunikasi tim dan empati."],
        ["Klaster 3: Creative & Explorative", "Openness (O) Tinggi + Penalaran Abstrak Tinggi", "Profil inovator yang adaptif, eksploratif, dan kaya akan gagasan solutif baru."]
    ]
    create_styled_table(doc, ["Klaster & Label Perilaku", "Karakteristik Parameter Fitur", "Interpretasi Pola Kerja & Kolaborasi"], kmeans_table, [Inches(2.1), Inches(2.1), Inches(2.3)])

    doc.add_paragraph(
        "Evaluasi K-Means:\n"
        "• Silhouette Score (0.1696): Menunjukkan distribusi spektrum kepribadian manusia alami di ruang berdimensi 10 (bukan partisi kaku diskrit buatan).\n"
        "• Jarak Euclidean ke Centroid: Setiap prediksi mencatat jarak geometris peserta ke pusat klasternya untuk mengukur tingkat kekhasan pola profil."
    )

    doc.add_heading(level=2).add_run("3.3 Random Forest Classifier (n=120) — Supervised Learning")
    doc.add_paragraph(
        "Untuk menentukan arketipe profil kerja secara definitif, digunakan Random Forest Classifier (Breiman, 2001). Model terdiri dari 120 Pohon Keputusan (Decision Trees) dengan parameter:\n"
        "• n_estimators = 120\n"
        "• max_depth = 7 (mencegah overfitting)\n"
        "• min_samples_split = 4\n"
        "• class_weight = 'balanced' (mencegah bias kelas mayoritas)\n\n"
        "Tingkat Akurasi Model mencapai 91.75% pada data normatif. Klasifikasi ditentukan melalui agregasi mayoritas voting (Majority Voting):\n"
        "Confidence Score (%) = (Jumlah Pohon yang Memilih Kelas Pemenang / 120) × 100%"
    )

    doc.add_heading(level=2).add_run("3.4 Explainable AI (XAI): TreeSHAP (Shapley Additive exPlanations)")
    doc.add_paragraph(
        "Mindora tidak membiarkan keputusan AI menjadi kotak hitam. Digunakan TreeSHAP (Lundberg & Lee, 2017) yang mengadaptasi Teori Permainan Koperasi (Lloyd Shapley, Nobel 1953) untuk menghitung kontribusi marjinal setiap fitur:\n"
        "f(x) = φ_0 + ∑_{i=1}^{M} φ_i\n"
        "di mana φ_0 adalah nilai dasar rata-rata (base value) dan φ_i adalah kontribusi spesifik fitur i terhadap deviasi hasil prediksi.\n\n"
        "Dengan metode ini, pengguna dapat melihat alasan akurat mengapa mereka mendapatkan profil tertentu (contoh: 'Kemampuan Logika berkontribusi +14.2% dan Ketelitian +8.5% terhadap profil Analytical Strategist')."
    )

    # -------------------------------------------------------------
    # BAB 4: INTEGRASI GENERATIVE AI (GOOGLE GEMINI LIVE)
    # -------------------------------------------------------------
    h4 = doc.add_heading(level=1)
    run_h4 = h4.add_run("4. Integrasi Generative AI (Google Gemini Live)")
    run_h4.font.color.rgb = RGBColor(15, 23, 42)
    run_h4.font.size = Pt(15)

    doc.add_paragraph(
        "Untuk memberikan pengalaman konsultasi personal, Mindora mengintegrasikan Google Gemini Live API (gemini-3.6-flash) menggunakan paradigma Context-Conditioned Grounding:"
    )

    ai_table = [
        ["Context Injection Engine", "Sistem merangkai ringkasan skor 10 dimensi, arketipe kerja, klaster perilaku, serta fitur pendorong SHAP ke dalam System Prompt secara dinamis."],
        ["Alokasi Token (4096 Tokens)", "Mengalokasikan kapasitas token output maksimal sebesar 4096 agar proses penalaran internal (thinking process) model tuntas tanpa memotong jawaban utama."],
        ["Sanitasi Output (Plain Text)", "Sistem menerapkan pembersihan ekspresi reguler (regex post-processing) untuk membuang simbol markdown bintang berlebih (**), menghasilkan teks yang bersih dan rapi."],
        ["Batasan Etis & Non-Klinis", "Model dilengkapi guardrails ketat yang melarang pemberian vonis atau diagnosis medis/psikiatri klinis, melainkan fokus pada pengembangan potensi diri dan karir."]
    ]
    create_styled_table(doc, ["Mekanisme Integrasi", "Implementasi Teknis & Manfaat Pengguna"], ai_table, [Inches(2.2), Inches(4.3)])

    # -------------------------------------------------------------
    # BAB 5: ARSITEKTUR KEAMANAN SIBER & PROTEKSI DATA
    # -------------------------------------------------------------
    h5 = doc.add_heading(level=1)
    run_h5 = h5.add_run("5. Arsitektur Keamanan Siber & Proteksi Data")
    run_h5.font.color.rgb = RGBColor(15, 23, 42)
    run_h5.font.size = Pt(15)

    doc.add_paragraph(
        "Keamanan dan privasi data pada Mindora dirancang sesuai dengan standar regulasi internasional (GDPR, ISO/IEC 27001) serta Undang-Undang Perlindungan Data Pribadi (UU PDP No. 27/2022)."
    )

    # Add Diagram 3
    diag3_path = IMG_DIR / "diagram_3_envelope_encryption.png"
    if diag3_path.exists():
        doc.add_paragraph()
        img_p = doc.add_paragraph()
        img_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        img_p.paragraph_format.space_before = Pt(6)
        img_p.paragraph_format.space_after = Pt(2)
        doc.add_picture(str(diag3_path), width=Inches(6.3))
        
        cap = doc.add_paragraph()
        cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        cap.paragraph_format.space_after = Pt(12)
        r_cap = cap.add_run("Gambar 5.1: Topologi Enkripsi Amplop (Envelope Encryption) AES-256-GCM")
        r_cap.font.italic = True
        r_cap.font.size = Pt(8.5)
        r_cap.font.color.rgb = RGBColor(100, 116, 139)

    doc.add_heading(level=2).add_run("5.1 Enkripsi Amplop (Envelope Encryption) dengan AES-256-GCM")
    doc.add_paragraph(
        "Untuk melindungi data identitas sensitif (seperti nomor telepon dan data profil lengkap), Mindora menerapkan Enkripsi Amplop bertingkat:\n"
        "1. Master Key (MK): Kunci simetris 256-bit tingkat master yang disimpan dalam lingkungan server yang terisolasi.\n"
        "2. Key Encryption Key (KEK): Diturunkan dari Master Key menggunakan standar kriptografi HKDF (RFC 5869) berbasis HMAC-SHA-256.\n"
        "3. Data Encryption Key (DEK): Setiap kali baris data sensitif disimpan, sistem menghasilkan kunci 256-bit unik melalui fungsi random_bytes(32).\n"
        "4. Enkripsi AES-256-GCM (AEAD): Data dienkripsi menggunakan mode Galois/Counter Mode dengan 96-bit Initialization Vector (IV) dan 128-bit Authentication Tag. Hal ini menjamin aspek kerahasiaan (confidentiality) sekaligus keutuhan anti-manipulasi (integrity/tamper-proof)."
    )

    doc.add_heading(level=2).add_run("5.2 Hashing Kredensial Menggunakan Argon2id")
    doc.add_paragraph(
        "Mindora tidak menggunakan algoritma hash rentan (MD5/SHA) ataupun Bcrypt standar, melainkan mengadopsi Argon2id (Pemenang Password Hashing Competition - PHC). Parameter yang dikonfigurasikan:\n"
        "• Memori: 64 MB (memory = 65,536 KB)\n"
        "• Waktu Komputasi: 4 putaran (time = 4)\n"
        "• Thread: 1 thread paralel\n\n"
        "Argon2id menggabungkan ketahanan terhadap serangan akses memori (Argon2d) dan serangan saluran samping / side-channel cache timing (Argon2i), menjadikannya sangat kebal terhadap serangan brute-force berbasis kartu grafis (GPU/ASIC/FPGA)."
    )

    doc.add_heading(level=2).add_run("5.3 Penyamaran Data (Profile Masking) & Gerbang Re-Autentikasi")
    doc.add_paragraph(
        "Data identitas sensitif secara default disamarkan (masked) baik pada dashboard pengguna maupun panel admin (contoh: +62 812-****-4567). Untuk melihat plaintext asli, pengguna atau admin wajib melewati gerbang Re-Authentication (memasukkan kembali kata sandi akun). Proses dekripsi hanya terjadi sementara di memori dan tidak pernah diekspos secara permanen."
    )

    doc.add_heading(level=2).add_run("5.4 Role-Based Access Control (RBAC) & Audit Trail Terperinci")
    doc.add_paragraph(
        "• RBAC Berjenjang: Pemisahan hak akses yang ketat antara Pengguna (User), Pengelola (Admin), dan Super Admin yang diverifikasi pada lapisan middleware Laravel (EnsureUserHasRole).\n"
        "• Security Audit Logging: Setiap aktivitas krusial (pembukaan data terselubung, pembaruan bank soal, upaya login, ekspor data) dicatat secara kekal pada tabel audit_logs lengkap dengan alamat IP, user-agent, dan stempel waktu ISO 8601."
    )

    # -------------------------------------------------------------
    # BAB 6: MATRIKS KOMPREHENSIF METODE & SPESIFIKASI SISTEM
    # -------------------------------------------------------------
    h6 = doc.add_heading(level=1)
    run_h6 = h6.add_run("6. Matriks Komprehensif Seluruh Metode & Spesifikasi")
    run_h6.font.color.rgb = RGBColor(15, 23, 42)
    run_h6.font.size = Pt(15)

    doc.add_paragraph(
        "Tabel berikut merangkum seluruh metode, algoritma, parameter teknis, dan fungsi utama yang bekerja secara harmonis di dalam platform Mindora:"
    )

    master_matrix = [
        ["Psikometri Kognitif", "Cattell-Horn-Carroll (CHC) Theory", "3 Sub-tes: Logika, Numerik, Verbal", "Memetakan kapasitas pemrosesan intelektual & pemecahan masalah."],
        ["Psikometri Karakter", "Big Five / Five-Factor Model (OCEAN)", "5 Dimensi, Skala Likert 5-poin, Inversi Butir", "Mengukur dinamika kepribadian, gaya komunikasi, dan perilaku kerja."],
        ["Telemetri Perilaku", "Reaction Time & Consistency Analysis", "Satuan milidetik per butir soal", "Mendeteksi konsistensi responden dan gaya kognitif (reflektif/impulsif)."],
        ["Normalisasi Data", "StandardScaler (Z-Score)", "Mean μ=0, Simpangan Baku σ=1", "Menghilangkan bias skala antara waktu respons dan skor psikometri."],
        ["Unsupervised ML", "K-Means Clustering", "k=4 Klaster, Jarak Euclidean, Inisialisasi n=15", "Mengelompokkan pola karakter ke dalam 4 klaster alami (Silhouette: 0.1696)."],
        ["Supervised ML", "Random Forest Classifier", "120 Estimator, Max Depth 7, Balanced Weight", "Memprediksi arketipe profil kerja dominan dengan Akurasi 91.75%."],
        ["Explainable AI (XAI)", "TreeSHAP (Shapley Explanations)", "Nilai Shapley Teori Permainan Koperasi", "Menghitung kontribusi persentase pendorong skor secara transparan."],
        ["Generative AI", "Google Gemini Live Engine", "gemini-3.6-flash, Alokasi 4096 Tokens", "Memberikan konsultasi karir & pengembangan diri terpersonalisasi."],
        ["Enkripsi Data Sensitif", "Envelope Encryption (AES-256-GCM)", "HKDF SHA-256 KEK + Random DEK 256-bit", "Mengamankan data profil sensitif dengan jaminan anti-tamper (AEAD)."],
        ["Hashing Kata Sandi", "Argon2id Algorithm", "Memori 64MB, Waktu 4 Putaran, 1 Thread", "Melindungi kredensial terhadap serangan brute-force GPU/ASIC."],
        ["Proteksi Privasi", "Zero-PII Feature Vector", "Pseudo-Anonymized Hash ID", "Menjamin model ML dan AI sama sekali tidak mengakses identitas pribadi."],
        ["Tata Kelola Akses", "RBAC & Re-Authentication Gate", "Middleware terproteksi + Audit Trail Log", "Menjaga integritas akses sistem dan kepatuhan audit keamanan regulasi."]
    ]
    create_styled_table(doc, ["Komponen Sistem", "Nama Metode / Algoritma", "Parameter / Spesifikasi", "Fungsi Utama & Output"], master_matrix, [Inches(1.5), Inches(1.8), Inches(1.6), Inches(1.6)])

    # -------------------------------------------------------------
    # DAFTAR PUSTAKA & REFERENSI
    # -------------------------------------------------------------
    doc.add_heading(level=1).add_run("Daftar Pustaka & Referensi Ilmiah")
    references = [
        "Breiman, L. (2001). Random Forests. Machine Learning, 45(1), 5-32.",
        "Goldberg, L. R. (1990). An alternative 'description of personality': The Big-Five factor structure. Journal of Personality and Social Psychology, 59(6), 1216-1229.",
        "Lundberg, S. M., & Lee, S. I. (2017). A Unified Approach to Interpreting Model Predictions. Advances in Neural Information Processing Systems (NeurIPS 2017), 30.",
        "McCrae, R. R., & Costa, P. T. (1992). An introduction to the five-factor model and its applications. Journal of Personality, 60(2), 175-215.",
        "Krawczyk, H., & Eronen, P. (2010). HMAC-based Extract-and-Expand Key Derivation Function (HKDF). RFC 5869, Internet Engineering Task Force (IETF).",
        "Dworkin, M. (2007). Recommendation for Block Cipher Modes of Operation: Galois/Counter Mode (GCM) and GMAC. NIST Special Publication 800-38D, National Institute of Standards and Technology.",
        "Biryukov, A., Dinu, D., & Khovratovich, A. (2016). Argon2: the memory-hard function for password hashing and other applications. Password Hashing Competition (PHC)."
    ]
    for ref in references:
        p_ref = doc.add_paragraph()
        p_ref.paragraph_format.left_indent = Inches(0.4)
        p_ref.paragraph_format.first_line_indent = Inches(-0.4)
        p_ref.paragraph_format.space_after = Pt(4)
        run_ref = p_ref.add_run(ref)
        run_ref.font.size = Pt(8.5)
        run_ref.font.color.rgb = RGBColor(71, 85, 105)

    # Save Document
    doc.save(str(OUTPUT_FILE))
    print(f"Document successfully created at: {OUTPUT_FILE}")

if __name__ == "__main__":
    build_word_document()
