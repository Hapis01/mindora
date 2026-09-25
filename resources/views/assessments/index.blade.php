@extends('layouts.app')
@section('title', 'Katalog Asesmen Psikometri')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
    <!-- Header -->
    <div class="p-8 sm:p-10 rounded-3xl relative overflow-hidden"
         style="background: linear-gradient(135deg, rgba(30, 27, 94, 0.75) 0%, rgba(13, 11, 51, 0.9) 60%, rgba(124, 58, 237, 0.2) 100%); border: 1px solid rgba(124, 58, 237, 0.25); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);">
        
        <div class="absolute right-0 top-0 w-80 h-80 pointer-events-none rounded-full" style="background: radial-gradient(circle, rgba(124, 58, 237, 0.15) 0%, transparent 70%);"></div>

        <div class="relative z-10 space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.35); color: #c4b5fd;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: #7c3aed; box-shadow: 0 0 6px #7c3aed;"></span>
                Katalog Asesmen Psikometri Terstandarisasi
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Instrumen Asesmen <span class="text-mindora">Mindora</span>
            </h1>
            <p class="text-sm text-slate-300 max-w-3xl leading-relaxed">
                Pilihlah instrumen psikometri di bawah untuk mengeksplorasi kemampuan penalaran kognitif atau profil kecenderungan kepribadian Big Five Anda. Hasil diproses menggunakan metode psikometri terstandarisasi yang diperkaya model machine learning.
            </p>
        </div>
    </div>

    <!-- Available Assessments Grid -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            Daftar Instrumen Aktif
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($categories as $category)
                @foreach($category->assessments as $assessment)
                    <div class="card p-8 flex flex-col justify-between space-y-6 group">
                        <div class="space-y-4">
                            <!-- Badges -->
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                      style="{{ $assessment->isCognitive() ? 'background: rgba(56, 189, 248, 0.12); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.3);' : 'background: rgba(124, 58, 237, 0.15); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.35);' }}">
                                    {{ $category->name }} &bull; {{ $assessment->version }}
                                </span>
                                <span class="text-xs text-slate-400 flex items-center gap-1.5 font-mono">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $assessment->duration_minutes }} Menit
                                </span>
                            </div>

                            <!-- Title & Description -->
                            <div class="space-y-2">
                                <h3 class="text-2xl font-bold text-white group-hover:text-purple-300 transition-colors">
                                    {{ $assessment->title }}
                                </h3>
                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ $assessment->description }}
                                </p>
                            </div>

                            <!-- Details Pill -->
                            <div class="flex flex-wrap gap-2 pt-2 text-[11px] text-slate-400">
                                <span class="px-2.5 py-1 rounded-lg flex items-center gap-1.5" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $assessment->questions_count }} Pertanyaan
                                </span>
                                @if($assessment->isCognitive())
                                    <span class="px-2.5 py-1 rounded-lg" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">Domain: Logical, Numerical, Verbal</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">Model: OCEAN Likert Scale</span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-4" style="border-top: 1px solid rgba(124, 58, 237, 0.15);">
                            <a href="{{ route('assessments.instructions', $assessment) }}"
                               class="btn-mindora w-full py-3.5 px-4 rounded-xl text-white font-bold text-sm shadow-lg transition-all flex items-center justify-center gap-2">
                                <span>Petunjuk &amp; Mulai Asesmen</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Recent Assessment History -->
    @if($recentSessions->isNotEmpty())
        <div class="p-8 rounded-3xl space-y-6" style="background: rgba(30, 27, 94, 0.4); border: 1px solid rgba(124, 58, 237, 0.2); backdrop-filter: blur(16px);">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Riwayat Pengerjaan Anda
            </h2>

            <div class="divide-y" style="border-color: rgba(124, 58, 237, 0.12);">
                @foreach($recentSessions as $sess)
                    <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h4 class="font-bold text-white text-sm">{{ $sess->assessment->title }}</h4>
                            <p class="text-xs text-slate-400">
                                Dikerjakan pada {{ $sess->created_at->format('d M Y, H:i') }} &bull;
                                Durasi: {{ $sess->total_time_seconds ? gmdate('i:s', $sess->total_time_seconds) : '-' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($sess->isCompleted() && $sess->score)
                                <a href="{{ route('assessments.result', $sess->score) }}"
                                   class="px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shadow-md"
                                   style="background: rgba(16, 185, 129, 0.15); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.35);"
                                   onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'"
                                   onmouseout="this.style.background='rgba(16, 185, 129, 0.15)'">
                                    <span>Lihat Hasil &amp; Insight</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            @else
                                <a href="{{ route('assessments.take', $sess) }}"
                                   class="px-4 py-2 rounded-xl text-xs font-semibold transition-all shadow-md"
                                   style="background: rgba(245, 158, 11, 0.15); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.35);"
                                   onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'"
                                   onmouseout="this.style.background='rgba(245, 158, 11, 0.15)'">
                                    Lanjutkan Sesi
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection