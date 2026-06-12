<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Diagnosis;
use App\Models\Disease;
use App\Models\LoginAttempt;
use App\Models\SystemConfiguration;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@hospital.com',
            'phone_number' => '628123456789',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'phone_verified_at' => now(),
        ]);

        // 1b. Create Shared Dokter IGD Account (1 akun bersama, dibuatkan oleh Admin)
        $dokter = User::create([
            'name' => 'Dokter IGD (Akun Bersama)',
            'email' => 'dokter@hospital.com',
            'phone_number' => '628111111111',
            'password' => Hash::make('dokter123'),
            'role' => 'dokter',
            'status' => 'active',
            'phone_verified_at' => now(),
        ]);

        // 2. Create Default Patient Budi Santoso
        $patient = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone_number' => '628999999999',
            'gender' => 'Laki-laki',
            'birth_date' => '1990-04-15',
            'address' => 'Jl. Merdeka No. 12, RT 03/RW 05, Kel. Sukamaju, Kec. Cilandak, Jakarta Selatan',
            'password' => Hash::make('pasien123'),
            'role' => 'pasien',
            'status' => 'active',
            'phone_verified_at' => now(),
        ]);

        // Other mock patients
        $patient2 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'phone_number' => '628777777777',
            'gender' => 'Perempuan',
            'birth_date' => '1996-11-02',
            'address' => 'Jl. Kenanga Raya No. 8, Kel. Pasar Minggu, Jakarta Selatan',
            'password' => Hash::make('pasien123'),
            'role' => 'pasien',
            'status' => 'active',
            'phone_verified_at' => now(),
        ]);

        $patient3 = User::create([
            'name' => 'Randi Wijaya',
            'email' => 'randi@example.com',
            'phone_number' => '628555555555',
            'gender' => 'Laki-laki',
            'birth_date' => '1988-07-21',
            'address' => 'Perum Griya Asri Blok C2 No. 14, Bekasi Barat',
            'password' => Hash::make('pasien123'),
            'role' => 'pasien',
            'status' => 'active',
            'phone_verified_at' => now(),
        ]);

        // 3. Configurations
        SystemConfiguration::setVal('hospital_name', 'Rumah Sakit Bhayangkara LEMDIKLAT', $admin->id);
        SystemConfiguration::setVal('hospital_hero_title', 'Empowering Your Health, Every Single Day', $admin->id);
        SystemConfiguration::setVal('hospital_hero_subtitle', 'Portal Resmi Rumah Sakit - Akses Jadwal Dokter, Diagnosis Cepat, Screening Mandiri, dan Evaluasi Layanan.', $admin->id);
        SystemConfiguration::setVal('hospital_image', 'https://images.unsplash.com/photo-1586773860418-d3b3b998de55?auto=format&fit=crop&w=1200&q=80', $admin->id);

        SystemConfiguration::setVal('fonnte_token', 'fnnt_62b78d3ac12456_mock_token', $admin->id);
        SystemConfiguration::setVal('whatsapp_gateway_status', 'Aktif', $admin->id);
        SystemConfiguration::setVal('otp_status', 'Aktif', $admin->id);
        SystemConfiguration::setVal('otp_length', '6', $admin->id);
        SystemConfiguration::setVal('otp_expired_minutes', '5', $admin->id);
        SystemConfiguration::setVal('otp_max_attempt', '3', $admin->id);
        SystemConfiguration::setVal('otp_resend_cooldown', '60', $admin->id);
        SystemConfiguration::setVal('default_country_code', '62', $admin->id);
        SystemConfiguration::setVal('otp_message_template', "Halo {{nama_pasien}},\n\nKode OTP pendaftaran akun SISMED Anda adalah:\n\n{{kode_otp}}\n\nKode ini berlaku selama {{masa_berlaku}} menit.\nJangan berikan kode ini kepada siapa pun.\n\nTerima kasih.\nSISMED - Sistem Informasi & Diagnosa Medis Terpadu", $admin->id);

        // Predefined Doctor Schedules & Info
        $doctorSchedules = [
            [
                'nama' => 'dr. Ika Safira, Sp.PD',
                'spesialis' => 'Spesialis Penyakit Dalam',
                'jadwal' => 'Jumat, 09.30 - 10.30',
                'lokasi' => 'Rumah Sakit Bhayangkara LEMDIKLAT',
                'foto' => 'https://images.unsplash.com/photo-1594824813573-246434de83fb?auto=format&fit=crop&w=150&q=80'
            ],
            [
                'nama' => 'dr. Zeth Boroh, Sp.KO',
                'spesialis' => 'Spesialis Kedokteran Olahraga',
                'jadwal' => 'Jumat, 16.00 - 17.00',
                'lokasi' => 'Rumah Sakit Bhayangkara LEMDIKLAT',
                'foto' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=150&q=80'
            ],
            [
                'nama' => 'Rafael Aditya Marjoto, M.Psi.',
                'spesialis' => 'Psikolog Klinis',
                'jadwal' => 'Kamis, 16.00 - 17.00',
                'lokasi' => 'Rumah Sakit Bhayangkara LEMDIKLAT',
                'foto' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=150&q=80'
            ]
        ];
        SystemConfiguration::setVal('doctor_schedules', json_encode($doctorSchedules), $admin->id);

        $hospitalServices = [
            ['title' => 'Layanan PCR Swab', 'desc' => 'Pemeriksaan PCR swab mandiri atau kelompok, hasil cepat dalam 12-24 jam.', 'icon' => 'beaker'],
            ['title' => 'Vaksinasi Influenza', 'desc' => 'Vaksin influenza kuadrivalen musiman untuk menjaga daya tahan tubuh.', 'icon' => 'shield-check'],
            ['title' => 'Poli Penyakit Dalam', 'desc' => 'Pemeriksaan komprehensif penyakit dalam oleh dr. Ika Safira, Sp.PD.', 'icon' => 'heart'],
            ['title' => 'Klinik Kedokteran Olahraga', 'desc' => 'Konsultasi kebugaran dan cedera olahraga bersama dr. Zeth Boroh, Sp.KO.', 'icon' => 'user-group']
        ];
        SystemConfiguration::setVal('hospital_services', json_encode($hospitalServices), $admin->id);

        // Predefined Screening Questions
        $screeningQuestions = [
            [
                'id' => 1,
                'question' => 'Apakah Anda mengalami sesak napas berat atau nyeri dada hebat yang tiba-tiba?',
                'options' => [
                    ['text' => 'Ya, sangat sesak / nyeri dada hebat', 'weight' => 'severe'],
                    ['text' => 'Tidak, bernapas dengan normal', 'weight' => 'normal']
                ]
            ],
            [
                'id' => 2,
                'question' => 'Berapakah suhu tubuh Anda saat ini?',
                'options' => [
                    ['text' => 'Demam Tinggi (di atas 38.5°C)', 'weight' => 'severe_temp'],
                    ['text' => 'Demam Ringan (37.5°C - 38.5°C)', 'weight' => 'mild_temp'],
                    ['text' => 'Normal (di bawah 37.5°C)', 'weight' => 'normal']
                ]
            ],
            [
                'id' => 3,
                'question' => 'Apakah Anda memiliki batuk pilek hebat atau tenggorokan sangat nyeri?',
                'options' => [
                    ['text' => 'Ya, batuk pilek terus-menerus', 'weight' => 'mild'],
                    ['text' => 'Tidak ada gejala flu', 'weight' => 'normal']
                ]
            ],
            [
                'id' => 4,
                'question' => 'Siapakah pasien yang akan berobat?',
                'options' => [
                    ['text' => 'Anak-anak (di bawah 12 tahun)', 'weight' => 'pediatric'],
                    ['text' => 'Dewasa / Lansia', 'weight' => 'general']
                ]
            ]
        ];
        SystemConfiguration::setVal('screening_questions', json_encode($screeningQuestions), $admin->id);

        // 3b. Master data penyakit (dikelola Dokter IGD)
        $diseaseList = [
            ['nama_penyakit' => 'ISPA (Infeksi Saluran Pernapasan Akut)', 'kode_icd' => 'J06.9', 'kategori' => 'Umum', 'gejala_umum' => 'Batuk, pilek, nyeri tenggorokan, demam ringan', 'tindakan' => 'Rawat jalan Poli Umum, terapi simtomatik'],
            ['nama_penyakit' => 'Demam Berdarah Dengue (DBD)', 'kode_icd' => 'A90', 'kategori' => 'Gawat Darurat', 'gejala_umum' => 'Demam tinggi mendadak, nyeri otot/sendi, bintik merah', 'tindakan' => 'Observasi IGD, cek trombosit, rawat inap bila perlu'],
            ['nama_penyakit' => 'Gastritis Akut', 'kode_icd' => 'K29.7', 'kategori' => 'Umum', 'gejala_umum' => 'Nyeri ulu hati, mual, kembung', 'tindakan' => 'Rawat jalan, terapi antasida, edukasi pola makan'],
            ['nama_penyakit' => 'Hipertensi', 'kode_icd' => 'I10', 'kategori' => 'Umum', 'gejala_umum' => 'Sakit kepala, tengkuk berat, tekanan darah > 140/90', 'tindakan' => 'Kontrol rutin Poli Umum, terapi antihipertensi'],
            ['nama_penyakit' => 'Demam Tifoid', 'kode_icd' => 'A01.0', 'kategori' => 'Umum', 'gejala_umum' => 'Demam naik bertahap terutama sore-malam, gangguan pencernaan', 'tindakan' => 'Cek widal/IgM Salmonella, rawat inap bila berat'],
            ['nama_penyakit' => 'Diare Akut Anak', 'kode_icd' => 'A09', 'kategori' => 'Anak', 'gejala_umum' => 'BAB cair > 3x/hari, lemas, tanda dehidrasi', 'tindakan' => 'Rehidrasi oralit/zink, rujuk Poli Anak'],
            ['nama_penyakit' => 'Angina Pektoris / Nyeri Dada Kardiak', 'kode_icd' => 'I20.9', 'kategori' => 'Gawat Darurat', 'gejala_umum' => 'Nyeri dada kiri menjalar, sesak, keringat dingin', 'tindakan' => 'Penanganan IGD segera, EKG, observasi intensif'],
        ];
        foreach ($diseaseList as $d) {
            Disease::create($d);
        }

        // 4. Diagnoses history seeds
        // Budi
        Diagnosis::create([
            'user_id' => $patient->id,
            'jenis_layanan' => 'kurang_sehat',
            'diagnosa_singkat' => 'Demam Tinggi dan Menggigil',
            'screening_answers' => [1 => 'Tidak, bernapas dengan normal', 2 => 'Demam Tinggi (di atas 38.5°C)', 3 => 'Tidak ada gejala flu', 4 => 'Dewasa / Lansia'],
            'screening_result' => 'Disarankan ke Poli Umum',
            'survey_facilities' => 5,
            'survey_cleanliness' => 4,
            'survey_doctor' => 5,
            'survey_pharmacy' => 4,
            'status_survei' => 'Survei Selesai',
            'verification_status' => 'Terverifikasi',
            'verified_penyakit' => 'Demam Tifoid',
            'catatan_dokter' => 'Pemeriksaan fisik sinkron dengan hasil screening. Suhu 38.9°C, lidah kotor. Dirujuk ke Poli Umum dengan pemeriksaan lab lanjutan.',
            'verified_by' => $dokter->id,
            'verified_at' => now()->subDays(9),
            'profit_amount' => 150000.00,
            'created_at' => now()->subDays(10),
        ]);

        Diagnosis::create([
            'user_id' => $patient->id,
            'jenis_layanan' => 'kurang_sehat',
            'diagnosa_singkat' => 'Sakit Kepala Anak dan Panas',
            'screening_answers' => [1 => 'Tidak, bernapas dengan normal', 2 => 'Demam Ringan (37.5°C - 38.5°C)', 3 => 'Ya, batuk pilek terus-menerus', 4 => 'Anak-anak (di bawah 12 tahun)'],
            'screening_result' => 'Disarankan ke Poli Anak',
            'survey_facilities' => 4,
            'survey_cleanliness' => 5,
            'survey_doctor' => 5,
            'survey_pharmacy' => 5,
            'status_survei' => 'Survei Selesai',
            'verification_status' => 'Terverifikasi',
            'verified_penyakit' => 'ISPA (Infeksi Saluran Pernapasan Akut)',
            'catatan_dokter' => 'Anak demam ringan disertai batuk pilek, kondisi stabil. Hasil screening sesuai, dirujuk Poli Anak.',
            'verified_by' => $dokter->id,
            'verified_at' => now()->subDays(4),
            'profit_amount' => 245000.00,
            'created_at' => now()->subDays(5),
        ]);

        Diagnosis::create([
            'user_id' => $patient->id,
            'jenis_layanan' => 'kurang_sehat',
            'diagnosa_singkat' => 'Sesak Napas Berat & Nyeri Dada',
            'screening_answers' => [1 => 'Ya, sangat sesak / nyeri dada hebat', 2 => 'Demam Ringan (37.5°C - 38.5°C)', 3 => 'Tidak ada gejala flu', 4 => 'Dewasa / Lansia'],
            'screening_result' => 'Disarankan ke IGD',
            'survey_facilities' => 5,
            'survey_cleanliness' => 5,
            'survey_doctor' => 5,
            'survey_pharmacy' => 5,
            'status_survei' => 'Survei Selesai',
            'verification_status' => 'Menunggu Verifikasi',
            'profit_amount' => 1250000.00,
            'created_at' => now()->subDays(2),
        ]);

        // Siti Rahma
        Diagnosis::create([
            'user_id' => $patient2->id,
            'jenis_layanan' => 'kurang_sehat',
            'diagnosa_singkat' => 'Flu Berat',
            'screening_answers' => [1 => 'Tidak, bernapas dengan normal', 2 => 'Demam Ringan (37.5°C - 38.5°C)', 3 => 'Ya, batuk pilek terus-menerus', 4 => 'Dewasa / Lansia'],
            'screening_result' => 'Disarankan ke Poli Umum',
            'survey_facilities' => 3,
            'survey_cleanliness' => 4,
            'survey_doctor' => 4,
            'survey_pharmacy' => 3,
            'status_survei' => 'Survei Selesai',
            'verification_status' => 'Menunggu Verifikasi',
            'profit_amount' => 180000.00,
            'created_at' => now()->subDays(4),
        ]);

        // Siti Rahma - kunjungan kontrol (survei kepuasan via barcode)
        Diagnosis::create([
            'user_id' => $patient2->id,
            'jenis_layanan' => 'kontrol',
            'diagnosa_singkat' => 'Kunjungan Kontrol',
            'survey_facilities' => 5,
            'survey_cleanliness' => 4,
            'survey_doctor' => 5,
            'survey_pharmacy' => 4,
            'status_survei' => 'Selesai & Diarsipkan',
            'profit_amount' => 0,
            'created_at' => now()->subDays(1),
        ]);

        // Randi Wijaya
        Diagnosis::create([
            'user_id' => $patient3->id,
            'jenis_layanan' => 'kurang_sehat',
            'diagnosa_singkat' => 'Demam Anak dan Rewet',
            'screening_answers' => [1 => 'Tidak, bernapas dengan normal', 2 => 'Demam Tinggi (di atas 38.5°C)', 3 => 'Tidak ada gejala flu', 4 => 'Anak-anak (di bawah 12 tahun)'],
            'screening_result' => 'Disarankan ke Poli Anak',
            'survey_facilities' => 4,
            'survey_cleanliness' => 4,
            'survey_doctor' => 5,
            'survey_pharmacy' => 4,
            'status_survei' => 'Survei Selesai',
            'verification_status' => 'Menunggu Verifikasi',
            'profit_amount' => 310000.00,
            'created_at' => now()->subDays(3),
        ]);

        // 4a2. Histori survei lintas 6 bulan terakhir (untuk grafik tren indeks kepuasan)
        $patientsPool = [$patient, $patient2, $patient3];
        $resultProfit = [
            'Disarankan ke Poli Umum' => 150000,
            'Disarankan ke Poli Anak' => 245000,
            'Disarankan ke IGD' => 1250000,
        ];
        $penyakitPool = ['ISPA (Infeksi Saluran Pernapasan Akut)', 'Gastritis Akut', 'Hipertensi', 'Demam Tifoid'];
        $resultKeys = array_keys($resultProfit);

        for ($m = 6; $m >= 1; $m--) {
            $perMonth = rand(3, 5);
            for ($i = 0; $i < $perMonth; $i++) {
                $date = now()->subMonthsNoOverflow($m)->startOfMonth()->addDays(rand(0, 26))->addHours(rand(8, 18));
                $p = $patientsPool[array_rand($patientsPool)];
                $resultKey = $resultKeys[array_rand($resultKeys)];

                Diagnosis::create([
                    'user_id' => $p->id,
                    'jenis_layanan' => 'kurang_sehat',
                    'diagnosa_singkat' => 'Riwayat kunjungan pemeriksaan',
                    'screening_answers' => [1 => 'Tidak, bernapas dengan normal', 2 => 'Demam Ringan (37.5°C - 38.5°C)', 3 => 'Tidak ada gejala flu', 4 => 'Dewasa / Lansia'],
                    'screening_result' => $resultKey,
                    'survey_facilities' => rand(3, 5),
                    'survey_cleanliness' => rand(3, 5),
                    'survey_doctor' => rand(4, 5),
                    'survey_pharmacy' => rand(3, 5),
                    'status_survei' => 'Survei Selesai',
                    'verification_status' => 'Terverifikasi',
                    'verified_penyakit' => $penyakitPool[array_rand($penyakitPool)],
                    'verified_by' => $dokter->id,
                    'verified_at' => $date,
                    'profit_amount' => $resultProfit[$resultKey],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }

        // 4b. Login attempts (monitoring login gagal untuk Admin)
        LoginAttempt::create([
            'user_id' => $patient->id,
            'identifier' => 'budi@example.com',
            'status' => 'Gagal',
            'reason' => 'Password salah',
            'ip_address' => '114.10.22.101',
            'user_agent' => 'Mozilla/5.0 (Linux; Android 14)',
            'created_at' => now()->subHours(5),
        ]);
        LoginAttempt::create([
            'user_id' => $patient2->id,
            'identifier' => '628777777777',
            'status' => 'Gagal',
            'reason' => 'Password salah',
            'ip_address' => '36.84.91.12',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5)',
            'created_at' => now()->subHours(3),
        ]);
        LoginAttempt::create([
            'user_id' => null,
            'identifier' => 'andi@example.com',
            'status' => 'Gagal',
            'reason' => 'Username/Email/No WhatsApp tidak terdaftar',
            'ip_address' => '103.147.8.55',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subHours(2),
        ]);
        LoginAttempt::create([
            'user_id' => $patient->id,
            'identifier' => 'budi@example.com',
            'status' => 'Berhasil',
            'reason' => null,
            'ip_address' => '114.10.22.101',
            'user_agent' => 'Mozilla/5.0 (Linux; Android 14)',
            'created_at' => now()->subHour(),
        ]);

        // 5. Audit logs
        AuditLog::create([
            'admin_id' => $admin->id,
            'activity' => 'Konfigurasi Fonnte Gateway diperbarui',
            'module' => 'Fonnte / WhatsApp',
            'old_value' => 'null',
            'new_value' => 'fnnt_62b78d3ac12456_mock_token',
            'ip_address' => '127.0.0.1'
        ]);

        AuditLog::create([
            'admin_id' => $admin->id,
            'activity' => 'Menambahkan Pertanyaan Screening Baru',
            'module' => 'Screening',
            'old_value' => '[]',
            'new_value' => 'Pertanyaan 1-4',
            'ip_address' => '127.0.0.1'
        ]);
    }
}
