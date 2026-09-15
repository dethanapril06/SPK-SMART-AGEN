# 📊 Sistem Pendukung Keputusan (SPK) Seleksi Calon Agen - Metode SMART

Sistem Pendukung Keputusan (SPK) berbasis web untuk proses seleksi dan penentuan kelayakan calon agen menggunakan metode **SMART (Simple Multi-Attribute Rating Technique)**. Aplikasi ini dibangun dengan framework **Laravel (PHP 8.3+)** dan frontend modern berbasis **Tailwind CSS & Vite**.

---

## 📑 Daftar Isi

1. [Fitur Utama](#-fitur-utama)
2. [Kebutuhan Sistem (System Requirements)](#-kebutuhan-sistem-system-requirements)
3. [Panduan Instalasi & Konfigurasi](#-panduan-instalasi--konfigurasi-langkah-demi-langkah)
4. [Cara Menjalankan Program](#-cara-menjalankan-program)
5. [Informasi Akun Default](#-informasi-akun-default)
6. [Alur Singkat Penggunaan](#-alur-singkat-penggunaan-sistem)
7. [Solusi Kendala Populer (Troubleshooting)](#-solusi-kendala-populer-troubleshooting)

---

## ✨ Fitur Utama

- **Role Multi-User**:
  - **Admin**: Mengelola periode pendaftaran, data calon agen, kriteria & sub-kriteria, penilaian calon agen, komputasi metode SMART, laporan / cetak PDF, serta manajemen user.
  - **Calon Agen**: Registrasi mandiri, mengisi formulir pendaftaran/screening, mengunggah dokumen persyaratan (NIB & NPWP), dan memantau status pengumuman serta notifikasi hasil seleksi.
- **Kalkulasi Otomatis Metode SMART**:
  - Penentuan bobot kriteria (tipe *benefit* dan *cost*).
  - Normalisasi bobot secara transparan.
  - Perhitungan nilai *Utility* untuk setiap alternatif pada setiap kriteria.
  - Perhitungan nilai akhir (*Total Utility*) dan perankingan alternatif secara otomatis.
- **Laporan & Export**:
  - Cetak rekap periode dan hasil seleksi akhir dalam bentuk dokumen cetak / PDF (menggunakan `barryvdh/laravel-dompdf`).
- **Penyimpanan Dokumen**:
  - Pengunggahan dan pengelolaan dokumen NIB dan NPWP dengan validasi format dan ukuran file.

---

## 💻 Kebutuhan Sistem (System Requirements)

Sebelum menjalankan aplikasi, pastikan perangkat komputer Anda telah terpasang software berikut:

| Perangkat Lunak | Versi Minimal yang Dibutuhkan | Keterangan |
| :--- | :--- | :--- |
| **Sistem Operasi** | Windows 10/11, Linux, atau macOS | Disarankan Windows dengan **Laragon** |
| **PHP** | `>= 8.3.0` | Pastikan binary `php` terdaftar di PATH |
| **Composer** | `>= 2.x` | Manajer paket PHP |
| **Node.js** | `>= 18.x` (disarankan 20.x atau 22.x) | Runtime JavaScript |
| **npm** | `>= 9.x` | Manajer paket Node.js |
| **Database** | MySQL `>= 5.7` / `>= 8.0` atau MariaDB `>= 10.4` | Server database lokal |
| **Web Server (Opsional)** | Laragon / XAMPP / Apache / Nginx | Memudahkan menjalankan MySQL & PHP |

### Ekstensi PHP yang Wajib Aktif

Pastikan ekstensi-ekstensi berikut telah diaktifkan di file konfigurasi `php.ini` Anda:
- `pdo_mysql`
- `mbstring`
- `openssl`
- `fileinfo`
- `curl`
- `gd`
- `tokenizer`
- `xml`
- `ctype`

> **Tips Pengguna Laragon / XAMPP:**
> Ekstensi-ekstensi di atas umumnya sudah aktif secara default pada instalasi standar Laragon atau XAMPP terbaru.

---

## 🛠️ Panduan Instalasi & Konfigurasi (Langkah demi Langkah)

Ikuti langkah-langkah di bawah ini secara berurutan:

### Langkah 1: Siapkan Folder Project

Pastikan project sudah berada di folder web server lokal Anda.
Contoh penempatan di Windows Laragon:
```bash
C:\laragon\www\SPKSmartAgen
```
Buka terminal (PowerShell, Command Prompt, atau Git Bash) dan arahkan ke direktori project:
```bash
cd C:\laragon\www\SPKSmartAgen
```

---

### Langkah 2: Buat Database MySQL

1. Pastikan servis **MySQL** sudah berjalan (misalnya klik **Start All** di Laragon / XAMPP).
2. Buka aplikasi pengelola database seperti **phpMyAdmin** (`http://localhost/phpmyadmin`) atau **HeidiSQL**.
3. Buat sebuah database baru dengan nama:
   ```sql
   spksmartagen
   ```
   *(Gunakan Collation: `utf8mb4_unicode_ci`)*

---

### Langkah 3: Ekstrak & Import Database dari File `database/spk-smart-agen.sql.gz`

Database lengkap aplikasi sudah disediakan di dalam folder `database/spk-smart-agen.sql.gz`. Anda dapat memilih salah satu cara berikut:

#### Opsi 1: Import Langsung via phpMyAdmin (Rekomendasi Tanpa Ekstrak Manual)
phpMyAdmin mendukung file kompresi `.sql.gz` secara langsung:
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Klik database **`spksmartagen`** di sebelah kiri.
3. Klik tab **Import** di atas.
4. Klik **Choose File** (Pilih Berkas), lalu arahkan ke file:
   `C:\laragon\www\SPKSmartAgen\database\spk-smart-agen.sql.gz`
5. Klik tombol **Import / Go**.

#### Opsi 2: Ekstrak via Baris Perintah (CLI) & Import ke MySQL
1. Ekstrak file `.gz` menjadi `.sql` menggunakan PHP:
   ```powershell
   php -r "file_put_contents('database/spk-smart-agen.sql', gzdecode(file_get_contents('database/spk-smart-agen.sql.gz')));"
   ```
2. Import file SQL ke database MySQL:
   ```powershell
   # Jika MySQL tanpa password:
   mysql -u root spksmartagen < database/spk-smart-agen.sql

   # Jika MySQL menggunakan password:
   mysql -u root -p spksmartagen < database/spk-smart-agen.sql
   ```

#### Opsi 3: Ekstrak Menggunakan 7-Zip atau WinRAR
1. Buka folder `database/`, klik kanan file `spk-smart-agen.sql.gz` > pilih **7-Zip/WinRAR** > **Extract Here**.
2. Buka file hasil ekstrak `spk-smart-agen.sql` di **HeidiSQL** atau **phpMyAdmin**, lalu eksekusi ke database `spksmartagen`.

> **Info:** Karena database sudah diisi dari file dump, Anda tidak perlu menjalankan `php artisan migrate --seed`.

---

### Langkah 4: Konfigurasi File Lingkungan (`.env`)

1. Periksa apakah file `.env` sudah ada di folder utama project. Jika belum ada, salin dari `.env.example`:
   ```powershell
   copy .env.example .env
   ```
2. Buka file `.env` menggunakan teks editor (VS Code, Notepad, dll.), lalu sesuaikan konfigurasi database berikut:
   ```env
   APP_NAME="SPK SMART Agen"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=spksmartagen
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan kredensial MySQL lokal Anda).*

---

### Langkah 5: Install Dependensi PHP (Composer)

Jalankan perintah berikut untuk mengunduh semua library PHP yang dibutuhkan:
```bash
composer install
```

---

### Langkah 6: Generate Application Key

Buat kunci enkripsi aplikasi Laravel dengan perintah:
```bash
php artisan key:generate
```

---

### Langkah 7: Buat Symlink Storage (Penting untuk Berkas Dokumen)

Agar berkas dokumen pendaftaran calon agen (NIB & NPWP) yang diunggah dapat diakses dan dilihat oleh Admin, buat symlink direktori penyimpanan dengan perintah:
```bash
php artisan storage:link
```

---

### Langkah 8: Install Dependensi Frontend (Node.js & npm)

Jalankan instalasi paket frontend (Tailwind CSS, Vite, dll.):
```bash
npm.cmd install
```

*(Gunakan `npm.cmd` jika PowerShell membatasi script eksekusi).*

---

## 🚀 Cara Menjalankan Program

Pilih salah satu metode di bawah ini untuk menjalankan aplikasi:

### Metode 1: Dual Terminal (Rekomendasi untuk Pengembangan)

Buka dua jendela terminal di direktori project:

**Terminal 1 (Backend - Server Laravel):**
```bash
php artisan serve
```
*Server Laravel akan berjalan di:* `http://127.0.0.1:8000`

**Terminal 2 (Frontend - Asset Compiler Vite):**
```bash
npm.cmd run dev
```

Setelah kedua perintah berjalan, buka browser dan akses:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

### Metode 2: Sekali Perintah (Menggunakan Concurrently)

Project ini sudah dilengkapi skrip bawaan untuk menjalankan server dan vite sekaligus:
```bash
composer run dev
```

---

### Metode 3: Menggunakan Virtual Host Laragon

Jika Anda menggunakan Laragon:
1. Jalankan perintah build frontend satu kali:
   ```bash
   npm.cmd run build
   ```
2. Pastikan Laragon berjalan (**Start All**).
3. Anda dapat langsung mengakses aplikasi melalui domain virtual Laragon:
   👉 **`http://spksmartagen.test`**

---

## 🔐 Informasi Akun Default

Akun yang sudah tersedia di dalam database hasil import:

### Akun Administrator
- **URL Login**: `http://127.0.0.1:8000/login`
- **Email**: `admin@smart.com`
- **Password**: `password`
- **Role**: `admin`
- **Hak Akses**: Mengelola seluruh master data, periode pendaftaran, penilaian calon agen, kalkulasi SMART, laporan, serta data user.

### Akun Calon Agen
- Calon agen dapat mendaftar langsung melalui tautan **Daftar / Register** di halaman login (`http://127.0.0.1:8000/register`).
- Admin juga dapat menambahkan akun calon agen secara manual melalui menu **Kelola User** di dashboard admin.

---

## 🔄 Alur Singkat Penggunaan Sistem

1. **Login Administrator**:
   - Masuk menggunakan akun `admin@smart.com`.
2. **Buka Periode Pendaftaran**:
   - Masuk ke menu **Periode Pendaftaran**, buat periode baru, dan ubah statusnya menjadi **Buka**.
3. **Pendaftaran Calon Agen**:
   - Calon agen mendaftar melalui form registrasi web.
   - Calon agen masuk ke dashboard mereka untuk melengkapi form data usaha dan mengunggah dokumen persyaratan (NIB dan NPWP).
4. **Penilaian oleh Admin**:
   - Admin memeriksa kelengkapan data calon agen.
   - Admin masuk ke menu **Penilaian**, memilih periode yang aktif, lalu memberikan penilaian untuk tiap kriteria calon agen berdasarkan kondisi riil dan dokumen.
5. **Kalkulasi Metode SMART**:
   - Admin masuk ke menu **Metode SMART**.
   - Klik tombol **Hitung SMART** untuk memproses normalisasi bobot, nilai utility, dan total perankingan.
   - Admin dapat melihat rincian langkah perhitungan melalui menu **Langkah Perhitungan**.
6. **Laporan & Rekap**:
   - Admin dapat melihat dan mencetak laporan hasil seleksi melalui menu **Laporan**.

---

## ❓ Solusi Kendala Populer (Troubleshooting)

### 1. Pesan Error: `npm.ps1 cannot be loaded because running scripts is disabled`
- **Penyebab**: Kebijakan keamanan Windows PowerShell memblokir eksekusi script eksternal.
- **Solusi 1 (Paling Cepat)**: Gunakan akhiran `.cmd` saat menjalankan perintah:
  ```powershell
  npm.cmd install
  npm.cmd run dev
  ```
- **Solusi 2**: Izinkan eksekusi script untuk sesi PowerShell saat ini:
  ```powershell
  Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
  ```

---

### 2. Berkas NIB / NPWP Tidak Muncul / Error 404 Saat Diunduh
- **Solusi**: Jalankan perintah symlink storage Laravel:
  ```bash
  php artisan storage:link
  ```

---

### 3. Error Koneksi Database: `Access denied for user 'root'@'localhost'`
- **Penyebab**: Konfigurasi username atau password database di `.env` belum sesuai dengan setelan MySQL Anda.
- **Solusi**: Buka file `.env`, sesuaikan baris:
  ```env
  DB_USERNAME=root
  DB_PASSWORD=
  ```

---

### 4. Tampilan Web Berantakan atau Tidak Memiliki Gaya (CSS)
- **Solusi**: Pastikan Anda menjalankan `npm.cmd run dev` atau build asset siap pakai dengan `npm.cmd run build`.

---

### 5. Port 8000 Sudah Terpakai
- Jalankan server di port lain:
  ```bash
  php artisan serve --port=8080
  ```
  Lalu akses melalui `http://127.0.0.1:8080`.

---

### 6. Reset atau Bersihkan Cache Laravel
Jika melakukan perubahan pada file `.env` atau konfigurasi tetapi tidak berpengaruh:
```bash
php artisan optimize:clear
```

---

*Dikembangkan dengan ❤️ menggunakan Laravel & Tailwind CSS.*
