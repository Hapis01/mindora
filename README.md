<p align="center">
  <img src="public/images/logo-mark-512.png" alt="Mindora Logo" width="120">
</p>

<h1 align="center">Mindora</h1>
<p align="center">
  <strong>Platform Psikometri Online &amp; AI Assessment</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL%20%7C%20MariaDB-5.7%2F10.4%2B-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="MIT License">
</p>

---

## Apa itu Mindora?

**Mindora** adalah platform tes psikometri (psikotes) online yang membantu kamu mengenali potensi diri melalui data dan kecerdasan buatan (AI).

Fitur utama:
- **Tes Kognitif** — Mengukur kemampuan penalaran logis, numerik, dan verbal
- **Tes Kepribadian Big Five (OCEAN)** — Mengevaluasi 5 dimensi kepribadian: Openness, Conscientiousness, Extraversion, Agreeableness, Neuroticism
- **AI Assistant** — Konsultasi hasil tes dengan asisten AI yang memahami profil psikometrimu secara privat
- **Dashboard Interaktif** — Visualisasi skor dengan grafik radar dan statistik
- **Panel Admin** — Kelola soal, pengguna, model ML, dan audit log
- **Keamanan Berlapis** — Enkripsi data pribadi (AES-256-GCM), hashing Argon2id, RBAC, rate limiting, security headers

### Desain & Pengalaman Pengguna

Seluruh antarmuka Mindora memakai **satu sistem desain** yang konsisten dari landing page sampai dashboard:

| Aspek | Detail |
|-------|--------|
| **Tema** | Gelap (dark navy `#07061b`) dengan satu aksen lilac `#a99bff` di seluruh situs |
| **Tipografi** | Bricolage Grotesque (judul), Plus Jakarta Sans (teks), Geist Mono (angka/kode) |
| **Ikon** | Phosphor Icons |
| **Animasi** | GSAP + Lenis smooth scroll + SplitText di landing page |
| **Radius** | Seragam: 24px kartu, 14px blok dalam, pil penuh untuk tombol |
| **Responsif** | Mobile-first, target sentuh minimal 40px, tanpa overflow horizontal di 390px |
| **Aksesibilitas** | Dukungan `prefers-reduced-motion`, focus ring lilac, ARIA label |

---

## Screenshot

| Halaman | Deskripsi |
|---------|-----------|
| **Landing Page** | Hero dengan animasi GSAP, penjelasan cara kerja, mesin ML, konsultasi AI, dan FAQ |
| **Dashboard** | Ringkasan asesmen, kartu menu utama, dan panduan memulai |
| **Katalog Tes** | Daftar instrumen psikometri aktif dengan durasi dan jumlah soal |
| **Hasil Asesmen** | Grafik radar kepribadian, skor kognitif, dan insight machine learning |
| **Asisten AI** | Konsultasi hasil tes secara privat dengan Gemini |
| **Panel Admin** | Statistik platform, kelola soal, pengguna, model ML, dan audit log |

> Semua halaman memakai tema gelap dengan aksen lilac yang sama, responsif dari layar 390px ke atas.

---

## Daftar Isi

- [Apa itu Mindora?](#apa-itu-mindora)
- [Prasyarat (Yang Perlu Diinstall Dulu)](#prasyarat-yang-perlu-diinstall-dulu)
- [Instalasi Langkah demi Langkah](#instalasi-langkah-demi-langkah)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default (Demo)](#akun-default-demo)
- [Konfigurasi Tambahan (Opsional)](#konfigurasi-tambahan-opsional)
- [Layanan Machine Learning (Python)](#layanan-machine-learning-python)
- [Pengujian](#pengujian)
- [Perintah yang Tersedia](#perintah-yang-tersedia)
- [Pindah ke Laptop Lain](#pindah-ke-laptop-lain)
- [Struktur Folder](#struktur-folder)
- [Tech Stack](#tech-stack)
- [Troubleshooting (Masalah Umum)](#troubleshooting-masalah-umum)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

---

## Prasyarat (Yang Perlu Diinstall Dulu)

Sebelum mulai, pastikan komputer kamu sudah punya software berikut. Klik link-nya untuk download.

| Software | Versi Minimum | Link Download | Keterangan |
|----------|--------------|---------------|------------|
| **XAMPP** | PHP 8.2+ | [apachefriends.org](https://www.apachefriends.org/download.html) | Sudah termasuk PHP 8.2 + MySQL + Apache |
| **Composer** | 2.x | [getcomposer.org](https://getcomposer.org/download/) | Package manager untuk PHP |
| **Node.js** | 18+ | [nodejs.org](https://nodejs.org/) | Pilih versi LTS, sudah termasuk npm |
| **Git** | 2.x | [git-scm.com](https://git-scm.com/downloads) | Untuk clone repository |
| **Python** | 3.10+ (opsional) | [python.org](https://www.python.org/downloads/) | Hanya untuk layanan ML (SHAP & cluster) |

### Cara Cek Sudah Terinstall atau Belum

Buka **Command Prompt** (tekan `Win + R`, ketik `cmd`, Enter), lalu ketik satu per satu:

```bash
php -v          # Harus muncul PHP 8.2 atau lebih tinggi
composer -V     # Harus muncul Composer version 2.x.x
node -v         # Harus muncul v18.x.x atau lebih tinggi
npm -v          # Harus muncul angka versi (ikut terinstall bersama Node.js)
git --version   # Harus muncul git version 2.x.x
```

> Database yang didukung: **MySQL 5.7+ / MariaDB 10.4+** (sudah diuji di MariaDB 10.4.32 bawaan XAMPP).

> **Belum muncul?** Pastikan kamu sudah install dan restart Command Prompt setelah instalasi.

---

## Instalasi Langkah demi Langkah

### Langkah 1: Clone (Download) Repository

Buka Command Prompt, lalu arahkan ke folder `htdocs` XAMPP kamu:

```bash
cd C:\xampp\htdocs
```

Download project dari GitHub:

```bash
git clone https://github.com/Hapis01/mindora.git
```

Masuk ke folder project:

```bash
cd mindora
```

### Langkah 2: Install Dependency PHP (Laravel)

```bash
composer install
```

> Proses ini akan mendownload semua library PHP yang diperlukan ke folder `vendor/`. Tunggu sampai selesai (biasanya 1-3 menit, tergantung kecepatan internet).

### Langkah 3: Install Dependency JavaScript (Frontend)

```bash
npm install
```

> Ini akan mendownload library frontend (Tailwind CSS, Alpine.js, Chart.js, dll) ke folder `node_modules/`.

### Langkah 4: Buat File Konfigurasi `.env`

```bash
copy .env.example .env
```

> File `.env` berisi pengaturan koneksi database, API key, dll. File ini bersifat rahasia dan tidak di-upload ke GitHub.

### Langkah 5: Generate Application Key dan Encryption Key

```bash
php artisan key:generate
php -r "echo 'base64:'.base64_encode(random_bytes(32));"
```

Perintah kedua mencetak key 32-byte (contoh: `base64:AbCdEf123...=`). Salin hasilnya ke `.env`:

```env
ENCRYPTION_MASTER_KEY=base64:AbCdEf123...=
```

> **Penting:** `ENCRYPTION_MASTER_KEY` **wajib terisi sebelum** `php artisan db:seed` (seeder mengenkripsi profil demo). Jangan bagikan key ini. Jika key diganti setelah data terenkripsi dibuat, data profil lama tidak bisa dibuka lagi.

### Langkah 6: Buat Database dan Atur `.env`

1. Buka **XAMPP Control Panel**, klik **Start** pada **Apache** dan **MySQL**
2. Klik **Admin** di sebelah MySQL (terbuka phpMyAdmin)
3. Klik **New**, isi nama database **`psychoai_db`**, collation **`utf8mb4_unicode_ci`**, klik **Create**
4. Cocokkan `.env` dengan MySQL lokal kamu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=psychoai_db
DB_USERNAME=root
DB_PASSWORD=
```

> Kalau memakai nama database lain, ubah `DB_DATABASE`. Username/password XAMPP default: `root` dan kosong. Setelah mengubah `.env`, jalankan `php artisan config:clear`.
>
> Semua kolom indeks bertipe string dibatasi 191 karakter, sehingga migrasi aman di MySQL 5.7 / MariaDB lama (batas indeks 767 byte, error `#1071` / `#1709`).

### Langkah 7: Jalankan Migrasi Database

```bash
php artisan migrate
```

> Membuat seluruh tabel (users, cache, jobs, roles, assessment, ML, AI, dll).

### Langkah 8: Isi Data Awal (Seeder)

```bash
php artisan db:seed
```

> Aman dijalankan berulang (tidak membuat data ganda). Mengisi:
> - 3 akun pengguna default (super admin, admin, user biasa)
> - Data role & permission
> - Bank soal tes kognitif dan kepribadian
> - Metadata enkripsi
>
> Opsional, data skor contoh untuk akun demo: `php artisan db:seed --class=DemoScoreSeeder`

### Langkah 9: Bersihkan Cache Konfigurasi

```bash
php artisan config:clear
```

### Langkah 10: Build Asset Frontend

```bash
npm run build
```

> Ini akan mengcompile file CSS dan JavaScript ke folder `public/build/`.

---

## Menjalankan Aplikasi

### Cara 1: Menggunakan XAMPP (Recommended untuk pemula)

1. Pastikan **Apache** dan **MySQL** sudah **Start** di XAMPP Control Panel
2. Buka browser, akses:

```
http://localhost/mindora/public
```

> **Catatan:** Jika nama folder kamu berbeda dari `mindora`, sesuaikan URL-nya.

### Cara 2: Menggunakan Laravel Development Server

```bash
php artisan serve
```

Lalu buka browser:

```
http://localhost:8000
```

> Cara ini lebih praktis untuk development. Tekan `Ctrl + C` di Command Prompt untuk menghentikan server.

### Mode Development (Auto-refresh saat edit)

Jika kamu sedang mengubah kode frontend (CSS/JS), jalankan di terminal terpisah:

```bash
npm run dev
```

> Vite akan otomatis me-refresh browser saat kamu menyimpan perubahan file.

---

## Akun Default (Demo)

Setelah menjalankan `php artisan db:seed`, 3 akun demo sudah tersedia:

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | `superadmin@Mindora.test` | `SuperAdminPassword123!` |
| **Admin** | `admin@Mindora.test` | `AdminPassword123!` |
| **User** | `user@Mindora.test` | `UserPassword123!` |

> **⚠️ Penting:** Ganti password akun-akun ini jika digunakan di server publik/production!

### Apa Bedanya Tiap Role?

| Role | Bisa Apa Saja |
|------|--------------|
| **Super Admin** | Semua yang bisa dilakukan Admin + kelola konfigurasi AI & keamanan |
| **Admin** | Kelola pengguna, bank soal, lihat statistik model ML, audit log |
| **User** | Mengerjakan tes, lihat hasil & grafik, konsultasi dengan AI Assistant |

---

## Konfigurasi Tambahan (Opsional)

### Integrasi AI Assistant (Google Gemini)

Mindora memakai **Google Gemini** sebagai provider AI utama. Untuk mengaktifkannya:

1. Buka [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Buat API key
3. Buka file `.env`, isi:

```env
AI_PROVIDER=gemini
GEMINI_API_KEY=AIzaSyxxxxxxxxxxxxxxxxxxxx
GEMINI_MODEL=gemini-3-flash-preview
```

> **Provider alternatif (DeepSeek):** ubah `AI_PROVIDER=deepseek` dan isi `DEEPSEEK_API_KEY`.
>
> **Tanpa API key:** fitur AI Assistant tetap berjalan memakai mesin analisis lokal Mindora (mode demonstrasi) yang menjawab berdasarkan skor asesmen Anda. Fitur lainnya tidak terpengaruh.

### Konfigurasi Email (Opsional)

Secara default, email menggunakan mode `log` (tidak benar-benar terkirim, hanya dicatat di log). Untuk mengirim email sungguhan, ubah konfigurasi SMTP di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailkamu@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

---

## Layanan Machine Learning (Python)

Analisis klaster perilaku (K-Means), kategori profil (Random Forest), dan penjelasan SHAP dijalankan oleh microservice FastAPI terpisah di `python-ml/`. Layanan ini **opsional** untuk menjalankan aplikasi, tetapi **diperlukan** agar tes `MachineLearningIntegrationTest` lulus.

```bash
cd python-ml
pip install -r requirements.txt
```

Jalankan dengan token yang sama seperti `ML_SERVICE_TOKEN` di `.env`:

```bash
# Windows (Command Prompt)
set ML_SERVICE_TOKEN=isi_token_dari_env
python -m uvicorn app.main:app --host 127.0.0.1 --port 8001

# Linux / macOS
ML_SERVICE_TOKEN=isi_token_dari_env python -m uvicorn app.main:app --host 127.0.0.1 --port 8001
```

> Model terlatih sudah ada di `python-ml/artifacts/`. Untuk melatih ulang: `python training/train.py` dari folder `python-ml`. Detail lengkap ada di [docs/ML_SERVICE.md](docs/ML_SERVICE.md).

---

## Pengujian

Tes memakai database MySQL/MariaDB terpisah (`psychoai_test_db`, diatur di `phpunit.xml`) dan `RefreshDatabase`. **Buat database tes kosong dulu**, jangan arahkan ke database yang berisi data penting:

```bash
mysql -u root -e "CREATE DATABASE psychoai_test_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan config:clear
php artisan test
```

Hasil terakhir: **54 tes lulus (251 assertion)** dengan layanan ML aktif.

> **Jika banyak tes gagal dengan status 419:** terminal kamu kemungkinan punya variabel environment sisa (`APP_ENV`, `SESSION_DRIVER`, `DB_DATABASE`, dll.) yang menimpa `phpunit.xml`. Buka terminal baru, atau hapus variabel tersebut lalu ulangi.
>
> Tes ML butuh layanan Python di port 8001 aktif (lihat bagian di atas).

---

## Perintah yang Tersedia

| Perintah | Fungsi |
|----------|--------|
| `php artisan migrate` | Membuat/menjalankan migrasi tabel |
| `php artisan migrate:status` | Melihat status migrasi |
| `php artisan db:seed` | Mengisi data awal (aman dijalankan berulang) |
| `php artisan db:seed --class=DemoScoreSeeder` | Menambah skor contoh untuk akun demo |
| `php artisan migrate:fresh --seed` | **Menghapus semua tabel**, lalu migrasi + seed ulang. Hanya untuk database kosong/dev |
| `php artisan config:clear` | Membersihkan cache konfigurasi setelah ubah `.env` |
| `php artisan serve` | Server development di `http://localhost:8000` |
| `php artisan test` | Menjalankan seluruh tes PHPUnit |
| `npm run dev` | Vite dev server (hot reload) |
| `npm run build` | Build asset produksi ke `public/build/` |

---

## Pindah ke Laptop Lain

Checklist agar proyek jalan di perangkat baru tanpa error:

1. **Jangan salin** `.env`, `vendor/`, `node_modules/`, `storage/logs/`, dan `python-ml/__pycache__/`. `.env` berisi API key dan kunci enkripsi. Gunakan `git clone` saja.
2. Ikuti [Instalasi Langkah demi Langkah](#instalasi-langkah-demi-langkah) dari awal di laptop baru.
3. `ENCRYPTION_MASTER_KEY` di laptop baru boleh berbeda untuk database baru. Namun jika kamu **mengimpor database lama** yang sudah berisi profil terenkripsi, salin `ENCRYPTION_MASTER_KEY` yang sama dari laptop lama. Jika tidak, data profil tidak bisa didekripsi.
4. Untuk memindahkan data lama, ekspor lewat phpMyAdmin (**Export** > Format SQL) dari laptop lama, lalu **Import** ke database kosong di laptop baru. Setelah itu cukup jalankan `php artisan migrate` (tidak perlu `db:seed`).
5. Pastikan extension PHP aktif: `pdo_mysql`, `mbstring`, `openssl`, `sodium`, `fileinfo`, `intl`, `gd`, `zip`, `curl` (semuanya aktif di XAMPP standar).

### Kompatibilitas Database

Semua kolom string yang ter-index (email, key cache, nama role, dll.) dibatasi **191 karakter**, sehingga migrasi tidak memicu error `#1071 Specified key was too long; max key length is 767 bytes` atau `#1709 Index column size too large` pada MySQL/MariaDB lama. Collation default adalah `utf8mb4_unicode_ci`, tersedia di semua versi.

---

## Struktur Folder

```
mindora/
├── app/
│   ├── Http/Controllers/     # Logic request-response (Auth, Assessment, AI, Admin)
│   ├── Models/                # Model database (User, Assessment, Question, dll)
│   ├── Services/              # Business logic (Psychometric, ML, Encryption, AI)
│   │   └── AI/                # Provider AI (GeminiProvider, DeepSeekProvider, AIService)
│   └── Policies/              # Authorization rules
├── config/                    # Konfigurasi aplikasi (database, auth, session, dll)
├── database/
│   ├── migrations/            # Struktur tabel database
│   └── seeders/               # Data awal (role, soal, user demo)
├── docs/                      # Dokumentasi teknis (arsitektur, API, security, diagram)
├── python-ml/                 # Microservice FastAPI (K-Means, Random Forest, SHAP)
│   ├── app/                   # API & predictor
│   ├── artifacts/             # Model terlatih (.joblib)
│   └── training/              # Skrip pelatihan model
├── tests/                     # PHPUnit (Feature & Unit)
├── public/
│   ├── images/                # Logo & aset gambar
│   │   └── landing/           # Aset khusus landing page (logo-pill, logo-ghost)
│   └── build/                 # Hasil build Vite (CSS & JS produksi)
├── resources/
│   ├── css/
│   │   ├── app.css            # Stylesheet utama (dashboard, tes, admin)
│   │   └── landing.css        # Stylesheet landing page (GSAP, Lenis)
│   ├── js/
│   │   ├── app.js             # Script utama
│   │   ├── landing.js         # Animasi GSAP landing page
│   │   └── auth.js            # Interaksi halaman login & register
│   └── views/                 # Halaman HTML (Blade template)
│       ├── admin/             # Halaman panel admin
│       ├── assessments/       # Halaman tes (katalog, instruksi, pengerjaan, hasil)
│       ├── ai/                # Halaman AI Assistant
│       ├── auth/              # Halaman login & register
│       ├── dashboard/         # Halaman dashboard user
│       ├── layouts/           # Layout (app, auth)
│       └── landing.blade.php  # Landing page (standalone + GSAP)
├── routes/web.php             # Daftar URL & routing
├── .env.example               # Template konfigurasi (copy jadi .env)
├── LICENSE                    # Lisensi MIT
├── composer.json              # Dependency PHP
└── package.json               # Dependency JavaScript
```

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.2+, Laravel 11 |
| **Database** | MySQL 5.7+ / MariaDB 10.4+ (via XAMPP) |
| **Frontend** | Tailwind CSS 3, Alpine.js, Chart.js |
| **Design System** | Satu tema gelap + aksen lilac `#a99bff`, font lokal Bricolage Grotesque / Plus Jakarta Sans / Geist Mono, Phosphor Icons |
| **Animasi** | GSAP + ScrollTrigger, Lenis smooth scroll, SplitText (landing page) |
| **Build Tool** | Vite 5 |
| **AI Integration** | Google Gemini Live (`gemini-3-flash-preview`) dengan auto-remap model deprecated + fallback DeepSeek dan mesin analisis lokal |
| **Security** | AES-256-GCM encryption, Argon2id hashing, RBAC, CSRF protection, rate limiting, security headers |
| **Machine Learning** | FastAPI, scikit-learn (K-Means, Random Forest), SHAP |
| **Testing** | PHPUnit 10 (54 tes) |

> **Catatan AI:** Provider AI diatur lewat `AI_PROVIDER` (`gemini` atau `deepseek`). Jika model yang dikonfigurasi sudah tidak tersedia di Google API, aplikasi otomatis memetakannya ke model pengganti yang aktif, lalu memakai mesin analisis lokal Mindora bila koneksi gagal — sehingga konsultasi tidak pernah berhenti total.

---

## Troubleshooting (Masalah Umum)

### "Could not find driver" saat migrasi
PHP belum mengaktifkan extension MySQL. Buka file `php.ini` (biasanya di `C:\xampp\php\php.ini`), cari dan hapus titik koma (`;`) di depan baris:
```ini
extension=pdo_mysql
```
Lalu restart Apache di XAMPP.

### "SQLSTATE[HY000] [1049] Unknown database"
Database belum dibuat. Ikuti [Langkah 6](#langkah-6-buat-database-dan-atur-env) di atas.

### "Vite manifest not found"
Kamu belum menjalankan build frontend. Jalankan:
```bash
npm run build
```

### "The page is not working / Error 500"
1. Pastikan file `.env` sudah ada (copy dari `.env.example`)
2. Pastikan `APP_KEY` sudah di-generate (`php artisan key:generate`)
3. Cek log error di `storage/logs/laravel.log`

### "Permission denied" pada folder storage
Jalankan:
```bash
php artisan storage:link
```
Di Windows biasanya tidak perlu ubah permission. Di Linux/Mac:
```bash
chmod -R 775 storage bootstrap/cache
```

### Halaman CSS berantakan / tidak ada styling
Pastikan sudah menjalankan `npm run build`. Jika masih bermasalah:
```bash
npm run dev
```
Lalu akses via `http://localhost:8000` (bukan lewat XAMPP).

### "Specified key was too long; max key length is 767 bytes"
Sudah diperbaiki di migrasi (kolom ter-index dibatasi 191 karakter). Jika masih muncul, kamu memakai versi migrasi lama: `git pull`, hapus tabel yang setengah jadi di database kosong, lalu jalankan ulang `php artisan migrate`.

### "ENCRYPTION_MASTER_KEY belum dikonfigurasi" / "harus tepat 32 bytes"
Isi `ENCRYPTION_MASTER_KEY` di `.env` dengan hasil `php -r "echo 'base64:'.base64_encode(random_bytes(32));"`, lalu `php artisan config:clear`. Wajib sebelum `php artisan db:seed`.

### Status ML "offline" di halaman admin / tes ML gagal
Layanan Python belum jalan. Lihat [Layanan Machine Learning](#layanan-machine-learning-python). Aplikasi tetap bisa dipakai tanpa layanan ini.

### Port 80 / 3306 sudah digunakan
Matikan program lain yang menggunakan port tersebut (Skype, IIS, atau MySQL standalone). Atau ubah port di XAMPP Config.

---

## Kontribusi

Kontribusi sangat diapresiasi! Silakan:

1. **Fork** repository ini
2. Buat branch fitur baru: `git checkout -b fitur-keren`
3. Commit perubahan: `git commit -m "Tambah fitur keren"`
4. Push ke branch: `git push origin fitur-keren`
5. Buat **Pull Request**

Lihat [docs/CONTRIBUTING.md](docs/CONTRIBUTING.md) untuk panduan lebih detail.

---

## Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  Dibuat dengan ❤️ oleh <a href="https://github.com/hapis01">Hafiz Batubara</a>
</p>
