@extends('layouts.auth')
@section('title', 'Daftar Akun Baru')

@section('nav-action')
    <a href="{{ route('login') }}" class="btn btn--primary btn--sm">Masuk</a>
@endsection

@section('content')
    <div>
        <h1 class="auth__title">
            <span class="hero__line"><span>Mulai sekarang,</span></span>
            <span class="hero__line"><span><em>jam berapa pun.</em></span></span>
        </h1>
        <p class="auth__lead" data-hero>Buat akun gratis, kerjakan tes kognitif dan Big Five dari ponselmu, lalu tanyakan hasilnya ke Asisten AI.</p>
        <ul class="auth__points" data-hero>
            <li><i class="ph-bold ph-device-mobile"></i><span><strong>Dari perangkat apa pun.</strong> Cukup browser, tanpa aplikasi tambahan.</span></li>
            <li><i class="ph-bold ph-lock-key"></i><span><strong>Data pribadi terenkripsi.</strong> Tanggal lahir menjadi kunci pembuka profilmu.</span></li>
            <li><i class="ph-bold ph-chat-circle-text"></i><span><strong>Konsultasi tanpa antre.</strong> Asisten AI aktif 24 jam.</span></li>
        </ul>
    </div>

    <div class="authcard" data-hero>
        <h2>Buat akun Mindora</h2>
        <p class="authcard__sub">Mulai eksplorasi potensi dirimu hari ini.</p>

        @if ($errors->any())
            <div class="alert alert--err" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="form">
            @csrf

            <div class="field">
                <label for="name">Nama lengkap atau panggilan</label>
                <div class="field__box">
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Contoh: Muhammad Hafiz">
                </div>
            </div>

            <div class="field">
                <label for="email">Alamat email</label>
                <div class="field__box">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com">
                </div>
            </div>

            <div class="field">
                <label for="date_of_birth">Tanggal lahir <small>Kunci dekripsi AES-256</small></label>
                <div class="field__box">
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required max="{{ date('Y-m-d') }}">
                </div>
                <p class="field__hint">Dipakai sebagai verifikasi lapis kedua saat membuka dan mendekripsi profil terenkripsimu.</p>
            </div>

            <div class="field">
                <label for="password">Kata sandi</label>
                <div class="field__box">
                    <input type="password" name="password" id="password" class="has-toggle" required autocomplete="new-password" placeholder="Minimal 8 karakter kombinasi">
                    <button type="button" class="pwt" data-pw="password" aria-label="Lihat kata sandi" aria-pressed="false"><i class="ph-bold ph-eye"></i></button>
                </div>
                <div class="strength" data-strength>
                    <div class="strength__bars" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
                    <div class="strength__row"><span>Min. 8 karakter, huruf besar dan kecil, angka atau simbol</span><b data-strength-label></b></div>
                </div>
            </div>

            <div class="field">
                <label for="password_confirmation">Konfirmasi kata sandi</label>
                <div class="field__box">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="has-toggle" required autocomplete="new-password" placeholder="Ketik ulang kata sandi">
                    <button type="button" class="pwt" data-pw="password_confirmation" aria-label="Lihat kata sandi" aria-pressed="false"><i class="ph-bold ph-eye"></i></button>
                </div>
            </div>

            <label class="check">
                <input type="checkbox" name="terms" value="1" required>
                <span>Saya menyetujui Ketentuan Layanan Mindora sebagai instrumen <strong>self-assessment non-klinis</strong> dengan perlindungan privasi data terenkripsi.</span>
            </label>

            <button type="submit" class="btn btn--primary btn--lg">Daftar dan mulai tes <i class="ph-bold ph-arrow-up-right"></i></button>
        </form>

        <p class="auth__alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
@endsection
