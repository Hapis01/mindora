@extends('layouts.app')
@section('title', 'Dashboard Pengguna')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8 sm:space-y-12">

    {{-- Welcome Hero --}}
    <div class="dash-hero">
        <div class="dash-hero__glow"></div>
        <img class="dash-hero__logo" src="{{ asset('images/landing/logo-ghost.webp') }}" alt="" width="720" height="720">

        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
            <div class="space-y-5 w-full md:w-auto min-w-0">
                <span class="dash-chip"><i></i>Instrumen psikometri siap diakses</span>

                <div class="flex items-center gap-4">
                    <div class="dash-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <h1 class="dash-title break-words sm:truncate">Selamat datang, <em>{{ $user->name }}</em></h1>
                        <div class="text-xs sm:text-sm mt-2 flex flex-wrap items-center gap-2" style="color:#a3a1cf;">
                            <span>Akun: <span class="dash-mono" style="color:#ecebff;">{{ $user->masked_email }}</span></span>
                            <span class="inline-flex items-center gap-1.5 text-[11px] px-2.5 py-0.5 rounded-full" style="border:1px solid rgba(169,155,255,0.3);color:#cfc8ff;">
                                <i class="ph-bold ph-lock-simple"></i>Masked PII
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-auto">
                <a href="{{ route('profile.show') }}" class="btn-secondary w-full md:w-auto justify-center text-sm touch-feedback">
                    <i class="ph-bold ph-user-circle"></i>
                    <span>Profil &amp; keamanan data</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Status strip --}}
    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 sm:gap-x-10">
        <div class="metric">
            <dt>Asesmen</dt>
            <dd><span class="metric__v">2 instrumen</span><span class="metric__s">Kognitif dan Big Five</span></dd>
        </div>
        <div class="metric">
            <dt>Enkripsi</dt>
            <dd><span class="metric__v">Argon2id</span><span class="metric__s">Privasi terlindungi</span></dd>
        </div>
        <div class="metric">
            <dt>Asisten AI</dt>
            <dd><span class="metric__v">Aktif 24 jam</span><span class="metric__s">Siap konsultasi</span></dd>
        </div>
        <div class="metric">
            <dt>Zero-PII</dt>
            <dd><span class="metric__v">Aktif</span><span class="metric__s">Anonim dan aman</span></dd>
        </div>
    </dl>

    {{-- Menu utama --}}
    <section class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <h2 class="dash-h2">Pilihan asesmen &amp; menu utama</h2>
            <a href="{{ route('assessments.index') }}" class="dash-link flex items-center gap-1 touch-feedback">
                <span>Katalog lengkap</span><i class="ph-bold ph-arrow-up-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5" data-spotlight>
            <a href="{{ $cognitiveAssessment ? route('assessments.instructions', $cognitiveAssessment) : route('assessments.index') }}" class="card act touch-feedback">
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="act__icon"><i class="ph-bold ph-brain"></i></span>
                        <span class="act__tag">30 menit</span>
                    </div>
                    <div>
                        <span class="act__kicker">Penalaran &amp; logika</span>
                        <h3 class="act__name">Tes Kognitif</h3>
                        <p class="act__desc">Uji kemampuan penalaran logis, pola numerik, dan verbal secara terstruktur.</p>
                    </div>
                </div>
                <span class="act__go"><span>Mulai tes kognitif</span><i class="ph-bold ph-arrow-up-right"></i></span>
            </a>

            <a href="{{ $personalityAssessment ? route('assessments.instructions', $personalityAssessment) : route('assessments.index') }}" class="card act touch-feedback">
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="act__icon"><i class="ph-bold ph-users-three"></i></span>
                        <span class="act__tag">15 menit</span>
                    </div>
                    <div>
                        <span class="act__kicker">Dimensi karakter</span>
                        <h3 class="act__name">Tes Kepribadian</h3>
                        <p class="act__desc">Model Big Five (O-C-E-A-N) untuk memetakan dinamika perilaku kerja Anda.</p>
                    </div>
                </div>
                <span class="act__go"><span>Mulai tes Big Five</span><i class="ph-bold ph-arrow-up-right"></i></span>
            </a>

            <a href="{{ route('assessments.index') }}" class="card act touch-feedback">
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="act__icon"><i class="ph-bold ph-chart-polar"></i></span>
                        <span class="act__tag">Radar &amp; ML</span>
                    </div>
                    <div>
                        <span class="act__kicker">Analitik mandiri</span>
                        <h3 class="act__name">Hasil &amp; Analisis</h3>
                        <p class="act__desc">Grafik radar kepribadian, persentil populasi, dan bukti skor SHAP.</p>
                    </div>
                </div>
                <span class="act__go"><span>Lihat hasil saya</span><i class="ph-bold ph-arrow-up-right"></i></span>
            </a>

            <a href="{{ route('ai.conversation') }}" class="card act touch-feedback">
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="act__icon"><i class="ph-bold ph-chat-circle-text"></i></span>
                        <span class="act__tag">Asisten AI</span>
                    </div>
                    <div>
                        <span class="act__kicker">Konsultasi personal</span>
                        <h3 class="act__name">Asisten AI Mindora</h3>
                        <p class="act__desc">Diskusi mendalam tentang kekuatan, gaya kerja, dan strategi pengembangan diri.</p>
                    </div>
                </div>
                <span class="act__go"><span>Eksplorasi AI</span><i class="ph-bold ph-arrow-up-right"></i></span>
            </a>
        </div>
    </section>

    {{-- Panduan --}}
    <section class="space-y-2">
        <h2 class="dash-h2">Panduan memulai asesmen</h2>
        <div class="guide">
            <div class="guide__step">
                <span class="guide__n">01</span>
                <h3 class="guide__t">Kerjakan tes kognitif</h3>
                <p class="guide__d">Uji daya nalar logika dan kemampuan pemecahan masalah Anda terlebih dahulu.</p>
            </div>
            <div class="guide__step">
                <span class="guide__n">02</span>
                <h3 class="guide__t">Isi tes Big Five</h3>
                <p class="guide__d">Jawab butir kuesioner kepribadian sesuai kecenderungan alami Anda.</p>
            </div>
            <div class="guide__step">
                <span class="guide__n">03</span>
                <h3 class="guide__t">Diskusi dengan AI</h3>
                <p class="guide__d">Konsultasikan hasil analitik untuk mendapat rekomendasi karier dan pengembangan diri.</p>
            </div>
        </div>
    </section>

</div>

@section('footer')
    {{-- Footer landing page — sama persis dengan landing.blade.php --}}
    <footer class="foot">
        <div class="wrap">
            <div class="foot__grid">
                <div>
                    <div class="foot__brand"><span class="nav__logo"><img src="{{ asset('images/logo-icon-64.png') }}" alt="" width="38" height="38"></span>Mindora</div>
                    <p class="foot__about">Platform psikometri online dengan analitik machine learning dan Asisten AI. Hanya untuk eksplorasi diri, bukan diagnosis psikologis.</p>
                </div>
                <div class="foot__links">
                    <a href="{{ route('home') }}">Beranda</a>
                    <a href="{{ route('assessments.index') }}">Katalog Tes</a>
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ asset('docs/Dokumentasi_Sistem_Mindora_Metode_ML_Security.pdf') }}" target="_blank" rel="noopener">Dokumentasi</a>
                    <button type="button" data-open-disclaimer>Disclaimer</button>
                </div>
            </div>
            <div class="foot__word" aria-hidden="true">mindora</div>
            <p class="foot__copy">&copy; {{ date('Y') }} Mindora.</p>
        </div>
    </footer>
@endsection

@push('scripts')
<script>
    // Sorotan kursor pada kartu menu (hanya perangkat dengan pointer halus)
    if (matchMedia('(hover: hover) and (pointer: fine)').matches) {
        document.querySelectorAll('[data-spotlight] .act').forEach(function (card) {
            card.addEventListener('pointermove', function (e) {
                var r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top) + 'px');
            });
        });
    }
</script>
@endpush
@endsection
