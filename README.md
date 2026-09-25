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
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
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

---

## Screenshot

> Tambahkan screenshot halaman landing, dashboard, dan tes di sini setelah deploy.

---

## Daftar Isi

- [Apa itu Mindora?](#apa-itu-mindora)
- [Prasyarat (Yang Perlu Diinstall Dulu)](#prasyarat-yang-perlu-diinstall-dulu)
- [Instalasi Langkah demi Langkah](#instalasi-langkah-demi-langkah)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default (Demo)](#akun-default-demo)
- [Konfigurasi Tambahan (Opsional)](#konfigurasi-tambahan-opsional)
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
| **XAMPP** | 8.2+ | [apachefriends.org](https://www.apachefriends.org/download.html) | Sudah termasuk PHP 8.2 + MySQL + Apache |
| **Composer** | 2.x | [getcomposer.org](https://getcomposer.org/download/) | Package manager untuk PHP |
| **Node.js** | 18+ | [nodejs.org](https://nodejs.org/) | Pilih versi LTS, sudah termasuk npm |
| **Git** | 2.x | [git-scm.com](https://git-scm.com/downloads) | Untuk clone repository |

### Cara Cek Sudah Terinstall atau Belum

Buka **Command Prompt** (tekan `Win + R`, ketik `cmd`, Enter), lalu ketik satu per satu:

```bash
php -v          # Harus muncul PHP 8.2 atau lebih tinggi
composer -V     # Harus muncul Composer version 2.x.x
node -v         # Harus muncul v18.x.x atau lebih tinggi
npm -v          # Harus muncul angka versi (ikut terinstall bersama Node.js)
git --version   # Harus muncul git version 2.x.x
```

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
git clone https://github.com/hapis01/mindora.git
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

### Langkah 5: Generate Application Key

```bash
php artisan key:generate
```

> Ini akan mengisi `APP_KEY` di file `.env`. Key ini digunakan Laravel untuk enkripsi data.

### Langkah 6: Buat Database

1. Buka **XAMPP Control Panel**
2. Klik **Start** pada **Apache** dan **MySQL**
3. Klik **Admin** di sebelah MySQL (akan terbuka phpMyAdmin di browser)
4. Klik **New** di sidebar kiri
5. Isi nama database: **`psychoai_db`**
6. Pilih Collation: **`utf8mb4_unicode_ci`**
7. Klik **Create**

> Kalau kamu mau pakai nama database lain, ubah juga di file `.env` pada baris `DB_DATABASE=`.

### Langkah 7: Jalankan Migrasi Database

```bash
php artisan migrate
```

> Perintah ini akan membuat semua tabel yang dibutuhkan di database secara otomatis.

### Langkah 8: Isi Data Awal (Seeder)

```bash
php artisan db:seed
```

> Ini akan mengisi database dengan:
> - 3 akun pengguna default (super admin, admin, user biasa)
> - Data role & permission
> - Bank soal tes kognitif dan kepribadian
> - Metadata enkripsi

### Langkah 9: Generate Encryption Key

Buka file `.env` dengan Notepad atau text editor apapun, lalu cari baris:

```
ENCRYPTION_MASTER_KEY=base64:your_base64_encoded_32_byte_key
```

Ganti dengan key yang kamu generate. Buka Command Prompt dan jalankan:

```bash
php artisan tinker
```

Lalu ketik:

```php
echo 'base64:' . base64_encode(random_bytes(32));
```

Copy hasilnya (contoh: `base64:AbCdEf123...=`) lalu paste ke `.env` menggantikan value lama. Ketik `exit` untuk keluar dari Tinker.

> **Penting:** Key ini digunakan untuk mengenkripsi data pribadi pengguna. Jangan bagikan ke siapapun.

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

### Integrasi AI Assistant (DeepSeek)

Untuk mengaktifkan fitur AI Assistant, kamu perlu API key dari DeepSeek:

1. Daftar di [platform.deepseek.com](https://platform.deepseek.com/)
2. Buat API key
3. Buka file `.env`, isi:

```env
DEEPSEEK_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxx
DEEPSEEK_API_BASE=https://api.deepseek.com
DEEPSEEK_MODEL=deepseek-chat
```

> Tanpa konfigurasi ini, fitur AI Assistant tidak akan berfungsi. Fitur lainnya tetap berjalan normal.

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

## Struktur Folder

```
mindora/
├── app/
│   ├── Http/Controllers/     # Logic request-response (Auth, Assessment, AI, Admin)
│   ├── Models/                # Model database (User, Assessment, Question, dll)
│   ├── Services/              # Business logic (Psychometric, ML, Encryption, AI)
│   └── Policies/              # Authorization rules
├── config/                    # Konfigurasi aplikasi (database, auth, session, dll)
├── database/
│   ├── migrations/            # Struktur tabel database
│   └── seeders/               # Data awal (role, soal, user demo)
├── docs/                      # Dokumentasi teknis (arsitektur, API, security)
├── public/                    # File yang bisa diakses browser (CSS, JS, gambar)
├── resources/views/           # Halaman-halaman HTML (Blade template)
│   ├── admin/                 # Halaman panel admin
│   ├── assessments/           # Halaman tes (katalog, instruksi, pengerjaan, hasil)
│   ├── ai/                    # Halaman AI Assistant
│   ├── auth/                  # Halaman login & register
│   ├── dashboard/             # Halaman dashboard user
│   └── layouts/               # Layout utama (header, footer, sidebar)
├── routes/web.php             # Daftar URL & routing
├── .env.example               # Template konfigurasi (copy jadi .env)
├── composer.json              # Dependency PHP
└── package.json               # Dependency JavaScript
```

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.2+, Laravel 11 |
| **Database** | MySQL 8.0 (via XAMPP) |
| **Frontend** | Tailwind CSS 3, Alpine.js, Chart.js |
| **Build Tool** | Vite 5 |
| **AI Integration** | DeepSeek API (Gemini sebagai alternatif) |
| **Security** | AES-256-GCM encryption, Argon2id hashing, RBAC, CSRF protection, rate limiting, security headers |
| **Testing** | PHPUnit 10 |

---

## Troubleshooting (Masalah Umum)

### "Could not find driver" saat migrasi
PHP belum mengaktifkan extension MySQL. Buka file `php.ini` (biasanya di `C:\xampp\php\php.ini`), cari dan hapus titik koma (`;`) di depan baris:
```ini
extension=pdo_mysql
```
Lalu restart Apache di XAMPP.

### "SQLSTATE[HY000] [1049] Unknown database"
Database belum dibuat. Ikuti [Langkah 6](#langkah-6-buat-database) di atas.

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
