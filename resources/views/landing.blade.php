<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#07061b">
    <meta name="description" content="Mindora: psikotes online yang bisa dikerjakan kapan saja, dianalisis machine learning, dan dikonsultasikan ke Asisten AI di mana saja.">
    <title>{{ config('app.name', 'Mindora') }} | Psikotes Online Kapan Saja, Konsultasi AI Di Mana Saja</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/logo-icon-192.png') }}">

    <script>if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('js');</script>
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>
<div class="page">

    {{-- ===================== NAV ===================== --}}
    <header class="nav" role="banner">
        <a href="#top" class="nav__brand" aria-label="Mindora, ke atas">
            <span class="nav__logo"><img src="{{ asset('images/logo-icon-64.png') }}" alt="" width="34" height="34"></span>
            <span class="nav__name">Mindora</span>
        </a>
        <nav class="nav__links" aria-label="Navigasi utama">
            <a href="#cara">Cara kerja</a>
            <a href="#coba">Coba soal</a>
            <a href="#mesin">Mesin ML</a>
            <a href="#konsultasi">Konsultasi</a>
            <a href="#faq">FAQ</a>
        </nav>
        <div class="nav__actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn--primary btn--sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="nav__login">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Mulai gratis</a>
            @endauth
            <button type="button" id="burger" class="nav__burger" aria-expanded="false" aria-controls="menu" aria-label="Buka menu"><i class="ph ph-list"></i></button>
        </div>
    </header>

    <div id="menu" class="menu" role="dialog" aria-label="Menu">
        <nav class="menu__links">
            <a href="#cara">Cara kerja</a>
            <a href="#coba">Coba soal</a>
            <a href="#mesin">Mesin ML</a>
            <a href="#konsultasi">Konsultasi</a>
            <a href="#faq">FAQ</a>
        </nav>
        <div class="menu__cta">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn--primary">Buka dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn--primary">Mulai gratis</a>
                <a href="{{ route('login') }}" class="btn btn--ghost">Masuk</a>
            @endauth
        </div>
    </div>

    <main id="top">

        {{-- ===================== HERO ===================== --}}
        <section class="hero">
            <div class="hero__bg"><img src="{{ asset('images/landing/logo-ghost.webp') }}" alt="" width="720" height="720" fetchpriority="high"></div>
            <div class="wrap hero__inner">
                <h1 class="hero__title">
                    <span class="hero__line"><span>Psikotes online,</span></span>
                    <span class="hero__line"><span><em>kapan saja</em> <span class="chip-img"><img src="{{ asset('images/landing/logo-pill.webp') }}" alt="" width="400" height="200"></span></span></span>
                    <span class="hero__line"><span>konsultasi di mana saja</span></span>
                    <span class="sr-only">Psikotes online kapan saja, konsultasi dengan AI di mana saja.</span>
                </h1>
                <p class="hero__sub" data-hero>Kerjakan tes kognitif dan kepribadian dari ponselmu, dibaca model machine learning, lalu tanyakan hasilnya ke Asisten AI kapan pun kamu butuh.</p>
                <div class="hero__cta" data-hero>
                    @auth
                        <a href="{{ route('assessments.index') }}" class="btn btn--primary btn--lg">Mulai tes baru <i class="ph-bold ph-arrow-up-right"></i></a>
                        <a href="{{ route('ai.conversation') }}" class="btn btn--ghost btn--lg">Tanya Asisten AI</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn--primary btn--lg">Mulai tes gratis <i class="ph-bold ph-arrow-up-right"></i></a>
                        <a href="#coba" class="btn btn--ghost btn--lg">Coba satu soal</a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- ===================== MANIFESTO ===================== --}}
        <section class="manifesto">
            <div class="wrap">
                <p class="manifesto__text">Memahami diri tidak harus menunggu jadwal, antre di ruang tes, atau menebak arti angka. Mindora membawa psikotes ke jam berapa pun kamu siap, menjelaskan alasan di balik setiap skor, dan menemanimu bertanya sampai hasilnya terasa masuk akal.</p>
            </div>
        </section>

        {{-- ===================== SEHARI BERSAMA MINDORA (PIN) ===================== --}}
        <section id="cara" class="day" aria-label="Cara kerja Mindora">
            <div class="day__stage">
                <div class="day__glow"></div>
                <div class="wrap day__inner">
                    <p class="day__title">Cara kerja Mindora, sepanjang hari</p>

                    <div class="day__body">
                        <div class="day__clock" data-clock aria-hidden="true">06:40</div>
                        <div class="day__scenes">
                            <article class="scene">
                                <span class="scene__time">06:40</span>
                                <p class="scene__place">Di rumah, sebelum berangkat</p>
                                <h3>Tes kognitif sambil menunggu kopi</h3>
                                <p>Tiga puluh soal logika, numerik, dan verbal. Berhenti kapan pun, jawabanmu tersimpan otomatis di sesi yang sama.</p>
                            </article>
                            <article class="scene">
                                <span class="scene__time">12:15</span>
                                <p class="scene__place">Jam istirahat siang</p>
                                <h3>Big Five selesai sebelum makan siang habis</h3>
                                <p>Pernyataan skala Likert, waktu respons tiap butir tercatat sendiri. Skormu langsung dihitung dan dibandingkan dengan norma populasi.</p>
                            </article>
                            <article class="scene">
                                <span class="scene__time">19:30</span>
                                <p class="scene__place">Di perjalanan pulang</p>
                                <h3>Baca laporan, lalu tanya AI soal yang bikin penasaran</h3>
                                <p>Kenapa skormu begini? Asisten AI menjawab dari angka hasilmu, bukan dari identitasmu.</p>
                            </article>
                            <article class="scene">
                                <span class="scene__time">23:10</span>
                                <p class="scene__place">Tengah malam, saat pikiran belum diam</p>
                                <h3>Konsultasi tidak menunggu jam kerja</h3>
                                <p>Ada pertanyaan soal arah karier atau gaya kerja jam sebelas malam? Asisten AI selalu terjaga.</p>
                            </article>
                        </div>
                    </div>

                    <div class="day__rail" aria-hidden="true">
                        <div class="rail"><div class="rail__fill"></div></div>
                        <ol><li>Pagi</li><li>Siang</li><li>Sore</li><li>Malam</li></ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== COBA SATU SOAL ===================== --}}
        <section id="coba" class="sec">
            <div class="wrap try">
                <div>
                    <h2 class="h2" data-split>Coba satu soal, rasakan alurnya</h2>
                    <p class="lead" data-r>Tanpa daftar. Jawabanmu tidak dikirim ke server, hanya demo tampilan cara Mindora mengukur ketepatan dan waktu responsmu.</p>
                    <div class="tabs" role="tablist" aria-label="Jenis soal" data-r>
                        <button type="button" role="tab" id="tab-cog" aria-selected="true" aria-controls="panel-cog">Kognitif</button>
                        <button type="button" role="tab" id="tab-per" aria-selected="false" aria-controls="panel-per" tabindex="-1">Kepribadian</button>
                    </div>
                </div>

                <div class="card" data-r>
                    {{-- Soal kognitif --}}
                    <div id="panel-cog" class="panel" role="tabpanel" aria-labelledby="tab-cog">
                        <div class="q__meta">Penalaran numerik</div>
                        <p class="q__text">Lanjutkan pola berikut: 2, 6, 12, 20, ...</p>
                        <div class="q__opts">
                            <button type="button" class="opt"><span class="opt__k">A</span>28</button>
                            <button type="button" class="opt" data-correct><span class="opt__k">B</span>30</button>
                            <button type="button" class="opt"><span class="opt__k">C</span>32</button>
                            <button type="button" class="opt"><span class="opt__k">D</span>36</button>
                        </div>
                        <div class="fb" data-fb>
                            <div class="fb__stats">
                                <div class="fb__stat"><strong data-fb-verdict>Tepat</strong><span>Hasil jawaban</span></div>
                                <div class="fb__stat"><strong><span data-fb-time>0</span> dtk</strong><span>Waktu respons</span></div>
                            </div>
                            <p class="fb__text">Selisih antar suku naik 4, 6, 8, 10, jadi suku berikutnya 30. Di tes sungguhan, ketepatan dan waktu respons menjadi fitur untuk model machine learning.</p>
                            <button type="button" class="fb__again">Ulangi <i class="ph-bold ph-arrow-counter-clockwise"></i></button>
                        </div>
                    </div>

                    {{-- Pernyataan kepribadian --}}
                    <div id="panel-per" class="panel" role="tabpanel" aria-labelledby="tab-per" hidden>
                        <div class="q__meta">Big Five, Conscientiousness</div>
                        <p class="q__text">Saya merasa tenang jika sudah menyusun rencana sebelum mulai bekerja.</p>
                        <div class="likert" role="group" aria-label="Skala 1 sampai 5">
                            <button type="button" data-v="1" aria-label="1, sangat tidak setuju">1</button>
                            <button type="button" data-v="2" aria-label="2">2</button>
                            <button type="button" data-v="3" aria-label="3, netral">3</button>
                            <button type="button" data-v="4" aria-label="4">4</button>
                            <button type="button" data-v="5" aria-label="5, sangat setuju">5</button>
                        </div>
                        <div class="likert__ends"><span>Sangat tidak setuju</span><span>Sangat setuju</span></div>
                        <div class="fb" data-fb>
                            <div class="fb__stats">
                                <div class="fb__stat"><strong><span data-fb-pos>0</span>%</strong><span>Posisi pada skala</span></div>
                            </div>
                            <div class="measure"><i></i></div>
                            <p class="fb__text">Jawaban ini diubah menjadi posisi pada dimensi Conscientiousness. Skor akhir baru terbentuk dari seluruh butir, bukan satu pernyataan.</p>
                            <button type="button" class="fb__again">Ulangi <i class="ph-bold ph-arrow-counter-clockwise"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================== MESIN ML (BENTO) ===================== --}}
        <section id="mesin" class="sec">
            <div class="wrap">
                <h2 class="h2" data-split>Mesin di balik hasilmu</h2>
                <p class="lead" data-r>Skor bukan sekadar angka. Machine learning menempatkan profilmu pada arketipe dan menunjukkan faktor mana yang paling menentukan.</p>

                <div class="bento">
                    <article class="card cell cell--radar" data-r>
                        <h3>Arketipe dari K-Means, divisualkan sebagai radar</h3>
                        <p>Pilih arketipe untuk melihat bentuk profil Big Five-nya.</p>
                        <div class="radar"><canvas id="radar" role="img" aria-label="Radar lima dimensi Big Five"></canvas></div>
                        <div class="arch" role="group" aria-label="Pilih arketipe">
                            <button type="button" aria-pressed="true">Analytical Achiever</button>
                            <button type="button" aria-pressed="false">Creative Explorer</button>
                            <button type="button" aria-pressed="false">Collaborative Connector</button>
                            <button type="button" aria-pressed="false">Independent Specialist</button>
                        </div>
                        <p class="arch__desc" id="arch-desc">Profil berorientasi pada pemikiran metodis, ketepatan data kuantitatif, dan eksekusi tugas yang sistematis.</p>
                    </article>

                    <article class="card cell cell--shap" data-r>
                        <h3>Alasan skormu bisa dijelaskan</h3>
                        <p>TreeSHAP menunjukkan fitur mana yang mendorong hasil naik atau turun.</p>
                        <ul class="shap">
                            <li class="shap__row"><span>Skor kognitif</span><span class="shap__area"><i class="shap__bar shap__bar--pos" style="--v:1"></i></span><span class="shap__val">+0,42</span></li>
                            <li class="shap__row"><span>Conscientiousness</span><span class="shap__area"><i class="shap__bar shap__bar--pos" style="--v:0.9"></i></span><span class="shap__val">+0,38</span></li>
                            <li class="shap__row"><span>Agreeableness</span><span class="shap__area"><i class="shap__bar shap__bar--pos" style="--v:0.52"></i></span><span class="shap__val">+0,22</span></li>
                            <li class="shap__row"><span>Extraversion</span><span class="shap__area"><i class="shap__bar shap__bar--neg" style="--v:0.12"></i></span><span class="shap__val">&minus;0,05</span></li>
                            <li class="shap__row"><span>Openness</span><span class="shap__area"><i class="shap__bar shap__bar--neg" style="--v:0.29"></i></span><span class="shap__val">&minus;0,12</span></li>
                        </ul>
                        <p class="note">Data demo anonim.</p>
                    </article>

                    <article class="card cell cell--ai" data-r>
                        <img class="cell__art" src="{{ asset('images/landing/logo-ghost.webp') }}" alt="" width="720" height="720" loading="lazy">
                        <h3>Asisten AI hanya melihat angka</h3>
                        <p>Nama, email, dan identitas lain tidak pernah dikirim ke model AI.</p>
                        <ul class="sees" aria-label="Contoh data yang dikirim ke AI">
                            <li><b>kognitif</b>: 72,5</li>
                            <li><b>conscientiousness</b>: 82</li>
                            <li><b>arketipe</b>: Analytical Achiever</li>
                            <li><b>nama, email</b>: tidak dikirim</li>
                        </ul>
                        <p class="note">Data pribadi tersimpan terenkripsi dan disensor di layar.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- ===================== KONSULTASI AI ===================== --}}
        <section id="konsultasi" class="sec">
            <div class="wrap consult">
                <div>
                    <h2 class="h2" data-split>Bertanya kapan saja, tanpa antre</h2>
                    <ul class="consult__points" data-r>
                        <li><i class="ph-bold ph-clock"></i><span><strong>Selalu tersedia.</strong> Pagi, tengah malam, atau akhir pekan, jawaban datang dalam hitungan detik.</span></li>
                        <li><i class="ph-bold ph-device-mobile"></i><span><strong>Dari perangkat mana pun.</strong> Browser ponsel atau laptop sudah cukup.</span></li>
                        <li><i class="ph-bold ph-chart-polar"></i><span><strong>Berdasar hasilmu.</strong> Jawaban merujuk skor dan alasan SHAP milikmu sendiri.</span></li>
                    </ul>
                </div>

                <div class="chat" data-r>
                    <div class="chat__head">
                        <span class="chat__avatar"><img src="{{ asset('images/logo-icon-64.png') }}" alt="" width="64" height="64"></span>
                        <div><strong>Asisten AI Mindora</strong><span>Aktif 24 jam</span></div>
                    </div>
                    <div class="chat__body">
                        <div class="bubble bubble--user" data-bub="u">Skor Openness saya rendah. Apa artinya untuk karier analis data?</div>
                        <div class="bubble bubble--ai" data-bub="a">
                            <span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
                            <span class="txt">Openness rendah berarti kamu cenderung nyaman dengan metode yang sudah teruji. Itu cocok untuk analisis yang butuh ketelitian. Kekuatanmu ada di Conscientiousness dan skor kognitif.</span>
                        </div>
                        <div class="bubble bubble--user" data-bub="u">Apa yang sebaiknya saya kembangkan?</div>
                        <div class="bubble bubble--ai" data-bub="a">
                            <span class="dots" aria-hidden="true"><i></i><i></i><i></i></span>
                            <span class="txt">Coba latih sisi kolaborasi: presentasikan temuan datamu ke tim non-teknis. Ini melatih komunikasi tanpa meninggalkan kekuatan analitismu.</span>
                        </div>
                    </div>
                    <div class="chat__foot">Percakapan contoh. Asisten AI bukan psikolog berlisensi dan tidak memberikan diagnosis.</div>
                </div>
            </div>
        </section>

        {{-- ===================== FAQ ===================== --}}
        <section id="faq" class="sec">
            <div class="wrap faq">
                <h2 class="h2" data-split>Pertanyaan yang sering muncul</h2>
                <div class="faq__list" data-r>
                    <details>
                        <summary>Apakah hasilnya setara tes psikologi resmi?<i class="ph-bold ph-plus"></i></summary>
                        <p>Tidak. Mindora adalah alat eksplorasi diri, bukan pengganti asesmen psikolog berlisensi atau dasar keputusan klinis.</p>
                    </details>
                    <details>
                        <summary>Apakah model machine learning-nya sudah divalidasi?<i class="ph-bold ph-plus"></i></summary>
                        <p>Model saat ini adalah model demonstrasi yang dilatih dari data normatif sintetis (K-Means untuk arketipe, Random Forest untuk klasifikasi). Gunakan hasilnya sebagai gambaran, bukan vonis.</p>
                    </details>
                    <details>
                        <summary>Data apa yang dilihat Asisten AI?<i class="ph-bold ph-plus"></i></summary>
                        <p>Hanya skor numerik dan label arketipe. Identitas pribadi seperti nama dan email tidak dikirim ke model AI.</p>
                    </details>
                    <details>
                        <summary>Bisa dikerjakan dari ponsel?<i class="ph-bold ph-plus"></i></summary>
                        <p>Bisa. Semuanya berjalan di browser, tanpa aplikasi tambahan. Satu sesi memakan sekitar 15 menit.</p>
                    </details>
                    <details>
                        <summary>Berapa biayanya?<i class="ph-bold ph-plus"></i></summary>
                        <p>Pendaftaran dan pengerjaan tes saat ini gratis.</p>
                    </details>
                </div>
            </div>
        </section>

        {{-- ===================== CTA ===================== --}}
        <section class="cta">
            <div class="wrap">
                <h2 class="cta__title" data-split>Mulai sekarang,<br>jam berapa pun.</h2>
                <div class="cta__btns">
                    @auth
                        <a href="{{ route('assessments.index') }}" class="btn btn--primary btn--lg">Pilih tes <i class="ph-bold ph-arrow-up-right"></i></a>
                        <a href="{{ route('dashboard') }}" class="btn btn--ghost btn--lg">Buka dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn--primary btn--lg">Daftar gratis <i class="ph-bold ph-arrow-up-right"></i></a>
                        <a href="{{ route('login') }}" class="btn btn--ghost btn--lg">Masuk ke akun</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="foot">
        <div class="wrap">
            <div class="foot__grid">
                <div>
                    <div class="foot__brand"><span class="nav__logo"><img src="{{ asset('images/logo-icon-64.png') }}" alt="" width="38" height="38"></span>Mindora</div>
                    <p class="foot__about">Platform psikometri online dengan analitik machine learning dan Asisten AI. Hanya untuk eksplorasi diri, bukan diagnosis psikologis.</p>
                </div>
                <div class="foot__links">
                    <a href="#cara">Cara kerja</a>
                    <a href="#mesin">Mesin ML</a>
                    <a href="#konsultasi">Konsultasi</a>
                    <a href="{{ asset('docs/Dokumentasi_Sistem_Mindora_Metode_ML_Security.pdf') }}" target="_blank" rel="noopener">Dokumentasi</a>
                    <button type="button" data-open-disclaimer>Disclaimer</button>
                </div>
            </div>
            <div class="foot__word" aria-hidden="true">mindora</div>
            <p class="foot__copy">&copy; {{ date('Y') }} Mindora.</p>
        </div>
    </footer>
</div>

{{-- Disclaimer --}}
<dialog id="disclaimer" class="disclaimer" aria-label="Disclaimer Mindora">
    <button type="button" id="disclaimer-close" class="disclaimer__close" aria-label="Tutup"><i class="ph-bold ph-x"></i></button>
    <img src="{{ asset('images/notif.png') }}" alt="Disclaimer Mindora" loading="eager" decoding="async">
</dialog>

@foreach (['success', 'info'] as $flash)
    @if (session($flash))
        <div id="toast" class="toast" role="status"><i class="ph-bold ph-check-circle"></i>{{ session($flash) }}</div>
    @endif
@endforeach
</body>
</html>
