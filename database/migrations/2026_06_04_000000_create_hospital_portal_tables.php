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
        // OTP Verifications Table
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('otp_hash');
            $table->string('purpose')->default('register'); // register, forgot_password, change_phone
            $table->timestamp('expired_at');
            $table->timestamp('used_at')->nullable();
            $table->integer('attempt_count')->default(0);
            $table->string('status')->default('active'); // active, used, expired, failed
            $table->timestamps();
        });

        // System Configurations Table
        Schema::create('system_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->unique();
            $table->text('config_value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // WhatsApp Message Logs Table
        Schema::create('whatsapp_message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone_number');
            $table->string('message_type'); // otp_register, otp_forgot, etc.
            $table->text('message_content');
            $table->string('provider')->default('fonnte');
            $table->text('provider_response')->nullable();
            $table->string('status'); // Terkirim, Gagal, Menunggu, dll.
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Audit Logs Table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('activity');
            $table->string('module');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // Diagnoses (Diagnoses, Screenings, Surveys Combined)
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('diagnosa_singkat')->nullable();
            $table->text('screening_answers')->nullable(); // JSON string
            $table->string('screening_result')->nullable(); // Disarankan ke Poli Umum, IGD, Poli Anak, dll
            $table->integer('survey_facilities')->nullable();
            $table->integer('survey_cleanliness')->nullable();
            $table->integer('survey_doctor')->nullable();
            $table->integer('survey_pharmacy')->nullable();
            $table->string('status_survei')->default('Belum Mengisi Diagnosa'); // Belum Mengisi Diagnosa, Belum Screening, Belum Survei, Survei Selesai
            $table->decimal('profit_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('whatsapp_message_logs');
        Schema::dropIfExists('system_configurations');
        Schema::dropIfExists('otp_verifications');
    }
};
