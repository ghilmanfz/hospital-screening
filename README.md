# SIMPPTQ

SIMPPTQ adalah Sistem Informasi Manajemen PPTQ Nurul Iman, aplikasi web untuk membantu operasional pondok dalam satu sistem terpadu. Aplikasi ini mencakup pengelolaan akun dan hak akses, data personil, jadwal mengajar, presensi GPS, izin/cuti, tukar jam, payroll, data santri, kartu QR, presensi santri, kelas, nilai, perilaku, kunjungan wali, pengumuman, branding, laporan, dan integrasi WhatsApp Fonnte.

Proyek ini berbentuk aplikasi Laravel monolith yang server-rendered: backend, routing, otorisasi, query database, dan halaman HTML dikelola oleh Laravel, sedangkan interaksi frontend ringan memakai Blade, Tailwind CSS, Alpine.js, dan beberapa library browser via CDN.

## Status Teknis Saat Ini

- Framework backend: Laravel 13.13.0
- Bahasa backend: PHP 8.3+
- Arsitektur: MVC berbasis Laravel Controller, Eloquent Model, Blade View
- Route aktif: 114 route web
- Autentikasi: session-based auth dengan throttle login 5 kali percobaan gagal
- Otorisasi: custom role-permission melalui `Gate::before`, middleware `permission:`, dan direktif `@can`
- Database runtime: MySQL/MariaDB atau SQLite, mengikuti konfigurasi `.env`
- Database test: SQLite in-memory melalui `phpunit.xml`
- Frontend: Blade, Tailwind CSS v4, Vite v8, Alpine.js, JavaScript native
- Export: file `.xlsx` memakai PhpSpreadsheet
- Penyimpanan file: disk `public` untuk foto/logo/hero, disk `local` privat untuk dokumen sensitif

## Tech Stack

### Backend

| Kebutuhan | Teknologi |
| --- | --- |
| Web framework | Laravel 13 |
| Bahasa | PHP 8.3+ |
| ORM | Eloquent |
| Template | Blade |
| Auth | Laravel session auth |
| Authorization | Custom role-permission, Gate, middleware |
| HTTP client | Laravel HTTP Client |
| Export spreadsheet | PhpSpreadsheet |
| Testing | PHPUnit 12 |
| Formatter | Laravel Pint |
| Dev logs | Laravel Pail |

### Frontend

| Kebutuhan | Teknologi |
| --- | --- |
| Build tool | Vite 8 |
| CSS framework | Tailwind CSS 4 |
| Template rendering | Blade |
| Interaksi ringan | Alpine.js via CDN |
| Grafik dashboard | Chart.js via CDN |
| Peta lokasi presensi | Leaflet.js via CDN |
| Ikon | Remix Icon via CDN |
| Generate QR | qrcodejs via CDN |
| Scan QR | html5-qrcode via CDN |
| Font | Plus Jakarta Sans dan Instrument Sans |

## Struktur Folder Penting

```text
app/
  Http/Controllers/      Controller untuk seluruh modul aplikasi
  Http/Middleware/       Middleware permission custom
  Models/                Eloquent model dan relasi database
  Providers/             AppServiceProvider, Gate, dan view composer branding
  Support/               Helper domain seperti Branding dan ExcelExporter
database/
  migrations/            Skema database
  seeders/               Data awal, role, permission, demo user, akademik, operasional
resources/
  css/app.css            Tailwind CSS dan tema visual
  js/app.js              Entry JS Vite
  views/                 Blade view untuk halaman publik dan aplikasi
routes/
  web.php                Seluruh route web aplikasi
tests/
  Feature/SmokeTest.php  Smoke test alur utama aplikasi
public/
  pondok_hero_banner.png Asset publik default landing page
```

## Arsitektur Aplikasi

SIMPPTQ memakai pola MVC Laravel:

1. Request masuk melalui `routes/web.php`.
2. Route mengarah ke controller sesuai modul.
3. Controller melakukan validasi, otorisasi, dan operasi bisnis.
4. Data disimpan atau dibaca lewat Eloquent model.
5. Tampilan dikirim sebagai Blade view.

Area publik hanya terdiri dari landing page dan login. Area aplikasi berada di prefix `/app` dan wajib login. Menu serta akses fitur dikontrol oleh permission, sehingga user hanya melihat dan membuka modul yang sesuai dengan role-nya.

## Modul Aplikasi

### 1. Landing Page dan Branding

- Halaman publik `/` menampilkan informasi pondok dan pengumuman aktif bertarget `Semua`.
- Branding, logo, teks hero, gambar hero, statistik landing, dan nama pondok disimpan di tabel `settings`.
- Konfigurasi branding dibagikan ke seluruh view melalui `App\Support\Branding`.

### 2. Autentikasi dan Profil

- Login di `/login`.
- Logout memakai session invalidation dan token regeneration.
- Login dibatasi 5 percobaan gagal per email dan IP.
- Hanya user dengan `is_active = true` yang bisa masuk.
- User dapat mengubah profil dan password sendiri.

### 3. User, Role, dan Permission

- Role bawaan: Super Admin, Admin Operasional, Guru, Staf Non-Pengajar, Dua Fungsi, dan Pimpinan.
- Permission dikelompokkan ke area Sistem, Personil, Santri, dan Komunikasi.
- Super Admin mendapat akses penuh melalui method `User::hasPermissionTo`.
- Middleware `permission:` mendukung satu permission atau beberapa alternatif dengan pemisah `|`, misalnya `leave_apply|leave_approve`.

### 4. Personil dan Dokumen

- CRUD data personil, termasuk NIK, kontak, jabatan, unit kerja, status kerja, fungsi kerja, foto, dan komponen gaji.
- Fungsi kerja dipisah dari role: `Non-Pengajar`, `Pengajar`, dan `Dua Fungsi`.
- Data personil dapat ditautkan ke akun user.
- Foto personil disimpan di disk publik.
- Dokumen personil seperti PDF/JPG/PNG disimpan di disk privat dan hanya dapat diunduh melalui route berizin.
- Data personil dapat diekspor ke `.xlsx`.

### 5. Akademik, Kelas, dan Jadwal

- Master data akademik mencakup tahun ajaran, mapel/halaqah, kelas, dan sesi.
- Jadwal mengajar memakai hari Senin sampai Ahad dan sesi yang memiliki jam mulai/jam selesai.
- Saat jadwal dibuat atau diubah, sistem mencegah bentrok pengajar atau kelas pada hari dan sesi yang sama.
- Pengajar tanpa hak kelola hanya melihat jadwalnya sendiri.
- Halaman jadwal hari ini menampilkan jadwal efektif beserta pengecualian tukar jam.

### 6. Presensi GPS Personil

- Personil melakukan check-in dan check-out dengan latitude dan longitude.
- Sistem memvalidasi posisi terhadap lokasi presensi aktif memakai rumus haversine.
- Check-in di dalam radius lokasi sah akan dicatat.
- Check-in setelah pukul 07:30 diberi status `Terlambat`, selain itu `Tepat Waktu`.
- Setiap personil hanya bisa memiliki satu record presensi per tanggal.
- Admin dapat melihat rekap dan export presensi ke `.xlsx`.

### 7. Izin/Cuti

- Personil dapat mengajukan izin atau cuti dengan tanggal mulai, tanggal akhir, alasan, dan dokumen pendukung opsional.
- Dokumen izin disimpan di disk privat.
- User dengan permission approval dapat menyetujui atau menolak pengajuan.
- Status utama: `Diajukan`, `Disetujui`, dan `Ditolak`.

### 8. Tukar Jam Mengajar

- Pengajar dapat mengajukan tukar jam untuk jadwal sendiri atau ketika dirinya menjadi pengganti jadwal rekan.
- Pengganti tidak boleh sama dengan pengajar asli.
- Saat disetujui, sistem membuat record di `jadwal_exceptions`.
- Jadwal master tetap utuh; perubahan hanya berlaku pada tanggal tertentu.
- Payroll membaca pengecualian tukar jam untuk menghitung honor mengajar efektif.

### 9. Payroll dan Slip Gaji

- Admin membuat periode payroll dengan tanggal mulai dan akhir.
- Payroll berstatus `Draft` dapat diproses ulang.
- Payroll berstatus `Final` dikunci dan slip dapat dilihat personil.
- Perhitungan saat ini mencakup:
  - gaji pokok,
  - tunjangan,
  - potongan tetap,
  - potongan keterlambatan,
  - honor mengajar per sesi,
  - jumlah hari hadir,
  - jumlah hari terlambat.
- Potongan terlambat saat ini bernilai Rp 25.000 per keterlambatan.
- Data payroll dapat diekspor ke `.xlsx`.

### 10. Data Santri dan Kartu QR

- CRUD santri dengan NIS, NISN, biodata, kelas, status, wali, nomor wali, foto, dan token kartu.
- Token kartu dibuat unik oleh `Santri::generateCardToken`.
- Kartu santri dapat dicetak satuan atau massal.
- Token kartu dapat digenerate ulang satuan atau massal.
- Santri dapat dipindahkan kelas secara massal.
- Perubahan kelas tercatat di riwayat kelas.
- Data santri dapat diekspor ke `.xlsx`.

### 11. Presensi Santri

- Presensi santri dapat dilakukan melalui scan QR/token kartu atau input manual.
- Sistem menolak presensi untuk santri yang tidak aktif.
- Satu santri hanya bisa dicatat sekali per tanggal dan kegiatan.
- Data presensi mencatat kelas, tanggal, jam, kegiatan, status, dan petugas pencatat.

### 12. Naik Kelas dan Riwayat Kelas

- Modul kelas memiliki halaman anggota kelas.
- Santri dapat ditambahkan, dipindahkan, dikeluarkan dari kelas, atau diproses dalam wizard naik kelas.
- Wizard naik kelas mendukung aksi naik kelas, lulus, dan tinggal kelas.
- Riwayat kelas mencatat aksi seperti Penempatan, Pindah Kelas, Naik Kelas, Tinggal Kelas, Lulus, dan Keluar.

### 13. Perilaku, Nilai, dan Kunjungan

- Perilaku santri mencatat `Pelanggaran` dan `Kebaikan` dengan kategori, poin, tanggal, catatan, dan pencatat.
- Rekap perilaku menghitung total kebaikan, pelanggaran, saldo poin, dan ranking.
- Nilai/perkembangan santri dapat dicatat per mapel atau subject bebas.
- Kunjungan wali mencatat nama pengunjung, relasi, waktu kunjungan, keperluan, dan catatan.
- Catatan perilaku dapat diekspor ke `.xlsx`.

### 14. Pengumuman

- Pengumuman memiliki judul, isi, target, status aktif, tanggal publish, dan author.
- Target dapat berupa `Semua` atau nama role tertentu.
- Pengumuman `Semua` muncul di landing page.
- Pengumuman sesuai role muncul di dashboard user.

### 15. Laporan Strategis

- Laporan memakai rentang tanggal.
- Ringkasan mencakup personil, presensi personil, izin/cuti, santri, presensi santri, perilaku, dan kunjungan.
- Laporan dapat diekspor ke `.xlsx`.

### 16. WhatsApp Fonnte

- Token dan sender WhatsApp disimpan melalui menu aplikasi pada tabel `settings`.
- Test kirim memakai HTTP POST nyata ke endpoint Fonnte `https://api.fonnte.com/send`.
- Status koneksi disimpan sebagai `whatsapp_connected`.

## Database

Skema database berada di `database/migrations`. Tabel utama aplikasi mencakup:

- User dan akses: `users`, `roles`, `permissions`, `permission_role`
- Personil: `personils`, `personil_documents`
- Akademik: `tahun_ajarans`, `mapels`, `kelas`, `sesis`, `jadwals`, `jadwal_exceptions`, `class_histories`
- Presensi dan operasional personil: `lokasi_presensis`, `presensi_personils`, `leave_requests`, `swap_requests`
- Payroll: `payroll_periods`, `payslips`
- Santri: `santris`, `santri_presences`, `behaviors`, `grades`, `visits`
- Komunikasi dan konfigurasi: `announcements`, `settings`
- Tabel bawaan Laravel: `sessions`, `cache`, `jobs`, `failed_jobs`, dan tabel pendukung lain

Data awal dibuat oleh seeder berikut:

- `RolePermissionSeeder`: role dan permission sistem
- `PersonilUserSeeder`: akun demo dan data personil
- `AcademicSeeder`: tahun ajaran, kelas, mapel/halaqah, sesi, jadwal
- `SantriSeeder`: data santri contoh dan token kartu
- `OperationalSeeder`: lokasi presensi, presensi contoh, izin, tukar jam, perilaku, nilai, kunjungan, pengumuman
- `PayrollSeeder`: periode payroll final dan draft
- `SettingSeeder`: default branding, landing page, dan WhatsApp

## Akun Demo

Seeder menyediakan akun berikut:

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | `superadmin@nuruliman.net` | `superadmin123` |
| Admin Operasional | `petugas@nuruliman.net` | `petugas123` |
| Guru / Pengajar | `ustadz.ahmad@nuruliman.net` | `ustadz123` |
| Staf Non-Pengajar | `staff.budiyono@nuruliman.net` | `staff123` |
| Dua Fungsi | `ustadz.fatkur@nuruliman.net` | `ustadzstaff123` |
| Pimpinan Pondok | `pimpinan.kiai@nuruliman.net` | `kiai123` |

Tombol uji coba cepat di halaman login menggunakan kredensial tersebut.

## Instalasi Lokal

### Prasyarat

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan NPM
- MySQL/MariaDB jika memakai Laragon atau database server lokal
- Ekstensi PHP umum Laravel, termasuk PDO, mbstring, openssl, tokenizer, xml, ctype, json, fileinfo, dan gd untuk upload gambar

### Langkah Instalasi

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Jika memakai Linux/macOS/Git Bash, gunakan:

```bash
cp .env.example .env
```

### Opsi Database SQLite

`.env.example` memakai SQLite sebagai default. Untuk quick start:

```bash
type nul > database\database.sqlite
php artisan migrate:fresh --seed
```

Jika file `database/database.sqlite` sudah ada, cukup jalankan migrasi dan seeder:

```bash
php artisan migrate:fresh --seed
```

### Opsi Database MySQL/MariaDB

Jika memakai Laragon atau MySQL lokal, buat database misalnya `simpptq`, lalu sesuaikan `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpptq
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:

```bash
php artisan migrate:fresh --seed
```

### Storage Link

Jalankan storage link agar file publik seperti foto, logo, dan hero dapat diakses browser:

```bash
php artisan storage:link
```

Dokumen sensitif tetap berada di disk privat `storage/app/private` dan tidak dibuka langsung melalui folder publik.

### Build Asset

Untuk mode produksi/lokal biasa:

```bash
npm run build
```

Untuk mode pengembangan dengan hot reload:

```bash
npm run dev
```

### Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Buka:

```text
http://localhost:8000
```

Jika memakai Laragon virtual host, aplikasi juga bisa dibuka melalui domain lokal yang dikonfigurasi, misalnya:

```text
http://simpptq.test
```

### Script Development Gabungan

`composer.json` menyediakan script:

```bash
composer run dev
```

Script ini menjalankan Laravel server, queue listener, log viewer Pail, dan Vite secara bersamaan melalui `concurrently`.

## Pengujian

Jalankan test:

```bash
php artisan test
```

Atau:

```bash
composer test
```

Test memakai SQLite in-memory dan melakukan seeding otomatis. Smoke test saat ini memverifikasi:

- landing page dan login dapat diakses,
- login berhasil dan gagal,
- redirect user login dari halaman login ke dashboard,
- Super Admin dapat membuka halaman utama seluruh modul,
- semua role dapat membuka dashboard,
- otorisasi membatasi akses staf non-pengajar,
- admin dapat menambah santri,
- presensi GPS valid diterima dan koordinat di luar radius ditolak,
- admin dapat menyetujui izin.

## Perintah Berguna

```bash
php artisan route:list
php artisan migrate:fresh --seed
php artisan storage:link
php artisan test
vendor/bin/pint
npm run dev
npm run build
```

## Catatan Pengembangan

- Aplikasi ini bukan SPA React/Vue; mayoritas halaman adalah Blade server-rendered.
- Akses modul dikontrol oleh permission, bukan hanya pengecekan role statis.
- Fungsi kerja personil (`Pengajar`, `Non-Pengajar`, `Dua Fungsi`) menentukan kelayakan fitur operasional tertentu seperti jadwal dan tukar jam.
- Data konfigurasi branding dan WhatsApp berada di database, bukan hardcode di view.
- Export Excel dipusatkan di `App\Support\ExcelExporter` agar format laporan konsisten.
- Test otomatis saat ini berupa smoke/feature test. Jika menambah fitur bisnis besar, tambahkan test yang menguji validasi, otorisasi, dan perubahan database.
