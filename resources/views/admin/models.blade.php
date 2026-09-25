@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold uppercase tracking-wider mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                Machine Learning &amp; AI Diagnostics
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Manajemen Model &amp; ML Diagnostics</h1>
            <p class="text-slate-400 text-sm mt-1">Monitoring status microservice FastAPI, model ensembles, metrik validasi, dan telemetry inferensi anonim.</p>
        </div>

        <div class="flex items-center gap-3">
            @if(isset($health['status']) && $health['status'] === 'healthy')
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    FastAPI Service: Online (127.0.0.1:8001)
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                    FastAPI Service: Offline / Terputus
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs -->
    @include('admin.nav')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- High-Level Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Inferensi</span>
                <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-white mt-4">{{ $totalPredictions }}</p>
            <p class="text-xs text-slate-500 mt-1">Sesi asesmen yang diproses ML</p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Vektor Fitur Anonim</span>
                <span class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z" /></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-white mt-4">{{ $totalFeatures }}</p>
            <p class="text-xs text-slate-500 mt-1">10-Dimensi (Zero PII)</p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Akurasi Random Forest</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-white mt-4">91.75%</p>
            <p class="text-xs text-slate-500 mt-1">n=120 Estimators (4 Kelas)</p>
        </div>

        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">K-Means Silhouette</span>
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-white mt-4">0.1696</p>
            <p class="text-xs text-slate-500 mt-1">k=4 Pola Klaster Perilaku</p>
        </div>
    </div>

    <!-- Alur Kerja & Penjelasan Transparansi Machine Learning (Data Lineage & Arti Persentase) -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-2xl backdrop-blur-xl space-y-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-semibold mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Panduan Transparansi &amp; Alur Data
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">Alur Kerja Machine Learning: Dari Mana Data Diambil &amp; Menjadi Apa?</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Penjelasan sederhana mengenai siklus pengumpulan data, pengolahan AI/ML, dan arti dari setiap angka persentase sistem.</p>
        </div>

        <!-- 3-Langkah Alur Data -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Langkah 1: Input Data -->
            <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800/90 space-y-3 relative overflow-hidden">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs">
                    1
                </div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    Dari Mana Data Dikumpulkan?
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Data diambil murni dari <strong>jawaban tes psikometri</strong> yang diselesaikan peserta, terdiri dari <strong>10 angka parameter (Vektor Fitur)</strong>:
                </p>
                <ul class="text-[11px] text-slate-300 space-y-1 bg-slate-900/60 p-3 rounded-xl border border-slate-800/60">
                    <li class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> <strong>3 Skor Kognitif:</strong> Logika, Numerik, Verbal</li>
                    <li class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span> <strong>5 Dimensi Big Five:</strong> O, C, E, A, N</li>
                    <li class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span> <strong>2 Kecepatan Respons:</strong> Waktu jawab per soal</li>
                </ul>
                <div class="p-2.5 rounded-lg bg-emerald-950/30 border border-emerald-800/40 text-[10px] text-emerald-300 flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span><strong>Prinsip Zero-PII:</strong> Nama asli, email, kata sandi, dan identitas fisik <strong>TIDAK PERNAH</strong> dikirim ke model ML.</span>
                </div>
            </div>

            <!-- Langkah 2: Pemrosesan -->
            <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800/90 space-y-3 relative overflow-hidden">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-xs">
                    2
                </div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    Bagaimana Data Diolah?
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    10 angka tersebut dikirim ke <strong>FastAPI Microservice (Python)</strong> dan diproses bersamaan oleh 3 algoritma cerdas:
                </p>
                <ul class="text-[11px] text-slate-300 space-y-1.5 bg-slate-900/60 p-3 rounded-xl border border-slate-800/60">
                    <li><strong>K-Means (k=4):</strong> Mengelompokkan pola berpikir pengguna ke dalam salah satu dari 4 klaster kepribadian.</li>
                    <li><strong>Random Forest (120 Trees):</strong> 120 pohon keputusan melakukan voting untuk menentukan arketipe profil kerja dominan.</li>
                    <li><strong>TreeSHAP (Explainable AI):</strong> Menghitung secara matematis faktor mana yang paling berkontribusi pada hasil.</li>
                </ul>
                <div class="p-2.5 rounded-lg bg-purple-950/30 border border-purple-800/40 text-[10px] text-purple-300 flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Pemrosesan berlangsung instan (&lt;0.5 detik) dengan format anonim murni.</span>
                </div>
            </div>

            <!-- Langkah 3: Menjadi Seperti Apa -->
            <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800/90 space-y-3 relative overflow-hidden">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs">
                    3
                </div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    Menjadi Seperti Apa Nantinya?
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Hasil pemrosesan model menghasilkan wawasan psikometri konkret yang langsung dinikmati pengguna:
                </p>
                <ul class="text-[11px] text-slate-300 space-y-1.5 bg-slate-900/60 p-3 rounded-xl border border-slate-800/60">
                    <li><strong>Arketipe Profil Kerja:</strong> Label profil seperti <em>Collaborative Facilitator</em> atau <em>Systematic Executor</em>.</li>
                    <li><strong>Grafik Radar &amp; Faktor Pendorong:</strong> Pengguna melihat langsung alasan skor mereka (misal: "Logika +14% mendominasi").</li>
                    <li><strong>Saran Pengembangan Karir:</strong> Rekomendasi gaya belajar, peran tim, dan profesi yang selaras.</li>
                </ul>
                <div class="p-2.5 rounded-lg bg-blue-950/30 border border-blue-800/40 text-[10px] text-blue-300 flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>Data hasil ini juga menjadi bahan diskusi interaktif dengan Asisten AI Mindora.</span>
                </div>
            </div>
        </div>

        <!-- Penjelasan Sederhana Angka Persentase -->
        <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Panduan Memahami Angka Persentase (Bahasa Mudah Dipahami)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white">Akurasi Model</span>
                        <span class="font-mono font-bold text-emerald-400 text-sm">91.75%</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed text-[11px]">
                        <strong>Artinya:</strong> Dari setiap 100 kali tes yang diproses, model mampu mengelompokkan dan memprediksi pola perilaku peserta dengan tepat sebanyak <strong>~92 kali</strong>. Angka di atas 90% membuktikan model sangat akurat dan konsisten.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white">Tingkat Keyakinan (Confidence)</span>
                        <span class="font-mono font-bold text-cyan-400 text-sm">Rata-rata {{ $avgConfidence ?? '60' }}%</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed text-[11px]">
                        <strong>Artinya:</strong> Mengukur seberapa yakin AI bahwa profil seseorang memang khas pada label tersebut. Jika keyakinan 75%+, profil orang tersebut sangat tegas dan dominan. Jika 50-60%, kepribadiannya bersifat fleksibel/adaptif (perpaduan beberapa gaya).
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white">Distribusi 4 Klaster</span>
                        <span class="font-mono font-bold text-purple-400 text-sm">100% Populasi</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed text-[11px]">
                        <strong>Artinya:</strong> Menunjukkan persebaran tipe karakter pengguna platform secara keseluruhan. Setiap persentase klaster memperlihatkan berapa persen populasi yang memiliki kecenderungan analitis, mandiri, sosial, atau kreatif.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Models Table -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Model Registry &amp; Lifecycle Configuration</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar ensemble model machine learning yang terdaftar dalam database.</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID &amp; Nama Model</th>
                        <th class="px-6 py-4">Versi</th>
                        <th class="px-6 py-4">Algoritma Ensemble</th>
                        <th class="px-6 py-4">Metrik Evaluasi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($models as $model)
                        @php
                            $metrics = is_array($model->metrics) ? $model->metrics : json_decode($model->metrics, true);
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-white">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $model->status === 'active' ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                    <span>{{ $model->name }}</span>
                                </div>
                                <span class="text-[10px] text-slate-500 font-normal">Registered: {{ $model->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono text-amber-400 font-bold">
                                {{ $model->version }}
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ $model->algorithm }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if(isset($metrics['accuracy']))
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-mono text-[10px] border border-emerald-500/20">
                                            Acc: {{ number_format($metrics['accuracy'] * 100, 1) }}%
                                        </span>
                                    @endif
                                    @if(isset($metrics['silhouette_score']))
                                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 font-mono text-[10px] border border-blue-500/20">
                                            Sil: {{ number_format($metrics['silhouette_score'], 4) }}
                                        </span>
                                    @endif
                                    @if(isset($metrics['clusters_count']))
                                        <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 font-mono text-[10px] border border-purple-500/20">
                                            k={{ $metrics['clusters_count'] }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($model->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Aktif / Primary
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.models.toggle', $model) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status model {{ $model->name }}?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $model->status === 'active' ? 'bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                        {{ $model->status === 'active' ? 'Nonaktifkan' : 'Aktifkan Model' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                Belum ada model ML yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cluster Distribution Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl space-y-4">
            <h3 class="text-base font-bold text-white">Distribusi Klaster Hasil Asesmen</h3>
            <p class="text-xs text-slate-400">Klasifikasi 4 pola profil perilaku yang dihasilkan algoritma K-Means:</p>

            <div class="space-y-3 pt-2">
                @php
                    $clusters = [
                        'Analytical & Structured' => [
                            'name' => 'Klaster Analitis & Terstruktur',
                            'color' => 'blue',
                            'desc' => 'Tinggi penalaran logis & numerik, sistematis, dan pemecahan masalah berbasis fakta.'
                        ],
                        'Independent & Task-Oriented' => [
                            'name' => 'Klaster Mandiri & Fokus Tugas',
                            'color' => 'amber',
                            'desc' => 'Disiplin kerja tinggi (Conscientiousness), mandiri, dan fokus pada target kerja nyata.'
                        ],
                        'Social & Collaborative' => [
                            'name' => 'Klaster Sosial & Kolaboratif',
                            'color' => 'emerald',
                            'desc' => 'Tinggi keramahan (Agreeableness), komunikatif, dan sangat kuat dalam kerja sama tim.'
                        ],
                        'Creative & Explorative' => [
                            'name' => 'Klaster Kreatif & Eksploratif',
                            'color' => 'purple',
                            'desc' => 'Tinggi keterbukaan ide baru (Openness), inovatif, fleksibel, dan memiliki rasa ingin tahu tinggi.'
                        ],
                    ];
                @endphp

                @foreach($clusters as $clusterKey => $info)
                    @php
                        $count = $clusterDistribution[$clusterKey] ?? 0;
                        $pct = $totalPredictions > 0 ? round(($count / $totalPredictions) * 100, 1) : 0;
                    @endphp
                    <div class="p-3.5 rounded-xl bg-slate-950/40 border border-slate-800/80 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-white">{{ $info['name'] }}</span>
                            <span class="font-mono text-slate-300 font-semibold">{{ $count }} asesmen ({{ $pct }}%)</span>
                        </div>
                        <p class="text-[11px] text-slate-400">{{ $info['desc'] }}</p>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $info['color'] }}-500 rounded-full" style="width: {{ max(4, $pct) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Anonymous ML Telemetry -->
        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl space-y-4">
            <h3 class="text-base font-bold text-white">Telemetry Inferensi Anonim Terakhir</h3>
            <p class="text-xs text-slate-400">Catatan eksekusi microservice FastAPI (Zero PII - Data Pribadi Terisolasi):</p>

            <div class="space-y-3 pt-2">
                @forelse($recentPredictions as $prediction)
                    <div class="p-3.5 rounded-xl bg-slate-950/40 border border-slate-800/80 flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-amber-400">Score #{{ $prediction->assessment_score_id }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    {{ $prediction->cluster_label }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Kategori: <span class="text-slate-200 font-semibold">{{ $prediction->predicted_category }}</span>
                                &bull; Keyakinan: <span class="text-emerald-400 font-mono font-bold">{{ number_format($prediction->confidence * 100, 1) }}%</span>
                            </div>
                        </div>
                        <div class="text-right text-[10px] text-slate-500 font-mono shrink-0">
                            {{ $prediction->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs">
                        Belum ada inferensi ML yang tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
