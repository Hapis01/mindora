@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8" x-data="{
    initAdminCharts() {
        // 1. Cognitive Averages Bar Chart
        const ctxCog = document.getElementById('adminCognitiveChart');
        if (ctxCog) {
            new Chart(ctxCog, {
                type: 'bar',
                data: {
                    labels: ['Penalaran Logis', 'Penalaran Numerik', 'Penalaran Verbal'],
                    datasets: [{
                        label: 'Rata-rata Skor (%)',
                        data: [{{ $cogAvgLogical }}, {{ $cogAvgNumerical }}, {{ $cogAvgVerbal }}],
                        backgroundColor: ['rgba(59, 130, 246, 0.75)', 'rgba(168, 85, 247, 0.75)', 'rgba(16, 185, 129, 0.75)'],
                        borderColor: ['#a99bff', '#a99bff', '#a99bff'],
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, max: 100, grid: { color: 'rgba(51, 65, 85, 0.4)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#cbd5e1', font: { weight: 'bold' } } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Big Five Radar Chart
        const ctxPers = document.getElementById('adminPersonalityChart');
        if (ctxPers) {
            new Chart(ctxPers, {
                type: 'radar',
                data: {
                    labels: ['Openness', 'Conscientiousness', 'Extraversion', 'Agreeableness', 'Neuroticism'],
                    datasets: [{
                        label: 'Rata-rata Populasi (%)',
                        data: [
                            {{ $persAvgOpenness }},
                            {{ $persAvgConscientiousness }},
                            {{ $persAvgExtraversion }},
                            {{ $persAvgAgreeableness }},
                            {{ $persAvgNeuroticism }}
                        ],
                        backgroundColor: 'rgba(169, 155, 255, 0.25)',
                        borderColor: '#a99bff',
                        pointBackgroundColor: '#cfc8ff',
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
                            pointLabels: { color: '#cbd5e1', font: { size: 11, weight: 'bold' } },
                            ticks: { backdropColor: 'transparent', color: '#64748b', stepSize: 20 },
                            suggestedMin: 0, suggestedMax: 100
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }
}" x-init="initAdminCharts()">

    <!-- Admin Header -->
    <div class="p-8 rounded-3xl border border-[rgba(190,184,255,0.14)] bg-[#100e36] flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950 border border-purple-800 text-purple-300 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                Admin Control Center &bull; RBAC Protected
            </div>
            <h1 class="text-3xl font-extrabold text-white">Dashboard Administrasi Mindora</h1>
            <p class="text-sm text-slate-400">
                Pengawasan komprehensif metrik asesmen, distribusi skor kognitif &amp; kepribadian, model ML, dan keamanan.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs font-mono text-slate-300">
                Data Sensitif: <strong class="text-purple-400">Masked Default</strong>
            </span>
        </div>
    </div>

    <!-- Admin Sub-Navigation -->
    @include('admin.nav')

    <!-- Top Key Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Metric 1: Users -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <span>Total Pengguna</span>
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div class="text-3xl font-black text-white font-mono">{{ $totalUsers }}</div>
            <p class="text-[11px] text-slate-500">Terdaftar di sistem Mindora</p>
        </div>

        <!-- Metric 2: Completed Assessments -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <span>Asesmen Selesai</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="text-3xl font-black text-emerald-400 font-mono">{{ $totalSessions }}</div>
            <p class="text-[11px] text-slate-500">Sesi psikometri tervalidasi</p>
        </div>

        <!-- Metric 3: AI Consultation Messages -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <span>Konsultasi AI</span>
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
            </div>
            <div class="text-3xl font-black text-purple-400 font-mono">{{ $totalAiMessages }}</div>
            <p class="text-[11px] text-slate-500">Respons AI &amp; personal insights</p>
        </div>

        <!-- Metric 4: Security Audit Logs -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-400 uppercase tracking-wider">
                <span>Audit Trail Records</span>
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div class="text-3xl font-black text-purple-400 font-mono">{{ $totalAuditLogs }}</div>
            <p class="text-[11px] text-slate-500">Aktivitas keamanan tercatat</p>
        </div>
    </div>

    <!-- Distribution Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Cognitive Score Distribution -->
        <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-white text-sm">Rata-rata Skor Kemampuan Kognitif</h3>
                <span class="text-xs text-slate-400 font-mono">Skala 0 - 100%</span>
            </div>
            <div class="h-64 relative">
                <canvas id="adminCognitiveChart"></canvas>
            </div>
            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-800/80 text-center text-xs">
                <div class="p-2 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Logis</span>
                    <strong class="text-blue-400 font-mono">{{ $cogAvgLogical }}%</strong>
                </div>
                <div class="p-2 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Numerik</span>
                    <strong class="text-purple-400 font-mono">{{ $cogAvgNumerical }}%</strong>
                </div>
                <div class="p-2 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400 block text-[10px]">Verbal</span>
                    <strong class="text-emerald-400 font-mono">{{ $cogAvgVerbal }}%</strong>
                </div>
            </div>
        </div>

        <!-- Big Five Personality Distribution -->
        <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-white text-sm">Distribusi Populasi Dimensi Big Five</h3>
                <span class="text-xs text-slate-400 font-mono">Peta Radar Normatif</span>
            </div>
            <div class="h-64 relative">
                <canvas id="adminPersonalityChart"></canvas>
            </div>
            <div class="grid grid-cols-5 gap-1.5 pt-2 border-t border-slate-800/80 text-center text-[10px]">
                <div class="p-1.5 rounded-lg bg-slate-950 border border-slate-800">
                    <span class="text-slate-500 block">Openness</span>
                    <strong class="text-purple-400 font-mono">{{ $persAvgOpenness }}%</strong>
                </div>
                <div class="p-1.5 rounded-lg bg-slate-950 border border-slate-800">
                    <span class="text-slate-500 block">Consc.</span>
                    <strong class="text-purple-400 font-mono">{{ $persAvgConscientiousness }}%</strong>
                </div>
                <div class="p-1.5 rounded-lg bg-slate-950 border border-slate-800">
                    <span class="text-slate-500 block">Extra.</span>
                    <strong class="text-purple-400 font-mono">{{ $persAvgExtraversion }}%</strong>
                </div>
                <div class="p-1.5 rounded-lg bg-slate-950 border border-slate-800">
                    <span class="text-slate-500 block">Agree.</span>
                    <strong class="text-purple-400 font-mono">{{ $persAvgAgreeableness }}%</strong>
                </div>
                <div class="p-1.5 rounded-lg bg-slate-950 border border-slate-800">
                    <span class="text-slate-500 block">Neuro.</span>
                    <strong class="text-purple-400 font-mono">{{ $persAvgNeuroticism }}%</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Asesmen & Audit Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Completed Scores (7 Cols) -->
        <div class="lg:col-span-7 p-6 rounded-3xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-white text-sm">Hasil Asesmen Terbaru</h3>
                <a href="{{ route('admin.users') }}" class="text-xs text-brand-400 hover:underline">Kelola Pengguna &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-slate-400 bg-slate-950/60 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Pengguna</th>
                            <th class="py-2.5 px-3">Instrumen</th>
                            <th class="py-2.5 px-3">Skor</th>
                            <th class="py-2.5 px-3">Arketipe</th>
                            <th class="py-2.5 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentScores as $s)
                            <tr class="hover:bg-slate-800/40">
                                <td class="py-2.5 px-3 font-mono text-slate-200">{{ $s->user?->masked_email ?? 'Pengguna' }}</td>
                                <td class="py-2.5 px-3 capitalize text-slate-400">{{ $s->assessment_type }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-brand-400">{{ $s->overall_score }}%</td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-300">{{ $s->archetype_label ?? '-' }}</td>
                                <td class="py-2.5 px-3">
                                    <a href="{{ route('assessments.result', $s->id) }}" class="text-xs text-blue-400 hover:underline">
                                        Lihat Hasil &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-500">Belum ada asesmen yang diselesaikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Stream (5 Cols) -->
        <div class="lg:col-span-5 p-6 rounded-3xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-white text-sm">Audit Stream Terkini</h3>
                <a href="{{ route('admin.audit-logs') }}" class="text-xs text-purple-400 hover:underline">Semua Log &rarr;</a>
            </div>

            <div class="space-y-2 text-xs">
                @forelse($recentAuditLogs as $log)
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800/80 flex items-start justify-between gap-2">
                        <div class="space-y-0.5">
                            <span class="font-mono font-bold text-slate-200 block text-[11px]">{{ $log->action }}</span>
                            <span class="text-[10px] text-slate-400">{{ $log->actor ? $log->actor->email : 'System' }}</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500">Belum ada catatan audit.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
