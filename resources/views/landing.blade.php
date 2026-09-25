@extends('layouts.app')
@section('title', 'Platform Psikometri & AI Assessment')

@section('content')
<div class="space-y-16 sm:space-y-24 py-8 sm:py-16">

    {{-- ===================== HERO SECTION ===================== --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 sm:space-y-8 relative">

        {{-- Decorative glow behind hero --}}
        <div class="absolute inset-x-0 top-0 -translate-y-1/2 h-64 pointer-events-none" style="background:radial-gradient(ellipse 60% 100% at 50% 0%, rgba(124,58,237,0.25) 0%, transparent 80%);"></div>

        {{-- Pill Badge --}}
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold shadow-md" style="background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.35);color:#c4b5fd;">
            <span class="w-2 h-2 rounded-full animate-pulse" style="background:#38bdf8;box-shadow:0 0 8px #38bdf8;"></span>
            <span>Generasi Baru Tes Psikometri &amp; AI Assistant</span>
        </div>

        {{-- Logo in hero --}}
        <div class="flex justify-center">
            <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105 glow-violet" style="background:white;padding:4px;box-shadow:0 0 45px rgba(124,58,237,0.4);">
                <img src="{{ asset('images/logo-mark-512.png') }}" alt="Mindora" class="w-full h-full object-contain">
            </div>
        </div>

        {{-- Main Headline --}}
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white max-w-4xl mx-auto leading-tight sm:leading-tight">
            Kenali Potensi Dirimu Melalui
            <span style="background:linear-gradient(135deg,#a894ff 0%,#7c3aed 45%,#38bdf8 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                Data &amp; AI
            </span>
            <span style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0;">Understand Yourself Through Data &amp; AI</span>
        </h1>

        {{-- Subtitle --}}
        <p class="text-sm sm:text-lg max-w-2xl mx-auto leading-relaxed px-2" style="color:#94a3b8;">
            Kerjakan tes kognitif dan kepribadian Big Five secara mandiri, lalu dalami hasil analitikmu
            bersama Asisten AI Mindora yang memahami profilmu secara privat &amp; akurat.
        </p>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 pt-2 max-w-md sm:max-w-none mx-auto w-full">
            @auth
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto btn-primary text-sm sm:text-base px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl flex items-center justify-center gap-2 touch-feedback shadow-lg">
                    <span>Buka Dashboard Saya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
                <a href="{{ route('assessments.index') }}" class="w-full sm:w-auto btn-secondary text-sm sm:text-base px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl text-center touch-feedback">
                    Mulai Tes Baru
                </a>
            @else
                <a href="{{ route('register') }}" class="w-full sm:w-auto btn-primary text-sm sm:text-base px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl flex items-center justify-center gap-2 touch-feedback shadow-lg">
                    <span>Mulai Gratis Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto btn-secondary text-sm sm:text-base px-6 sm:px-8 py-3.5 sm:py-4 rounded-xl text-center touch-feedback">
                    Pelajari Fitur
                </a>
            @endauth
        </div>

        {{-- Feature Badges (Mobile-friendly wrapped chips) --}}
        <div class="pt-4 flex flex-wrap items-center justify-center gap-2 sm:gap-3 text-xs">
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium" style="background:rgba(124,58,237,0.12);border:1px solid rgba(124,58,237,0.25);color:#c4b5fd;">
                <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Tes Kemampuan Berpikir
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium" style="background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.25);color:#7dd3fc;">
                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Tes Karakter Big Five
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:#6ee7b7;">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Radar &amp; Analitik ML
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);color:#fcd34d;">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Data Terenkripsi Aman
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full font-medium" style="background:rgba(124,58,237,0.12);border:1px solid rgba(124,58,237,0.25);color:#c4b5fd;">
                <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Asisten AI Gemini
            </span>
        </div>
    </section>

    {{-- ===================== HOW IT WORKS ===================== --}}
    <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">

        <div class="text-center space-y-2 sm:space-y-3">
            <div class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase" style="background:rgba(124,58,237,0.15);color:#a78bfa;border:1px solid rgba(124,58,237,0.25);">
                Metodologi &amp; Proses
            </div>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-white">Bagaimana Mindora Bekerja</h2>
            <p class="text-slate-400 max-w-xl mx-auto text-xs sm:text-sm leading-relaxed">Tiga langkah sistematis untuk memahami potensi dan kecenderungan diri Anda secara mendalam.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            {{-- Step 1 --}}
            <div class="card p-6 sm:p-8 space-y-4 sm:space-y-5 group relative overflow-hidden touch-feedback">
                <div class="absolute -right-3 -top-3 text-7xl sm:text-8xl font-black opacity-10 select-none" style="color:#7c3aed;">01</div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 shadow-md" style="background:rgba(124,58,237,0.2);border:1px solid rgba(124,58,237,0.35);color:#c4b5fd;">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div class="text-[11px] sm:text-xs font-bold tracking-widest uppercase text-purple-400">Langkah 01</div>
                <h3 class="text-lg sm:text-xl font-bold text-white">Kerjakan Asesmen</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">Kerjakan tes penalaran adaptif (logika, numerik, verbal) serta instrumen Big Five dengan pencatatan waktu respons secara presisi.</p>
            </div>

            {{-- Step 2 --}}
            <div class="card p-6 sm:p-8 space-y-4 sm:space-y-5 group relative overflow-hidden touch-feedback">
                <div class="absolute -right-3 -top-3 text-7xl sm:text-8xl font-black opacity-10 select-none" style="color:#38bdf8;">02</div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 shadow-md" style="background:rgba(56,189,248,0.18);border:1px solid rgba(56,189,248,0.35);color:#7dd3fc;">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="text-[11px] sm:text-xs font-bold tracking-widest uppercase text-cyan-400">Langkah 02</div>
                <h3 class="text-lg sm:text-xl font-bold text-white">Dapatkan Hasil &amp; Analitik</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">Scoring engine menghitung skor terstandarisasi yang diperkaya klaster machine learning, persentil populasi, dan radar kepribadian.</p>
            </div>

            {{-- Step 3 --}}
            <div class="card p-6 sm:p-8 space-y-4 sm:space-y-5 group relative overflow-hidden touch-feedback">
                <div class="absolute -right-3 -top-3 text-7xl sm:text-8xl font-black opacity-10 select-none" style="color:#a78bfa;">03</div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center transition-all duration-300 group-hover:scale-110 shadow-md" style="background:rgba(167,139,250,0.18);border:1px solid rgba(167,139,250,0.35);color:#d8b4fe;">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div class="text-[11px] sm:text-xs font-bold tracking-widest uppercase text-purple-400">Langkah 03</div>
                <h3 class="text-lg sm:text-xl font-bold text-white">Eksplorasi dengan AI</h3>
                <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">Konsultasikan hasil Anda dengan AI Assistant Mindora untuk mendapatkan saran gaya kerja dan tips pengembangan diri tanpa mengekspos data PII.</p>
            </div>
        </div>
    </section>

    {{-- ===================== FEATURES SECTION (2 COLS MOBILE, 4 COLS DESKTOP) ===================== --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">

        <div class="text-center space-y-2 sm:space-y-3">
            <div class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase" style="background:rgba(56,189,248,0.12);color:#7dd3fc;border:1px solid rgba(56,189,248,0.25);">
                Fitur Unggulan
            </div>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-white">Dirancang Akurat &amp; Nyaman Digunakan</h2>
            <p class="text-slate-400 max-w-xl mx-auto text-xs sm:text-sm leading-relaxed">Seluruh instrumen pengujian dan fitur disusun secara praktis agar mudah dipahami siapa saja.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            {{-- Feature 1 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(56,189,248,0.15);color:#38bdf8;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Tes Kemampuan Berpikir</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">30 soal penalaran logika, numerik, dan verbal adaptif.</p>
            </div>

            {{-- Feature 2 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(124,58,237,0.18);color:#c4b5fd;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Pemetaan Karakter</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Model Big Five komprehensif untuk memetakan perilaku kerja.</p>
            </div>

            {{-- Feature 3 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,0.15);color:#6ee7b7;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Alasan &amp; Bukti Nilai</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Interpretasi SHAP berbasis machine learning untuk transparansi skor.</p>
            </div>

            {{-- Feature 4 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(245,158,11,0.15);color:#fcd34d;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Konsultasi AI Pribadi</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Tanya jawab kontekstual dengan AI Gemini seputar karir &amp; potensi.</p>
            </div>

            {{-- Feature 5 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(56,189,248,0.15);color:#38bdf8;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Keamanan Data Kuat</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Enkripsi terstandarisasi menjaga hasil dan identitas tetap terlindungi.</p>
            </div>

            {{-- Feature 6 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(124,58,237,0.18);color:#c4b5fd;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Sensor Data Pribadi</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Email dan data sensitif disensor otomatis di layar (masking PII).</p>
            </div>

            {{-- Feature 7 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(16,185,129,0.15);color:#6ee7b7;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Persentil Populasi</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Ketahui posisi skor Anda dibandingkan benchmark populasi umum.</p>
            </div>

            {{-- Feature 8 --}}
            <div class="card p-4 sm:p-6 space-y-2.5 sm:space-y-3 rounded-2xl transition-all duration-300 hover:border-purple-500/40 hover:-translate-y-1 touch-feedback">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center" style="background:rgba(245,158,11,0.15);color:#fcd34d;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Zero-PII AI Engine</h4>
                <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed">Model AI hanya memproses skor numerik tanpa identitas pribadi pengguna.</p>
            </div>
        </div>
    </section>

    {{-- ===================== SECURITY SECTION ===================== --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="p-6 sm:p-12 rounded-3xl space-y-6 sm:space-y-8 relative overflow-hidden" style="background:linear-gradient(135deg,rgba(45,27,105,0.6) 0%,rgba(79,70,229,0.15) 50%,rgba(13,11,51,0.8) 100%);border:1px solid rgba(124,58,237,0.25);">

            <div class="space-y-2 sm:space-y-3">
                <div class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase" style="background:rgba(124,58,237,0.2);color:#c4b5fd;border:1px solid rgba(124,58,237,0.3);">
                    Keamanan &amp; Privasi
                </div>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white">Privasi Anda Adalah Prioritas Utama</h2>
                <p class="text-slate-400 max-w-2xl text-xs sm:text-sm leading-relaxed">Setiap data psikometri dan profil pribadi Anda diamankan dengan standar perlindungan digital yang ketat.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="p-5 rounded-2xl space-y-2.5 bg-white/[0.03] border border-purple-500/20">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-purple-600/20 text-purple-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h4 class="font-bold text-white text-sm">Enkripsi End-to-End</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">Data profil dan jawaban tes dikunci rapat sehingga aman dan tidak dapat disalahgunakan pihak luar.</p>
                </div>
                <div class="p-5 rounded-2xl space-y-2.5 bg-white/[0.03] border border-cyan-500/20">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-cyan-600/20 text-cyan-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h4 class="font-bold text-white text-sm">Proteksi Kata Sandi Argon2id</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">Kredensial Anda dienkripsi dengan standar hash modern yang tahan terhadap serangan komputasi.</p>
                </div>
                <div class="p-5 rounded-2xl space-y-2.5 bg-white/[0.03] border border-emerald-500/20">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-emerald-600/20 text-emerald-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4 class="font-bold text-white text-sm">Audit Log &amp; Pemantauan</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">Setiap aktivitas login dan pengubahan data dicatat di sistem audit log terpusat secara transparan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CTA SECTION ===================== --}}
    @guest
    <section class="max-w-4xl mx-auto px-4 text-center space-y-6">
        <div class="p-8 sm:p-14 rounded-3xl space-y-5 sm:space-y-6 glow-violet" style="background:linear-gradient(135deg,#1e1b5e 0%,#2d1b69 50%,#14124a 100%);border:1px solid rgba(124,58,237,0.35);">
            <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">Siap Memulai Eksplorasi Diri?</h2>
            <p class="text-slate-300 text-xs sm:text-base max-w-xl mx-auto leading-relaxed">
                Bergabunglah dengan Mindora sekarang dan rasakan kejelasan profil psikometri Anda dalam hitungan menit.
            </p>
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md sm:max-w-none mx-auto w-full">
                <a href="{{ route('register') }}" class="w-full sm:w-auto btn-primary px-8 py-3.5 sm:py-4 rounded-xl text-sm sm:text-base shadow-xl flex items-center justify-center gap-2 touch-feedback">
                    <span>Daftar Akun Gratis</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto btn-secondary px-8 py-3.5 sm:py-4 rounded-xl text-sm sm:text-base text-center touch-feedback">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </section>
    @endguest

    {{-- ===================== DISCLAIMER IMAGE POPUP ===================== --}}
    <style>
        /* Blur the main page content when popup is active */
        body.disclaimer-active > *:not(#disclaimer-overlay) {
            filter: blur(8px);
            transition: filter 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            user-select: none;
        }
        body:not(.disclaimer-active) > * {
            filter: blur(0);
            transition: filter 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Overlay — covers entire screen */
        #disclaimer-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: rgba(10, 8, 40, 0.55);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #disclaimer-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* Popup container — centered on viewport */
        #disclaimer-popup {
            position: relative;
            max-width: 600px;
            width: 100%;
            border-radius: 1.25rem;
            overflow: hidden;
            transform: scale(0.9);
            opacity: 0;
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
                        opacity 0.35s ease;
            box-shadow:
                0 0 0 1px rgba(124, 58, 237, 0.25),
                0 20px 50px -10px rgba(0, 0, 0, 0.6),
                0 0 35px rgba(124, 58, 237, 0.2);
        }
        #disclaimer-overlay.active #disclaimer-popup {
            transform: scale(1);
            opacity: 1;
        }

        /* Close button */
        #disclaimer-close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: rgba(255, 255, 255, 0.85);
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        #disclaimer-close-btn:hover {
            background: rgba(239, 68, 68, 0.85);
            color: #fff;
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 0 16px rgba(239, 68, 68, 0.4);
        }

        /* Image styling */
        #disclaimer-popup img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 1.25rem;
        }

        /* Subtle glow ring animation */
        #disclaimer-popup::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 1.35rem;
            background: linear-gradient(135deg, rgba(124,58,237,0.5), rgba(56,189,248,0.3), rgba(124,58,237,0.5));
            background-size: 300% 300%;
            z-index: -1;
            animation: glowRing 4s ease-in-out infinite;
            opacity: 0.6;
        }
        @keyframes glowRing {
            0%, 100% { background-position: 0% 50%; opacity: 0.6; }
            50% { background-position: 100% 50%; opacity: 1; }
        }

        /* Responsive — on small screens */
        @media (max-width: 640px) {
            #disclaimer-overlay {
                padding: 0.75rem;
            }
            #disclaimer-popup {
                max-width: 95%;
            }
        }
    </style>

    <div id="disclaimer-overlay">
        <div id="disclaimer-popup">
            {{-- Close (X) button --}}
            <button type="button" id="disclaimer-close-btn" onclick="closeDisclaimerPopup()" aria-label="Tutup Notifikasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            {{-- Notification Image --}}
            <img src="{{ asset('images/notif.png') }}" alt="Notifikasi Disclaimer Mindora" loading="eager">
        </div>
    </div>

    <script>
        (function() {
            // Move overlay to <body> level so it won't be affected by blur
            const overlay = document.getElementById('disclaimer-overlay');
            if (overlay) {
                document.body.appendChild(overlay);
            }

            function openDisclaimerPopup() {
                if (overlay) {
                    document.body.classList.add('disclaimer-active');
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }

            window.closeDisclaimerPopup = function() {
                if (overlay) {
                    overlay.classList.remove('active');
                    document.body.classList.remove('disclaimer-active');
                    document.body.style.overflow = '';
                }
            };

            // Close on clicking the overlay background (not the popup itself)
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        window.closeDisclaimerPopup();
                    }
                });
            }

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    window.closeDisclaimerPopup();
                }
            });

            // Show popup on every page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(openDisclaimerPopup, 400);
                });
            } else {
                setTimeout(openDisclaimerPopup, 400);
            }
        })();
    </script>

</div>
@endsection