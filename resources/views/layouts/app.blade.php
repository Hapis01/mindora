<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#07061b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Mindora — Tes psikometri berbasis AI untuk memahami diri Anda lebih dalam melalui penilaian kognitif dan kepribadian yang akurat.">

    <title>{{ config('app.name', 'Mindora') }} — @yield('title', 'Kenali Dirimu Lebih Dalam')</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/logo-icon-64.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/logo-icon-192.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink: #07061b;
            --accent: #a99bff;
            --line: rgba(190, 184, 255, 0.14);
            --ease: cubic-bezier(0.22, 1, 0.36, 1);
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans Variable', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Bricolage Grotesque Variable', 'Plus Jakarta Sans Variable', system-ui, sans-serif; letter-spacing: -0.02em; }
        ::selection { background: rgba(169, 155, 255, 0.35); color: #fff; }
        :focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; border-radius: 6px; }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--ink); }
        ::-webkit-scrollbar-thumb { background: #3b3580; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent); }

        .glass {
            background: rgba(13, 11, 44, 0.55);
            backdrop-filter: blur(18px) saturate(150%);
            -webkit-backdrop-filter: blur(18px) saturate(150%);
            border: 1px solid var(--line);
        }
        .glass-strong {
            background: rgba(29, 24, 89, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(169, 155, 255, 0.2);
        }
        .nav-pill {
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(13, 11, 44, 0.6);
            backdrop-filter: blur(18px) saturate(150%);
            -webkit-backdrop-filter: blur(18px) saturate(150%);
        }

        .text-mindora { color: var(--accent); }
        .text-mindora-subtle { color: #cfc8ff; }

        .glow-violet { box-shadow: 0 0 40px -10px rgba(169, 155, 255, 0.5); }
        .glow-violet-sm { box-shadow: 0 0 18px -6px rgba(169, 155, 255, 0.5); }
        .glow-cyan { box-shadow: 0 0 20px -6px rgba(169, 155, 255, 0.5); }

        .card-hover { transition: transform 0.45s var(--ease), border-color 0.3s ease; }
        .card-hover:hover { transform: translateY(-3px); border-color: rgba(169, 155, 255, 0.4) !important; }

        .btn-mindora {
            background: #f2f0ff;
            color: #0c0a2a;
            transition: transform 0.35s var(--ease), box-shadow 0.3s, background-color 0.3s;
        }
        .btn-mindora:hover { background: #fff; box-shadow: 0 10px 40px -12px rgba(169, 155, 255, 0.65); }

        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 2px;
            background: var(--accent);
            transition: width 0.35s var(--ease);
            border-radius: 1px;
        }
        .nav-link:hover::after { width: 100%; }

        main { animation: fadeInUp 0.6s var(--ease); }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: -1;
            animation: orbFloat 14s ease-in-out infinite;
        }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -15px) scale(1.05); }
            66% { transform: translate(-10px, 10px) scale(0.95); }
        }
        @media (prefers-reduced-motion: reduce) {
            main, .orb { animation: none; }
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-navy-950 text-slate-100 antialiased tap-highlight-none overflow-x-hidden"
      style="background: #07061b;"
      x-data="{ mobileMenuOpen: false }">

    <!-- Ambient Orbs -->
    <div class="orb" style="width:500px;height:500px;top:-150px;left:-100px;background:rgba(169,155,255,0.12);animation-delay:0s;"></div>
    <div class="orb" style="width:400px;height:400px;top:40%;right:-120px;background:rgba(120,110,230,0.10);animation-delay:4s;"></div>
    <div class="orb" style="width:350px;height:350px;bottom:-100px;left:30%;background:rgba(169,155,255,0.08);animation-delay:8s;"></div>

    <!-- Navigation Bar -->
    <header class="sticky top-3 z-40 px-3 sm:px-4">
        <div class="max-w-6xl mx-auto nav-pill">
            <div class="flex items-center justify-between h-14 pl-2 pr-1.5">

                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group touch-feedback">
                        <!-- Logo Image -->
                        <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 transition-transform duration-500 group-hover:scale-105" style="background:white;padding:3px;">
                            <img src="{{ asset('images/logo-icon-192.png') }}" alt="Mindora Logo" class="w-full h-full object-contain">
                        </div>
                        <!-- Brand Name -->
                        <span class="text-xl font-bold tracking-tight" style="font-family:'Bricolage Grotesque Variable',sans-serif;">
                            <span class="text-white">Mindora</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation Links (Visible on md+) -->
                <nav class="hidden md:flex items-center gap-2">
                    <a href="{{ route('home') }}" class="nav-link text-sm font-medium {{ request()->routeIs('home') ? 'text-white font-semibold' : 'text-slate-300' }} hover:text-white transition-colors px-3 py-1.5 rounded-lg">
                        Beranda
                    </a>
                    @auth
                        <a href="{{ route('assessments.index') }}" class="nav-link text-sm font-medium {{ request()->routeIs('assessments.*') ? 'text-white font-semibold' : 'text-slate-300' }} hover:text-white transition-colors px-3 py-1.5 rounded-lg">
                            Katalog Tes
                        </a>
                        <a href="{{ route('dashboard') }}" class="nav-link text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-white font-semibold' : 'text-slate-300' }} hover:text-white transition-colors px-3 py-1.5 rounded-lg">
                            Dashboard
                        </a>
                        <a href="{{ route('profile.show') }}" class="nav-link text-sm font-medium {{ request()->routeIs('profile.*') ? 'text-white font-semibold' : 'text-slate-300' }} hover:text-white transition-colors flex items-center gap-1.5 px-3 py-1.5 rounded-lg">
                            <svg class="w-4 h-4" style="color:#a894ff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Profil
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-1" style="background:rgba(169,155,255,0.12);color:#cfc8ff;border:1px solid rgba(169,155,255,0.3);" onmouseover="this.style.background='rgba(169,155,255,0.22)'" onmouseout="this.style.background='rgba(169,155,255,0.12)'">
                                <span>⚙</span>
                                <span>Admin</span>
                            </a>
                        @endif

                        <!-- User Avatar & Logout -->
                        <div class="flex items-center gap-3 pl-3 border-l" style="border-color:rgba(124,58,237,0.2);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-md" style="background:#f2f0ff;color:#0c0a2a;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="text-xs text-slate-300 font-medium max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200 touch-feedback" style="background:rgba(255,255,255,0.05);color:#94a3b8;border:1px solid rgba(255,255,255,0.1);" onmouseover="this.style.background='rgba(239,68,68,0.15)';this.style.color='#fca5a5';this.style.borderColor='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#94a3b8';this.style.borderColor='rgba(255,255,255,0.1)'">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-link text-sm font-medium text-slate-300 hover:text-white transition-colors px-3 py-1.5">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-mindora text-sm font-semibold px-4 py-2 rounded-full touch-feedback flex items-center gap-1.5">
                            <span>Mulai Gratis</span>
                            <span>→</span>
                        </a>
                    @endauth
                </nav>

                <!-- Mobile Action & Hamburger Button (Visible on < md) -->
                <div class="flex md:hidden items-center gap-2">
                    @auth
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-1.5 px-2 py-1 rounded-full bg-white/5 border border-purple-500/30 touch-feedback">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm" style="background:#f2f0ff;color:#0c0a2a;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-xs text-slate-300 max-w-[70px] truncate font-medium">{{ explode(' ', auth()->user()->name)[0] }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold px-2.5 py-1.5 rounded-lg text-slate-200 bg-white/5 border border-white/10 touch-feedback">
                            Masuk
                        </a>
                    @endauth

                    <!-- Hamburger Button -->
                    <button type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="p-2 rounded-xl text-slate-300 hover:text-white focus:outline-none transition-colors touch-feedback"
                            style="background:rgba(255,255,255,0.05);border:1px solid rgba(190,184,255,0.2);"
                            aria-label="Buka Menu Navigasi">
                        <!-- Animated hamburger icon -->
                        <div class="w-5 h-4 flex flex-col justify-between items-center relative">
                            <span class="w-full h-0.5 bg-current rounded-full transition-all duration-300 origin-left"
                                  :class="mobileMenuOpen ? 'rotate-45 translate-x-0.5 -translate-y-0.5' : ''"></span>
                            <span class="w-full h-0.5 bg-current rounded-full transition-all duration-300"
                                  :class="mobileMenuOpen ? 'opacity-0' : 'opacity-100'"></span>
                            <span class="w-full h-0.5 bg-current rounded-full transition-all duration-300 origin-left"
                                  :class="mobileMenuOpen ? '-rotate-45 translate-x-0.5 translate-y-0.5' : ''"></span>
                        </div>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <!-- Slide-out Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen"
         x-cloak
         class="fixed inset-0 z-50 md:hidden"
         style="display: none;">
        
        <!-- Backdrop overlay -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 mobile-drawer-backdrop"></div>

        <!-- Drawer Panel -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed right-0 top-0 bottom-0 w-4/5 max-w-sm mobile-drawer-sheet flex flex-col justify-between p-6 overflow-y-auto">
            
            <div class="space-y-6">
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-4 border-b border-purple-500/20">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 bg-white p-0.5 shadow-md">
                            <img src="{{ asset('images/logo-icon-192.png') }}" alt="Mindora" class="w-full h-full object-contain">
                        </div>
                        <span class="text-lg font-bold" style="font-family:'Bricolage Grotesque Variable',sans-serif;">
                            <span class="text-white">Mindora</span>
                        </span>
                    </div>
                    <button type="button"
                            @click="mobileMenuOpen = false"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white bg-white/5 border border-white/10 touch-feedback"
                            aria-label="Tutup Menu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- User Info Card in Drawer -->
                @auth
                    <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-purple-500/25 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-base font-bold text-white shadow-lg flex-shrink-0" style="background:#f2f0ff;color:#0c0a2a;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-sm text-white truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono truncate">{{ auth()->user()->masked_email }}</div>
                            <div class="mt-1 flex items-center gap-1.5">
                                @if(auth()->user()->isAdmin())
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-purple-500/15 text-purple-300 border border-purple-500/30">
                                        Admin
                                    </span>
                                @else
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        Member Terverifikasi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-900/30 to-blue-900/20 border border-purple-500/30 space-y-3">
                        <div class="text-sm font-bold text-white">Selamat Datang di Mindora</div>
                        <p class="text-xs text-slate-300 leading-relaxed">Ikuti tes psikometri berbasis AI untuk mengenali kepribadian &amp; potensi penalaran Anda.</p>
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <a href="{{ route('login') }}" class="text-center py-2 px-3 rounded-xl text-xs font-semibold bg-white/10 text-white border border-white/15 touch-feedback">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="text-center py-2 px-3 rounded-full text-xs font-semibold btn-mindora touch-feedback">
                                Daftar Gratis
                            </a>
                        </div>
                    </div>
                @endauth

                <!-- Drawer Navigation Links -->
                <div class="space-y-1.5">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1">Navigasi Utama</div>

                    <a href="{{ route('home') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('home') ? 'bg-purple-600/20 text-purple-200 border border-purple-500/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} touch-feedback">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Beranda Utama</span>
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-purple-600/20 text-purple-200 border border-purple-500/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} touch-feedback">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>Dashboard Saya</span>
                        </a>

                        <a href="{{ route('assessments.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('assessments.*') ? 'bg-purple-600/20 text-purple-200 border border-purple-500/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} touch-feedback">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>Katalog Asesmen</span>
                        </a>

                        <a href="{{ route('profile.show') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('profile.*') ? 'bg-purple-600/20 text-purple-200 border border-purple-500/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} touch-feedback">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Profil &amp; Data Pribadi</span>
                        </a>

                        <a href="{{ route('ai.conversation') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('ai.*') ? 'bg-purple-600/20 text-purple-200 border border-purple-500/40' : 'text-slate-300 hover:bg-white/5 hover:text-white' }} touch-feedback">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <span>Asisten AI Gemini</span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-purple-300 bg-purple-500/10 border border-purple-500/25 touch-feedback">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Panel Admin</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Drawer Bottom Action -->
            <div class="pt-6 border-t border-purple-500/20">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold flex items-center justify-center gap-2 text-rose-300 bg-rose-500/10 border border-rose-500/25 hover:bg-rose-500/20 transition-all touch-feedback">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                @else
                    <div class="text-center text-[11px] text-slate-500">
                        Mindora &bull; Platform Psikometri AI Terenkripsi
                    </div>
                @endauth
            </div>

        </div>
    </div>

    <!-- Floating Mobile Bottom Navigation Dock (Visible on mobile < md) -->
    <nav class="fixed bottom-0 inset-x-0 z-30 md:hidden mobile-bottom-dock px-3 py-1.5 safe-area-pb"
         aria-label="Navigasi Bawah">
        <div class="max-w-md mx-auto grid grid-cols-4 items-center text-center">
            
            <!-- Tab 1: Beranda -->
            <a href="{{ route('home') }}"
               class="flex flex-col items-center justify-center py-1.5 rounded-xl transition-all touch-feedback {{ request()->routeIs('home') ? 'text-purple-300 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    @if(request()->routeIs('home'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-purple-400"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1">Beranda</span>
            </a>

            <!-- Tab 2: Asesmen -->
            <a href="{{ route('assessments.index') }}"
               class="flex flex-col items-center justify-center py-1.5 rounded-xl transition-all touch-feedback {{ request()->routeIs('assessments.*') ? 'text-cyan-300 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    @if(request()->routeIs('assessments.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-cyan-400"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1">Asesmen</span>
            </a>

            <!-- Tab 3: Dashboard / Masuk -->
            @auth
                <a href="{{ route('dashboard') }}"
                   class="flex flex-col items-center justify-center py-1.5 rounded-xl transition-all touch-feedback {{ request()->routeIs('dashboard') ? 'text-purple-300 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        @if(request()->routeIs('dashboard'))
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-purple-400"></span>
                        @endif
                    </div>
                    <span class="text-[10px] mt-1">Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="flex flex-col items-center justify-center py-1.5 rounded-xl transition-all touch-feedback {{ request()->routeIs('login') ? 'text-purple-300 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        @if(request()->routeIs('login'))
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-purple-400"></span>
                        @endif
                    </div>
                    <span class="text-[10px] mt-1">Masuk</span>
                </a>
            @endauth

            <!-- Tab 4: Profil / Daftar -->
            @auth
                <a href="{{ route('profile.show') }}"
                   class="flex flex-col items-center justify-center py-1.5 rounded-xl transition-all touch-feedback {{ request()->routeIs('profile.*') ? 'text-purple-300 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @if(request()->routeIs('profile.*'))
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-purple-400"></span>
                        @endif
                    </div>
                    <span class="text-[10px] mt-1">Profil</span>
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="flex flex-col items-center justify-center py-1.5 rounded-xl transition-all touch-feedback {{ request()->routeIs('register') ? 'text-purple-300 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        @if(request()->routeIs('register'))
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-purple-400"></span>
                        @endif
                    </div>
                    <span class="text-[10px] mt-1">Daftar</span>
                </a>
            @endauth

        </div>
    </nav>

    <!-- Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('success'))
            <div class="p-3.5 sm:p-4 mb-4 rounded-xl flex items-center gap-3 shadow-lg animate-slide-up" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);color:#6ee7b7;">
                <svg class="w-5 h-5 flex-shrink-0" style="color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-3.5 sm:p-4 mb-4 rounded-xl flex items-center gap-3 shadow-lg" style="background:rgba(124,58,237,0.12);border:1px solid rgba(124,58,237,0.3);color:#c4b5fd;">
                <svg class="w-5 h-5 flex-shrink-0" style="color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-xs sm:text-sm font-medium">{{ session('info') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 sm:p-4 mb-4 rounded-xl shadow-lg" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5;">
                <div class="flex items-center gap-2 mb-2 font-semibold text-xs sm:text-sm" style="color:#f87171;">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Terjadi kesalahan:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow pb-16 md:pb-6">
        @yield('content')
    </main>

    @hasSection('footer')
        @yield('footer')
    @else
        <!-- Footer -->
        <footer class="mt-16 sm:mt-20 text-slate-500 text-xs pb-24 md:pb-10" style="border-top:1px solid rgba(190,184,255,0.14);background:#07061b;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0" style="background:white;padding:3px;">
                            <img src="{{ asset('images/logo-icon-192.png') }}" alt="Mindora" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="font-bold text-slate-200 text-sm tracking-wide">Mindora</span>
                            <span class="text-slate-600 mx-2 hidden sm:inline">·</span>
                            <p class="text-slate-400 text-xs sm:inline">Platform Psikometri &amp; Personal Insight AI</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-3 text-center sm:text-left" style="border-top:1px solid rgba(190,184,255,0.1);">
                    <p style="color:#64748b;">© {{ date('Y') }} Mindora. Seluruh hak cipta dilindungi.</p>
                    <div class="flex flex-wrap justify-center gap-3 sm:gap-4 text-slate-500 text-[11px]">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Privasi Terlindungi</span>
                        <span>·</span>
                        <span>Zero-PII ML Engine</span>
                        <span>·</span>
                        <span>Enkripsi End-to-End</span>
                    </div>
                </div>
            </div>
        </footer>
    @endif

    @stack('scripts')

    {{-- Disclaimer Dialog --}}
    <dialog id="disclaimer" class="disclaimer" aria-label="Disclaimer Mindora">
        <button type="button" id="disclaimer-close" class="disclaimer__close" aria-label="Tutup"><i class="ph-bold ph-x"></i></button>
        <img src="{{ asset('images/notif.png') }}" alt="Disclaimer Mindora" loading="eager" decoding="async">
    </dialog>
    <script>
        (function () {
            const disclaimer = document.getElementById('disclaimer');
            if (!disclaimer) return;
            document.querySelectorAll('[data-open-disclaimer]').forEach(btn => {
                btn.addEventListener('click', () => disclaimer.showModal());
            });
            const closeBtn = document.getElementById('disclaimer-close');
            if (closeBtn) closeBtn.addEventListener('click', () => disclaimer.close());
            disclaimer.addEventListener('click', (e) => {
                if (e.target === disclaimer) disclaimer.close();
            });
        })();
    </script>
</body>
</html>