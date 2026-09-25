@extends('layouts.app')
@section('title', 'Masuk ke Akun Anda')

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
                <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-xs sm:text-sm mt-1" style="color:#94a3b8;">Masuk ke akun Mindora Anda untuk melanjutkan asesmen</p>
            </div>
        </div>

        {{-- Card --}}
        <div class="p-6 sm:p-8 rounded-2xl sm:rounded-3xl space-y-5"
             style="background:rgba(255,255,255,0.03);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(124,58,237,0.25);box-shadow:0 10px 40px rgba(0,0,0,0.45), 0 0 25px rgba(124,58,237,0.12);"
             x-data="{ showPassword: false }">

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="label text-xs sm:text-sm font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        <span>Alamat Email</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="input-field text-sm sm:text-base py-3 px-3.5 rounded-xl transition-all"
                        placeholder="nama@email.com">
                </div>

                {{-- Password with Show/Hide Toggle --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="label text-xs sm:text-sm font-semibold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span>Kata Sandi</span>
                        </label>
                    </div>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               name="password"
                               id="password"
                               required
                               class="input-field text-sm sm:text-base py-3 pl-3.5 pr-11 rounded-xl transition-all"
                               placeholder="••••••••">
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white rounded-lg transition-colors touch-feedback"
                                aria-label="Lihat kata sandi">
                            <!-- Eye Open -->
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <!-- Eye Closed -->
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2.5 py-1">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded cursor-pointer transition-colors"
                        style="accent-color:#7c3aed;">
                    <label for="remember" class="text-xs cursor-pointer select-none" style="color:#94a3b8;">
                        Ingat sesi saya di perangkat ini
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="w-full btn-primary py-3.5 rounded-xl text-sm font-bold justify-center touch-feedback shadow-lg mt-2">
                    <span>Masuk ke Mindora</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>
        </div>

        {{-- Register Link --}}
        <p class="text-center text-xs sm:text-sm text-slate-400">
            Belum memiliki akun Mindora?
            <a href="{{ route('register') }}" class="font-bold ml-1 text-purple-300 hover:text-purple-200 transition-colors inline-flex items-center gap-1 touch-feedback">
                <span>Daftar Gratis</span>
                <span>→</span>
            </a>
        </p>
    </div>
</div>
@endsection