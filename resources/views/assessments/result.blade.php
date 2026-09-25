@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10" x-data="{
    initCharts() {
        @if($score->assessment_type === 'personality' && $personalityScore)
            // Big Five Radar Chart
            const ctxRadar = document.getElementById('personalityRadarChart');
            if (ctxRadar) {
                new Chart(ctxRadar, {
                    type: 'radar',
                    data: {
                        labels: ['Openness', 'Conscientiousness', 'Extraversion', 'Agreeableness', 'Neuroticism'],
                        datasets: [{
                            label: 'Profil Big Five (%)',
                            data: [
                                {{ $personalityScore->openness }},
                                {{ $personalityScore->conscientiousness }},
                                {{ $personalityScore->extraversion }},
                                {{ $personalityScore->agreeableness }},
                                {{ $personalityScore->neuroticism }}
                            ],
                            backgroundColor: 'rgba(97, 114, 243, 0.25)',
                            borderColor: '#6172f3',
                            pointBackgroundColor: '#818cf8',
                            pointBorderColor: '#ffffff',
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#6172f3',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: { color: 'rgba(51, 65, 85, 0.5)' },
                                grid: { color: 'rgba(51, 65, 85, 0.5)' },
                                pointLabels: {
                                    color: '#cbd5e1',
                                    font: { size: 12, family: 'Plus Jakarta Sans', weight: 'bold' }
                                },
                                ticks: {
                                    backdropColor: 'transparent',
                                    color: '#64748b',
                                    stepSize: 20,
                                },
                                suggestedMin: 0,
                                suggestedMax: 100,
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        @endif

        @if($score->assessment_type === 'cognitive' && $cognitiveScore)
            // Cognitive Ability Bar Chart
            const ctxBar = document.getElementById('cognitiveBarChart');
            if (ctxBar) {
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: ['Penalaran Logis', 'Penalaran Numerik', 'Penalaran Verbal'],
                        datasets: [{
                            label: 'Skor Kemampuan Kognitif (%)',
                            data: [
                                {{ $cognitiveScore->logical_score }},
                                {{ $cognitiveScore->numerical_score }},
                                {{ $cognitiveScore->verbal_score }}
                            ],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(168, 85, 247, 0.8)',
                                'rgba(16, 185, 129, 0.8)'
                            ],
                            borderColor: [
                                '#3b82f6',
                                '#a855f7',
                                '#10b981'
                            ],
                            borderWidth: 1,
                            borderRadius: 10,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: 'rgba(51, 65, 85, 0.4)' },
                                ticks: { color: '#94a3b8' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#cbd5e1', font: { weight: 'bold' } }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        @endif
    }
}" x-init="initCharts()">

    <!-- Header & Summary Card -->
    <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-slate-900 via-brand-950/60 to-slate-900 border border-slate-800 shadow-2xl space-y-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-950 border border-brand-800 text-brand-300 text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-400"></span>
                    {{ $score->assessment_type === 'cognitive' ? 'Cognitive Assessment Score' : 'Big Five Personality Profile' }}
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white">{{ $assessment->title }}</h1>
                <p class="text-xs text-slate-400">
                    Selesai pada {{ $score->created_at->format('d M Y, H:i') }} &bull; Sesi #{{ $session->id }} (Versi {{ $assessment->version }})
                </p>
            </div>

            <!-- Overall Score Badge -->
            <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800 text-center min-w-[170px] space-y-1">
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Skor Asesmen</span>
                <div class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-300 font-mono">
                    {{ $score->overall_score }}%
                </div>
                @if($cognitiveScore)
                    <span class="text-[11px] text-emerald-400 block font-medium">Persentil ~{{ $cognitiveScore->percentile }}</span>
                @elseif($personalityScore)
                    <span class="text-[11px] text-purple-400 block font-medium">Konsistensi: {{ $personalityScore->consistency_score }}%</span>
                @endif
            </div>
        </div>

        <!-- Archetype & Narrative Insight -->
        <div class="p-6 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Profil Kecenderungan Utama: <span class="text-white ml-1 font-extrabold">{{ $score->archetype_label }}</span>
            </div>
            <p class="text-sm text-slate-300 leading-relaxed font-sans">
                {{ $score->summary_text }}
            </p>
        </div>
    </div>

    <!-- Visualization Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Chart Column -->
        <div class="lg:col-span-7 p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h3 class="font-bold text-white text-base">
                    {{ $score->assessment_type === 'personality' ? 'Peta Radar Dimensi Big Five' : 'Grafik Distribusi Kemampuan Kognitif' }}
                </h3>
                <span class="text-xs text-slate-400 font-mono">Normalized (0 - 100%)</span>
            </div>

            <div class="h-80 relative flex items-center justify-center">
                @if($score->assessment_type === 'personality')
                    <canvas id="personalityRadarChart"></canvas>
                @else
                    <canvas id="cognitiveBarChart"></canvas>
                @endif
            </div>

            <!-- Dimension Breakdown Metrics -->
            <div class="pt-4 border-t border-slate-800 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                @if($cognitiveScore)
                    <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[11px]">Penalaran Logis</span>
                        <span class="font-mono font-bold text-blue-400 text-sm">{{ $cognitiveScore->logical_score }}%</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[11px]">Penalaran Numerik</span>
                        <span class="font-mono font-bold text-purple-400 text-sm">{{ $cognitiveScore->numerical_score }}%</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[11px]">Penalaran Verbal</span>
                        <span class="font-mono font-bold text-emerald-400 text-sm">{{ $cognitiveScore->verbal_score }}%</span>
                    </div>
                @elseif($personalityScore)
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px]">Openness</span>
                        <span class="font-mono font-bold text-brand-300">{{ $personalityScore->openness }}%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px]">Conscientiousness</span>
                        <span class="font-mono font-bold text-brand-300">{{ $personalityScore->conscientiousness }}%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px]">Extraversion</span>
                        <span class="font-mono font-bold text-brand-300">{{ $personalityScore->extraversion }}%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px]">Agreeableness</span>
                        <span class="font-mono font-bold text-brand-300">{{ $personalityScore->agreeableness }}%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px]">Neuroticism</span>
                        <span class="font-mono font-bold text-brand-300">{{ $personalityScore->neuroticism }}%</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Strengths & Growth Areas Column -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Strengths -->
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
                <h3 class="font-bold text-emerald-400 text-sm flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Kekuatan Menonjol (Key Strengths)
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-300">
                    @foreach($score->strengths ?? [] as $st)
                        <li class="flex items-start gap-2.5 p-2 rounded-xl bg-emerald-950/30 border border-emerald-900/30">
                            <span class="text-emerald-400 font-bold">&bull;</span>
                            <span class="leading-relaxed">{{ $st }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Development Areas -->
            <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
                <h3 class="font-bold text-amber-400 text-sm flex items-center gap-2 uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Area Pengembangan Diri (Development Areas)
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-300">
                    @foreach($score->development_areas ?? [] as $da)
                        <li class="flex items-start gap-2.5 p-2 rounded-xl bg-amber-950/30 border border-amber-900/30">
                            <span class="text-amber-400 font-bold">&bull;</span>
                            <span class="leading-relaxed">{{ $da }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Machine Learning Archetype & SHAP Explainability Section -->
    @if($mlPrediction)
        <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl space-y-8 relative overflow-hidden">
            <!-- Ambient Glow Background -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-brand-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-800 relative z-10">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950/80 border border-purple-800/80 text-purple-300 text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                        Machine Learning &amp; Explainable AI (SHAP)
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white">Analisis Pola Machine Learning &amp; Penjelasan SHAP</h3>
                    <p class="text-xs text-slate-400">
                        Didukung oleh Python FastAPI microservice &bull; Klasifikasi Random Forest, K-Means Clustering, dan Atribusi Fitur SHAP.
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-950/80 border border-emerald-800/80 text-emerald-400 text-xs font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Model {{ $mlPrediction->model?->version ?? 'v1.0.0' }} Active
                    </span>
                </div>
            </div>

            <!-- Cluster & Classifier Result Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <!-- K-Means Cluster Card -->
                <div class="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 hover:border-slate-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            K-Means Behavioral Cluster
                        </span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-blue-950 text-blue-300 border border-blue-800">
                            Cluster #{{ $mlPrediction->cluster_id ?? 0 }}
                        </span>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-xl font-extrabold text-blue-300">{{ $mlPrediction->cluster_label ?? 'Analytical & Structured' }}</h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Berdasarkan vektor respon Anda dalam ruang 10 dimensi normatif, profil perilaku Anda terelasi paling dekat dengan kluster ini.
                        </p>
                    </div>
                </div>

                <!-- Random Forest Prediction Card -->
                <div class="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 hover:border-slate-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            Random Forest Profile Category
                        </span>
                        <span class="text-xs font-mono font-bold text-purple-300">
                            {{ $mlPrediction->confidence ?? 90 }}% Confidence
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xl font-extrabold text-purple-300">{{ $mlPrediction->predicted_category ?? 'Systematic Executor' }}</h4>
                        <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border border-slate-800">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-400 h-2 rounded-full" style="width: {{ $mlPrediction->confidence ?? 85 }}%"></div>
                        </div>
                        <p class="text-xs text-slate-400">
                            Tingkat keyakinan probabilitas klasifikasi pohon keputusan (ensemble dari 120 decision trees).
                        </p>
                    </div>
                </div>
            </div>

            <!-- SHAP Explainability Breakdown -->
            <div class="space-y-4 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-white text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            SHAP Feature Importance (Atribusi Fitur Terbesar)
                        </h4>
                        <p class="text-xs text-slate-400">
                            Menjelaskan bagaimana setiap dimensi kognitif dan kepribadian berkontribusi terhadap hasil klasifikasi model.
                        </p>
                    </div>
                    <span class="hidden sm:inline-flex text-[11px] font-mono text-slate-400 px-2 py-1 rounded bg-slate-950 border border-slate-800">
                        Method: TreeSHAP
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($mlPrediction->shap_values ?? [] as $shap)
                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-bold text-slate-200">
                                    {{ $shap['feature_name_id'] ?? ucfirst($shap['feature']) }}
                                </span>
                                @if(($shap['direction'] ?? '') === 'positive')
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-950 text-emerald-400 border border-emerald-800">
                                        + Positif
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-950 text-indigo-300 border border-indigo-800">
                                        &plusmn; Penyeimbang
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 text-[11px]">Skor Respon: <strong class="text-white">{{ $shap['raw_value'] ?? '-' }}%</strong></span>
                                <span class="text-slate-400 text-[11px] font-mono">SHAP: {{ sprintf('%+.3f', $shap['impact_score'] ?? 0) }}</span>
                            </div>

                            <div class="w-full bg-slate-900 rounded-full h-1.5 overflow-hidden">
                                @php
                                    $barWidth = min(100, max(15, abs(($shap['impact_score'] ?? 0.1) * 300)));
                                @endphp
                                <div class="{{ ($shap['direction'] ?? '') === 'positive' ? 'bg-emerald-500' : 'bg-indigo-400' }} h-1.5 rounded-full" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-4 rounded-xl bg-slate-950 text-slate-400 text-xs text-center">
                            Data atribusi fitur SHAP sedang dikalkulasi.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Non-Clinical Transparency Disclaimer -->
            <div class="p-4 rounded-2xl bg-amber-950/30 border border-amber-900/50 flex items-start gap-3 relative z-10">
                <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div class="space-y-0.5 text-xs text-amber-200/90 leading-relaxed">
                    <span class="font-bold block text-amber-300">Pemberitahuan Model Demonstrasi &amp; Non-Klinis</span>
                    Hasil analisis Machine Learning ini dihasilkan melalui model K-Means & Random Forest yang dilatih menggunakan dataset normatif sintetis untuk tujuan eksplorasi wawasan mandiri dan demonstrasi explainable AI. Hasil ini bukan merupakan diagnosis medis, psikiatri, ataupun evaluasi seleksi deterministik.
                </div>
            </div>
        </div>
    @endif

    <!-- Career & Growth Exploration Recommendations -->
    @if($recommendations->isNotEmpty())
        <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-6">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Rekomendasi Eksplorasi Bidang & Peran (Recommendation Engine)
                </h3>
                <p class="text-xs text-slate-400">
                    Dihasilkan berdasarkan sinergi kecenderungan kognitif dan profil kepribadian Anda (disajikan sebagai bahan pertimbangan eksplorasi mandiri, bukan klaim mutlak).
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($recommendations as $rec)
                    <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
                        <div class="space-y-1">
                            <h4 class="font-bold text-brand-300 text-sm">{{ $rec->title }}</h4>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $rec->description }}</p>
                        </div>

                        <div class="space-y-2">
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Bidang yang Patut Dieksplorasi:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($rec->suggested_areas ?? [] as $area)
                                    <span class="px-3 py-1 rounded-lg bg-slate-900 text-slate-200 border border-slate-700/80 text-xs font-medium">
                                        {{ $area }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="p-6 rounded-3xl bg-slate-900/60 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="{{ route('assessments.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold text-center transition-colors">
            &larr; Kembali ke Katalog Tes
        </a>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('ai.conversation', $score->id) }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-semibold text-center transition-all shadow-lg shadow-purple-600/25 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Konsultasi AI Mindora
            </a>
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold text-center transition-colors">
                Buka Dashboard
            </a>
        </div>
    </div>
</div>
@endsection