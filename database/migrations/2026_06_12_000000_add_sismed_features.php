<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Patient demographic completeness (gender, birth date, home address)
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('phone_number'); // Laki-laki, Perempuan
            $table->date('birth_date')->nullable()->after('gender');
            $table->text('address')->nullable()->after('birth_date');
        });

        // Service type & doctor verification flow on diagnoses
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->string('jenis_layanan')->default('kurang_sehat')->after('user_id'); // kurang_sehat, kontrol
            $table->string('verification_status')->nullable()->after('status_survei'); // Menunggu Verifikasi, Terverifikasi
            $table->string('verified_penyakit')->nullable()->after('verification_status');
            $table->text('catatan_dokter')->nullable()->after('verified_penyakit');
            $table->foreignId('verified_by')->nullable()->after('catatan_dokter')->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });

        // Master data penyakit (managed by Dokter IGD)
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penyakit');
            $table->string('kode_icd')->nullable();
            $table->string('kategori')->nullable(); // Umum, Anak, Gawat Darurat, dll
            $table->text('gejala_umum')->nullable();
            $table->text('tindakan')->nullable();
            $table->timestamps();
        });

        // Failed/successful login attempt log (admin monitoring)
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('identifier'); // username/email/no WA yang diketik
            $table->string('status'); // Gagal, Berhasil
            $table->string('reason')->nullable(); // Password salah, Akun tidak ditemukan, dll
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('diseases');

        Schema::table('diagnoses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['jenis_layanan', 'verification_status', 'verified_penyakit', 'catatan_dokter', 'verified_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'birth_date', 'address']);
        });
    }
};
