@extends('layouts.auth')
@section('title', 'Masuk ke Akun Anda')

@section('nav-action')
    <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Mulai gratis</a>
@endsection

@section('content')
    <div>
        <h1 class="auth__title">
            <span class="hero__line"><span>Lanjutkan,</span></span>
            <span class="hero__line"><span><em>kapan saja</em></span></span>
            <span class="hero__line"><span>kamu siap.</span></span>
        </h1>
        <p class="auth__lead" data-hero>Hasil tes dan percakapan dengan Asisten AI tersimpan di akunmu, siap dibuka dari perangkat mana pun.</p>
        <ul class="auth__points" data-hero>
            <li><i class="ph-bold ph-clock"></i><span><strong>Tes tersimpan otomatis.</strong> Berhenti kapan pun, lanjut di sesi yang sama.</span></li>
            <li><i class="ph-bold ph-chart-polar"></i><span><strong>Laporan dengan alasan skor.</strong> Arketipe ML dan penjelasan SHAP milikmu.</span></li>
            <li><i class="ph-bold ph-shield-check"></i><span><strong>Identitas tidak dikirim ke AI.</strong> Hanya skor numerik yang diproses.</span></li>
        </ul>
    </div>

    <div class="authcard" data-hero>
        <h2>Selamat datang kembali</h2>
        <p class="authcard__sub">Masuk ke akun Mindora untuk melanjutkan asesmen.</p>

        @if (session('info'))
            <div class="alert alert--ok" role="status">{{ session('info') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert--ok" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert--err" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="form">
            @csrf

            <div class="field">
                <label for="email">Alamat email</label>
                <div class="field__box">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com">
                </div>
            </div>

            <div class="field">
                <label for="password">Kata sandi</label>
                <div class="field__box">
                    <input type="password" name="password" id="password" class="has-toggle" required autocomplete="current-password" placeholder="Kata sandi kamu">
                    <button type="button" class="pwt" data-pw="password" aria-label="Lihat kata sandi" aria-pressed="false"><i class="ph-bold ph-eye"></i></button>
                </div>
            </div>

            <label class="check">
                <input type="checkbox" name="remember" id="remember">
                <span>Ingat sesi saya di perangkat ini</span>
            </label>

            <button type="submit" class="btn btn--primary btn--lg">Masuk ke Mindora <i class="ph-bold ph-arrow-up-right"></i></button>
        </form>

        <p class="auth__alt">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
    </div>
@endsection
