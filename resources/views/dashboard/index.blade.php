@extends('layouts.app')
@section('title', 'Dashboard Pengguna')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6 sm:space-y-8">

    <!-- Welcome Hero Card -->
    <div class="p-6 sm:p-10 rounded-2xl sm:rounded-3xl relative overflow-hidden"
         style="background: linear-gradient(135deg, rgba(30, 27, 94, 0.75) 0%, rgba(13, 11, 51, 0.95) 60%, rgba(124, 58, 237, 0.2) 100%); border: 1px solid rgba(124, 58, 237, 0.25); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.45);">
        
        {{-- Subtle radial decorative glow --}}
        <div class="absolute right-0 top-0 w-72 sm:w-96 h-72 sm:h-96 pointer-events-none rounded-full" style="background: radial-gradient(circle, rgba(56, 189, 248, 0.12) 0%, transparent 70%);"></div>

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-5 sm:gap-6 relative z-10">
            <div class="space-y-3 w-full md:w-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(124, 58, 237, 0.18); border: 1px solid rgba(124, 58, 237, 0.35); color: #c4b5fd;">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background: #38bdf8; box-shadow: 0 0 8px #38bdf8;"></span>
                    <span>Instrumen Psikometri Siap Diakses</span>
                </div>

                <div class="flex items-center gap-3.5 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center text-lg sm:text-xl font-extrabold text-white shadow-lg flex-shrink-0" style="background: linear-gradient(135deg, #7c3aed 0%, #38bdf8 100%);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="text-xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            Selamat Datang, <span class="text-mindora">{{ $user->name }}</span>!
                        </h1>
                        <div class="text-xs sm:text-sm text-slate-400 mt-1 flex flex-wrap items-center gap-2">
                            <span>Akun: <span class="font-mono text-slate-300">{{ $user->masked_email }}</span></span>
                            <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-md" style="background: rgba(16, 185, 129, 0.12); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.25);">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                Masked PII
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Header Action -->
            <div class="flex items-center gap-2.5 w-full md:w-auto pt-2 md:pt-0">
                <a href="{{ route('profile.show') }}" class="w-full md:w-auto px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-2 touch-feedback" style="background: rgba(255, 255, 255, 0.06); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15);" onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    <span>Profil &amp; Keamanan Data</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Status Strip (Mobile-friendly 4 Metric Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-3.5 sm:p-4 space-y-1 touch-feedback">
            <div class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                <span>Asesmen</span>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-white">2 Instrumen</div>
            <div class="text-[10px] text-cyan-300/80">Kognitif &amp; Big Five</div>
        </div>

        <div class="card p-3.5 sm:p-4 space-y-1 touch-feedback">
            <div class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                <span>Enkripsi</span>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-white">Argon2id</div>
            <div class="text-[10px] text-purple-300/80">Privasi Terlindungi</div>
        </div>

        <div class="card p-3.5 sm:p-4 space-y-1 touch-feedback">
            <div class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>AI Engine</span>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-white">Gemini 2.5</div>
            <div class="text-[10px] text-emerald-300/80">Siap Konsultasi</div>
        </div>

        <div class="card p-3.5 sm:p-4 space-y-1 touch-feedback">
            <div class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                <span>Zero-PII</span>
            </div>
            <div class="text-base sm:text-lg font-extrabold text-white">Aktif</div>
            <div class="text-[10px] text-amber-300/80">Anonim &amp; Aman</div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                <span>Pilihan Asesmen &amp; Menu Utama</span>
            </h2>
            <a href="{{ route('assessments.index') }}" class="text-xs font-semibold text-purple-300 hover:text-purple-200 transition-colors flex items-center gap-1 touch-feedback">
                <span>Katalog Lengkap</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

            <!-- Card 1: Cognitive Test -->
            <div class="card p-5 sm:p-6 space-y-4 flex flex-col justify-between group touch-feedback">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105 shadow-md"
                             style="background: linear-gradient(135deg, rgba(56, 189, 248, 0.2) 0%, rgba(14, 165, 233, 0.05) 100%); border: 1px solid rgba(56, 189, 248, 0.35); color: #38bdf8;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/25">
                            30 Menit
                        </span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-cyan-400">Penalaran &amp; Logika</span>
                        <h3 class="font-bold text-white text-base sm:text-lg group-hover:text-cyan-300 transition-colors">Tes Kognitif</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Uji kemampuan penalaran logis, pola numerik, dan verbal secara terstruktur.
                        </p>
                    </div>
                </div>
                <a href="{{ $cognitiveAssessment ? route('assessments.instructions', $cognitiveAssessment) : route('assessments.index') }}"
                   class="block text-center w-full py-2.5 px-4 rounded-xl font-semibold text-xs transition-all duration-200 shadow-md touch-feedback"
                   style="background: rgba(56, 189, 248, 0.12); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.3);"
                   onmouseover="this.style.background='rgba(56, 189, 248, 0.25)'; this.style.color='#ffffff';"
                   onmouseout="this.style.background='rgba(56, 189, 248, 0.12)'; this.style.color='#7dd3fc';">
                    Mulai Tes Kognitif &rarr;
                </a>
            </div>

            <!-- Card 2: Personality Test -->
            <div class="card p-5 sm:p-6 space-y-4 flex flex-col justify-between group touch-feedback">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105 shadow-md"
                             style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.25) 0%, rgba(109, 40, 217, 0.05) 100%); border: 1px solid rgba(124, 58, 237, 0.35); color: #c4b5fd;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-purple-500/15 text-purple-300 border border-purple-500/30">
                            15 Menit
                        </span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-purple-400">Dimensi Karakter</span>
                        <h3 class="font-bold text-white text-base sm:text-lg group-hover:text-purple-300 transition-colors">Tes Kepribadian</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Model Big Five (O-C-E-A-N) untuk memetakan dinamika perilaku kerja Anda.
                        </p>
                    </div>
                </div>
                <a href="{{ $personalityAssessment ? route('assessments.instructions', $personalityAssessment) : route('assessments.index') }}"
                   class="block text-center w-full py-2.5 px-4 rounded-xl font-semibold text-xs transition-all duration-200 shadow-md touch-feedback"
                   style="background: rgba(124, 58, 237, 0.15); color: #d8b4fe; border: 1px solid rgba(124, 58, 237, 0.35);"
                   onmouseover="this.style.background='rgba(124, 58, 237, 0.3)'; this.style.color='#ffffff';"
                   onmouseout="this.style.background='rgba(124, 58, 237, 0.15)'; this.style.color='#d8b4fe';">
                    Mulai Tes Big Five &rarr;
                </a>
            </div>

            <!-- Card 3: Results -->
            <div class="card p-5 sm:p-6 space-y-4 flex flex-col justify-between group touch-feedback">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105 shadow-md"
                             style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.05) 100%); border: 1px solid rgba(16, 185, 129, 0.35); color: #6ee7b7;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/25">
                            Radar &amp; ML
                        </span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-emerald-400">Analitik Mandiri</span>
                        <h3 class="font-bold text-white text-base sm:text-lg group-hover:text-emerald-300 transition-colors">Hasil &amp; Analisis</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Grafik radar kepribadian, persentil populasi, dan bukti skor SHAP.
                        </p>
                    </div>
                </div>
                <a href="{{ route('assessments.index') }}"
                   class="block text-center w-full py-2.5 px-4 rounded-xl font-semibold text-xs transition-all duration-200 shadow-md touch-feedback"
                   style="background: rgba(16, 185, 129, 0.12); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3);"
                   onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'; this.style.color='#ffffff';"
                   onmouseout="this.style.background='rgba(16, 185, 129, 0.12)'; this.style.color='#6ee7b7';">
                    Lihat Hasil Saya &rarr;
                </a>
            </div>

            <!-- Card 4: AI Consultant -->
            <div class="card p-5 sm:p-6 space-y-4 flex flex-col justify-between group touch-feedback">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105 shadow-md"
                             style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(217, 119, 6, 0.05) 100%); border: 1px solid rgba(245, 158, 11, 0.35); color: #fcd34d;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/25">
                            AI Gemini
                        </span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-amber-400">Konsultasi Personal</span>
                        <h3 class="font-bold text-white text-base sm:text-lg group-hover:text-amber-300 transition-colors">Asisten AI Mindora</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Diskusi mendalam tentang kekuatan, gaya kerja, dan strategi pengembangan diri.
                        </p>
                    </div>
                </div>
                <a href="{{ route('ai.conversation') }}"
                   class="block text-center w-full py-2.5 px-4 rounded-xl font-semibold text-xs transition-all duration-200 shadow-md touch-feedback"
                   style="background: rgba(245, 158, 11, 0.12); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.3);"
                   onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'; this.style.color='#ffffff';"
                   onmouseout="this.style.background='rgba(245, 158, 11, 0.12)'; this.style.color='#fcd34d';">
                    Eksplorasi AI &rarr;
                </a>
            </div>

        </div>
    </div>

    <!-- Quick Onboarding Guide for Users (Panduan Langkah) -->
    <div class="p-5 sm:p-7 rounded-2xl bg-gradient-to-r from-purple-950/40 via-indigo-950/30 to-purple-950/40 border border-purple-500/25 space-y-4">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-sm sm:text-base font-bold text-white">Panduan Memulai Asesmen Mindora</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 space-y-1">
                <span class="font-bold text-cyan-300">1. Kerjakan Tes Kognitif</span>
                <p class="text-slate-400 text-[11px] leading-relaxed">Uji daya nalar logika dan kemampuan pemecahan masalah Anda terlebih dahulu.</p>
            </div>
            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 space-y-1">
                <span class="font-bold text-purple-300">2. Isi Tes Big Five</span>
                <p class="text-slate-400 text-[11px] leading-relaxed">Jawab 50 butir kuesioner kepribadian sesuai kecenderungan alami Anda.</p>
            </div>
            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/5 space-y-1">
                <span class="font-bold text-amber-300">3. Diskusi dengan AI</span>
                <p class="text-slate-400 text-[11px] leading-relaxed">Konsultasikan hasil analitik untuk mendapat rekomendasi karir &amp; self-growth.</p>
            </div>
        </div>
    </div>

</div>
@endsection