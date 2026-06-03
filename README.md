# Portal Rumah Sakit & Sistem Screening Mandiri

Aplikasi web portal rumah sakit modern yang dirancang dengan estetika premium menggunakan tema warna **Mayapada Hospital / Deloitte** (Deep Navy `#0b2545` dan Emerald Green `#008751`). Aplikasi ini memfasilitasi pasien untuk mengisi keluhan kesehatan, melakukan screening mandiri dengan rekomendasi rujukan cerdas (IGD, Poli Umum, Poli Anak), dan memberikan survei penilaian kepuasan layanan serta pelaporan lengkap untuk admin.

---

## 🚀 Fitur Utama

### 1. Halaman Publik (Landing Page)
- **Hero Banner Dinamis**: Menampilkan teks utama dan gambar banner hero yang dapat diubah oleh admin.
- **Informasi Layanan**: Grid kartu layanan rumah sakit (PCR Swab, Vaksin, dll).
- **Flyer Jadwal Dokter (Employee Healthy Week)**: Menampilkan jadwal praktik dokter spesialis secara dinamis dengan foto profil dokter dari database.

### 2. Autentikasi & Registrasi dengan OTP WhatsApp
- **Registrasi Pasien Baru**: Menginput nama, no. WhatsApp, email, dan password.
- **Simulasi OTP WhatsApp**: Halaman verifikasi kode OTP interaktif yang dilengkapi dengan log simulasi pengiriman pesan WhatsApp (tidak perlu setting API Key Fonnte riil untuk uji coba, kode OTP langsung tampil di layar notifikasi simulasi untuk mempermudah pendaftaran).

### 3. Dasbor Pasien (Patient Dashboard)
- **Alur Status Layanan**: Indikator status tahapan pemeriksaan pasien saat ini.
- **Form Keluhan & Diagnosa Mandiri**: Tempat pasien menginput gejala awal.
- **Screening Medis Mandiri**: Wizard kuesioner interaktif untuk menganalisis tingkat keparahan gejala pasien.
- **Rujukan Otomatis**: Rekomendasi tindakan medis secara instan (Disarankan ke IGD, Poli Umum, atau Poli Anak).
- **Survei Kepuasan Layanan**: Form penilaian terhadap 4 aspek (Kelayakan Fasilitas, Kebersihan Lingkungan, Layanan Dokter, Kecepatan Apotek).
- **Grafik Radar Kepuasan**: Diagram radar Chart.js interaktif yang menggambarkan skor kepuasan pasien.
- **Riwayat Diagnosa**: Tabel riwayat kunjungan medis pasien dengan modal detail yang responsif dan dapat di-scroll.

### 4. Dasbor Admin (Admin Panel)
- **Ringkasan Statistik**: Dashboard pemantauan jumlah pasien aktif, jumlah survei selesai, dan rata-rata skor kepuasan.
- **Kelola Landing Page**: Form konfigurasi nama rumah sakit, logo (mendukung upload file gambar langsung atau input URL), gambar banner hero, teks judul utama, subjudul, jadwal dokter (nama, spesialis, lokasi, jadwal, dan upload foto), serta kartu layanan.
- **Kelola Alur Screening**: Menambah/mengedit pertanyaan screening beserta opsi jawaban dan bobot penilaiannya.
- **Indeks Diagnosa Pasien**: Daftar riwayat diagnosis dan screening semua pasien yang dapat dicari dan difilter berdasarkan tanggal, hasil rujukan, dan status survei.
- **Konfigurasi Sistem**:
  - Pengaturan token API **Fonnte** untuk gateway WhatsApp.
  - Pengaturan parameter OTP (panjang karakter, durasi kedaluwarsa, batas percobaan ulang, cooldown, default kode negara).
  - Kustomisasi template pesan OTP.
- **Log Pengiriman WhatsApp**: Tabel audit pengiriman OTP WhatsApp lengkap dengan respons status pengiriman provider.
- **Audit Logs**: Rekaman aktivitas perubahan yang dilakukan oleh admin untuk menjaga integritas sistem.

---

## 🛠️ Tech Stack

- **Framework**: Laravel 11
- **Styling**: Tailwind CSS v4 (Desain Responsif, Glassmorphism, Micro-animations)
- **Front-end Interactivity**: Alpine.js
- **Charts**: Chart.js (Radar & Analitik)
- **Database**: SQLite / MySQL

---

## 📦 Panduan Instalasi & Penggunaan Lokal

### Prasyarat
Pastikan komputer Anda sudah terinstal:
- PHP >= 8.2
- Composer
- Node.js & NPM

### Langkah-langkah Setup
1. **Clone Repositori**:
   ```bash
   git clone https://github.com/ghilmanfz/hospital-screening.git
   cd hospital-screening
   ```

2. **Instal Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Instal Dependensi Frontend**:
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**:
   Salin berkas `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   *Secara default, konfigurasi DB diatur ke SQLite untuk kemudahan portabilitas.*

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database & Seed Data**:
   Lakukan migrasi tabel database sekaligus memuat data uji coba default (akun admin, pasien simulasi, daftar pertanyaan screening, log, dll):
   ```bash
   php artisan migrate:refresh --seed
   ```

7. **Compile Aset Frontend**:
   Gunakan perintah ini untuk memicu *Vite hot-reloading* saat pengembangan:
   ```bash
   npm run dev
   ```
   Atau untuk membuat build produksi:
   ```bash
   npm run build
   ```

8. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi Anda sekarang aktif di `http://127.0.0.1:8000`.

---

## 🔑 Kredensial Akun Uji Coba Default

Untuk mempermudah peninjauan fitur, Anda dapat masuk menggunakan akun default berikut setelah menjalankan perintah `--seed`:

- **Akun Administrator**:
  - **Email / Username**: `admin@hospital.com`
  - **Password**: `admin123`

- **Akun Pasien Contoh**:
  - **Email / Username**: `budi@example.com`
  - **Password**: `pasien123`

---

## 📁 Struktur Direktori Penting

- [app/Http/Controllers/](file:///c:/laragon/www/hospital-screening/app/Http/Controllers/): Berisi backend controllers (`PublicController`, `AuthController`, `PatientController`, `AdminController`).
- [app/Models/](file:///c:/laragon/www/hospital-screening/app/Models/): Model Eloquent ORM (`User`, `Diagnosis`, `SystemConfiguration`, `WhatsappMessageLog`, `AuditLog`).
- [routes/web.php](file:///c:/laragon/www/hospital-screening/routes/web.php): Konfigurasi endpoint perutean sistem.
- [resources/views/](file:///c:/laragon/www/hospital-screening/resources/views/): View templates Laravel Blade (UI Dasbor Pasien, Admin, Autentikasi, dan Beranda).
- [public/uploads/](file:///c:/laragon/www/hospital-screening/public/uploads/): Folder lokal tempat file foto dokter, banner hero, dan logo rumah sakit disimpan ketika admin melakukan upload berkas.
