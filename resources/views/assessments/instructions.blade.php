@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <!-- Back Button -->
    <a href="{{ route('assessments.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Kembali ke Katalog Asesmen
    </a>

    <!-- Instruction Card -->
    <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl backdrop-blur-xl space-y-8">
        <div class="space-y-3">
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $assessment->isCognitive() ? 'bg-blue-950/80 text-blue-300 border border-blue-800/80' : 'bg-purple-950/80 text-purple-300 border border-purple-800/80' }}">
                {{ $assessment->category->name }} &bull; Versi {{ $assessment->version }}
            </span>
            <h1 class="text-3xl font-extrabold text-white">{{ $assessment->title }}</h1>
            <p class="text-sm text-slate-400 leading-relaxed">{{ $assessment->description }}</p>
        </div>

        <!-- Meta Information Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs">
            <div>
                <span class="text-slate-500 block">Jumlah Soal</span>
                <span class="font-bold text-slate-200 text-sm">{{ $assessment->questions_count }} Pertanyaan</span>
            </div>
            <div>
                <span class="text-slate-500 block">Alokasi Waktu</span>
                <span class="font-bold text-slate-200 text-sm">{{ $assessment->duration_minutes }} Menit</span>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <span class="text-slate-500 block">Format Jawaban</span>
                <span class="font-bold text-slate-200 text-sm">{{ $assessment->isCognitive() ? 'Pilihan Ganda (MCQ)' : 'Skala Likert (1 - 5)' }}</span>
            </div>
        </div>

        <!-- Guidelines List -->
        <div class="space-y-4 text-xs text-slate-300">
            <h3 class="font-bold text-white text-sm uppercase tracking-wider">Petunjuk Pengerjaan:</h3>
            <ul class="space-y-3 list-none">
                <li class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">1</span>
                    <span class="leading-relaxed">Pastikan Anda berada di lingkungan yang tenang dan kondusif untuk konsentrasi penuh selama pengerjaan.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">2</span>
                    <span class="leading-relaxed">Jawaban Anda tersimpan secara otomatis setiap kali Anda memilih opsi. Jika halaman tidak sengaja ter-refresh, kemajuan Anda tetap aman.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">3</span>
                    <span class="leading-relaxed">Sistem mencatat latensi waktu respons per soal (millisecond) sebagai sinyal perilaku analitis tambahan. Jawablah secara spontan dan jujur.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">4</span>
                    <span class="leading-relaxed">Asesmen ini adalah instrumen self-insight psikometri mandiri dan bukan diagnosis medis atau IQ klinis.</span>
                </li>
            </ul>
        </div>

        <!-- Disclaimer Box -->
        <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400 leading-relaxed">
            <strong class="text-slate-300">Pernyataan Etika Psikometri:</strong>
            Hasil asesmen disajikan sebagai profil kecenderungan dan bahan refleksi pengembangan diri. Skor kognitif mencerminkan Cognitive Ability Score pada instrumen ini dan tidak boleh diklaim sebagai diagnosis inteligensi klinis definitif.
        </div>

        <!-- Action CTA -->
        <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('assessments.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold text-center transition-colors">
                Batal
            </a>

            <form action="{{ route('assessments.start', $assessment) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-[#f2f0ff] hover:bg-white text-[#0c0a2a] font-bold text-xs rounded-full transition-all hover:shadow-[0_10px_40px_-12px_rgba(169,155,255,0.65)] flex items-center justify-center gap-2">
                    @if($existingSession)
                        <span>Lanjutkan Sesi Berjalan &rarr;</span>
                    @else
                        <span>Mulai Kerjakan Tes Sekarang &rarr;</span>
                    @endif
                </button>
            </form>
        </div>
    </div>
</div>
@endsection