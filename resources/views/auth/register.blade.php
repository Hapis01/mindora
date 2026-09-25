@extends('layouts.app')
@section('title', 'Daftar Akun Baru')

@section('content')
<div class="min-h-[calc(100vh-160px)] flex items-center justify-center px-4 py-6 sm:py-12">
    <div class="w-full max-w-md space-y-5 sm:space-y-6">

        {{-- Header --}}
        <div class="text-center space-y-3 sm:space-y-4">
            <div class="flex justify-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden shadow-xl transition-transform hover:scale-105" style="background:white;padding:3px;box-shadow:0 0 35px rgba(124,58,237,0.4);">
                    <img src="{{ asset('images/logo-icon-192.png') }}" alt="Mindora" class="w-full h-full object-contain">
                </div>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Buat Akun Mindora</h1>
                <p class="text-xs sm:text-sm mt-1" style="color:#94a3b8;">Mulai eksplorasi psikometri dan potensi diri Anda hari ini</p>
            </div>
        </div>

        {{-- Card --}}
        <div class="p-6 sm:p-8 rounded-2xl sm:rounded-3xl space-y-5"
             style="background:rgba(255,255,255,0.03);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(124,58,237,0.25);box-shadow:0 10px 40px rgba(0,0,0,0.45), 0 0 25px rgba(124,58,237,0.12);"
             x-data="{
                 showPassword: false,
                 showConfirmPassword: false,
                 password: '',
                 get score() {
                     let s = 0;
                     if (this.password.length >= 8) s += 1;
                     if (/[A-Z]/.test(this.password) && /[a-z]/.test(this.password)) s += 1;
                     if (/[0-9]/.test(this.password)) s += 1;
                     if (/[^A-Za-z0-9]/.test(this.password)) s += 1;
                     return s;
                 },
                 get strengthLabel() {
                     if (!this.password) return '';
                     if (this.score <= 1) return 'Lemah';
                     if (this.score <= 3) return 'Sedang';
                     return 'Sangat Kuat';
                 },
                 get strengthColor() {
                     if (this.score <= 1) return '#f87171';
                     if (this.score <= 3) return '#fbbf24';
                     return '#34d399';
                 }
             }">

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div class="space-y-1.5">
                    <label for="name" class="label text-xs sm:text-sm font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <span>Nama Lengkap / Panggilan</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                        class="input-field text-sm sm:text-base py-3 px-3.5 rounded-xl transition-all"
                        placeholder="Contoh: Muhammad Hafiz">
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="label text-xs sm:text-sm font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        <span>Alamat Email</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="input-field text-sm sm:text-base py-3 px-3.5 rounded-xl transition-all"
                        placeholder="nama@email.com">
                </div>

                {{-- Tanggal Lahir (Faktor Dekripsi AES-256) --}}
                <div class="space-y-1.5">
                    <label for="date_of_birth" class="label text-xs sm:text-sm font-semibold flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>Tanggal Lahir</span>
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-medium" style="background:rgba(124,58,237,0.15);color:#c4b5fd;border:1px solid rgba(124,58,237,0.3);">
                            Kunci Dekripsi AES-256
                        </span>
                    </label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                        max="{{ date('Y-m-d') }}"
                        class="input-field text-sm sm:text-base py-3 px-3.5 rounded-xl transition-all"
                        style="color-scheme: dark;">
                    <p class="text-[10px] sm:text-[11px] leading-tight" style="color:#94a3b8;">
                        Wajib diisi sebagai verifikasi lapis kedua (2FA) saat membuka & mendekripsi profil terenkripsi Anda.
                    </p>
                    @error('date_of_birth')
                        <p class="text-xs text-rose-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password with Show/Hide Toggle & Strength Meter --}}
                <div class="space-y-1.5">
                    <label for="password" class="label text-xs sm:text-sm font-semibold flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span>Kata Sandi</span>
                        </span>
                        <span x-show="password.length > 0" x-text="strengthLabel" :style="{ color: strengthColor }" class="text-[11px] font-bold"></span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               name="password"
                               id="password"
                               x-model="password"
                               required
                               class="input-field text-sm sm:text-base py-3 pl-3.5 pr-11 rounded-xl transition-all"
                               placeholder="Minimal 8 karakter kombinasi">
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white rounded-lg transition-colors touch-feedback"
                                aria-label="Lihat kata sandi">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>

                    {{-- Dynamic Password Strength Bar --}}
                    <div x-show="password.length > 0" x-cloak class="pt-1 space-y-1.5">
                        <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden flex gap-1">
                            <div class="h-full rounded-full transition-all duration-300 flex-1"
                                 :style="{ background: score >= 1 ? strengthColor : 'transparent' }"></div>
                            <div class="h-full rounded-full transition-all duration-300 flex-1"
                                 :style="{ background: score >= 2 ? strengthColor : 'transparent' }"></div>
                            <div class="h-full rounded-full transition-all duration-300 flex-1"
                                 :style="{ background: score >= 3 ? strengthColor : 'transparent' }"></div>
                            <div class="h-full rounded-full transition-all duration-300 flex-1"
                                 :style="{ background: score >= 4 ? strengthColor : 'transparent' }"></div>
                        </div>
                        <div class="flex flex-wrap gap-2 text-[10px] text-slate-400">
                            <span :class="password.length >= 8 ? 'text-emerald-400' : 'text-slate-500'">✓ Min. 8 Karakter</span>
                            <span :class="/[A-Z]/.test(password) && /[a-z]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">✓ Huruf Besar &amp; Kecil</span>
                            <span :class="/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">✓ Angka / Simbol</span>
                        </div>
                    </div>
                </div>

                {{-- Confirm Password with Show/Hide Toggle --}}
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="label text-xs sm:text-sm font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <span>Konfirmasi Kata Sandi</span>
                    </label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'"
                               name="password_confirmation"
                               id="password_confirmation"
                               required
                               class="input-field text-sm sm:text-base py-3 pl-3.5 pr-11 rounded-xl transition-all"
                               placeholder="Ketik ulang kata sandi">
                        <button type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white rounded-lg transition-colors touch-feedback"
                                aria-label="Lihat konfirmasi kata sandi">
                            <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Touch-friendly Terms Checkbox --}}
                <div class="pt-1">
                    <label class="flex items-start gap-3 p-3 rounded-xl bg-white/[0.02] border border-purple-500/20 hover:border-purple-500/40 transition-colors cursor-pointer touch-feedback">
                        <input type="checkbox" name="terms" value="1" required
                            class="mt-1 w-4 h-4 rounded cursor-pointer transition-colors flex-shrink-0"
                            style="accent-color:#7c3aed;">
                        <span class="text-xs leading-relaxed text-slate-300 select-none">
                            Saya menyetujui Ketentuan Layanan Mindora sebagai instrumen
                            <strong class="text-purple-300 font-semibold">self-assessment non-klinis</strong>
                            dengan perlindungan privasi data terenkripsi.
                        </span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full btn-primary py-3.5 rounded-xl text-sm font-bold justify-center touch-feedback shadow-lg mt-2">
                    <span>Daftar &amp; Mulai Tes</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

        </div>

        {{-- Login Link --}}
        <p class="text-center text-xs sm:text-sm text-slate-400">
            Sudah memiliki akun Mindora?
            <a href="{{ route('login') }}" class="font-bold ml-1 text-purple-300 hover:text-purple-200 transition-colors inline-flex items-center gap-1 touch-feedback">
                <span>Masuk di sini</span>
                <span>→</span>
            </a>
        </p>
    </div>
</div>
@endsection