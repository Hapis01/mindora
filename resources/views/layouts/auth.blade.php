<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#07061b">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mindora') }} | @yield('title')</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/logo-icon-192.png') }}">

    <script>if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('js');</script>
    @vite(['resources/css/landing.css', 'resources/js/auth.js'])
</head>
<body>
<div class="page">
    <header class="nav" role="banner">
        <a href="{{ route('home') }}" class="nav__brand" aria-label="Mindora, ke beranda">
            <span class="nav__logo"><img src="{{ asset('images/logo-icon-64.png') }}" alt="" width="34" height="34"></span>
            <span class="nav__name">Mindora</span>
        </a>
        <nav class="nav__links" aria-label="Navigasi">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('home') }}#coba">Coba soal</a>
            <a href="{{ route('home') }}#konsultasi">Konsultasi</a>
        </nav>
        <div class="nav__actions">
            @yield('nav-action')
        </div>
    </header>

    <main class="auth">
        <div class="hero__bg"><img src="{{ asset('images/landing/logo-ghost.webp') }}" alt="" width="720" height="720"></div>
        <div class="wrap auth__grid">
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
