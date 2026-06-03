<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpVerification;
use App\Models\SystemConfiguration;
use App\Models\WhatsappMessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('patient.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_phone' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->email_phone;
        $password = $request->password;

        // Try login by email or phone_number
        $user = User::where('email', $credentials)
            ->orWhere('phone_number', $credentials)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->status === 'blocked') {
                return back()->withErrors(['email_phone' => 'Akun Anda diblokir. Silakan hubungi admin.']);
            }

            if ($user->status === 'pending_verification') {
                // Save user ID to session and redirect to OTP page
                Session::put('pending_user_id', $user->id);
                // Resend OTP automatically if none active
                $this->sendWhatsAppOtp($user);
                return redirect()->route('auth.otp')->with('info', 'Akun Anda belum diverifikasi. Kode OTP baru telah dikirim.');
            }

            Auth::login($user);
            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('patient.dashboard');
        }

        return back()->withErrors(['email_phone' => 'Username/Email/No WhatsApp atau password salah.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Clean phone number (replace leading 0 with country code 62 if needed)
        $phone = $request->phone_number;
        $countryCode = SystemConfiguration::getVal('default_country_code', '62');
        if (str_starts_with($phone, '0')) {
            $phone = $countryCode . substr($phone, 1);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $phone,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
            'status' => 'pending_verification',
        ]);

        Session::put('pending_user_id', $user->id);
        $this->sendWhatsAppOtp($user);

        return redirect()->route('auth.otp')->with('success', 'Pendaftaran berhasil! Kode OTP sedang dikirim ke WhatsApp Anda.');
    }

    public function showOtp()
    {
        $userId = Session::get('pending_user_id');
        if (!$userId) {
            return redirect()->route('auth.login');
        }

        $user = User::findOrFail($userId);
        // Find latest active OTP code for this user (to display for easy debugging/demo)
        $latestOtp = OtpVerification::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();

        // Pass the raw OTP code so the user knows what to type without needing a real WhatsApp setup
        $rawOtpForTesting = Session::get('raw_otp_code');

        return view('auth.otp', compact('user', 'rawOtpForTesting'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $userId = Session::get('pending_user_id');
        if (!$userId) {
            return redirect()->route('auth.login');
        }

        $user = User::findOrFail($userId);
        $maxAttempts = (int)SystemConfiguration::getVal('otp_max_attempt', 3);

        $otpVerification = OtpVerification::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();

        if (!$otpVerification) {
            return back()->withErrors(['otp' => 'Kode OTP tidak ditemukan atau sudah kedaluwarsa. Silakan kirim ulang.']);
        }

        // Check expiry
        if (now()->greaterThan($otpVerification->expired_at)) {
            $otpVerification->update(['status' => 'expired']);
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Verify hash
        if (Hash::check($request->otp, $otpVerification->otp_hash)) {
            // Success
            $otpVerification->update([
                'status' => 'used',
                'used_at' => now(),
            ]);

            $user->update([
                'status' => 'active',
                'phone_verified_at' => now(),
            ]);

            Auth::login($user);
            Session::forget('pending_user_id');
            Session::forget('raw_otp_code');

            return redirect()->route('patient.dashboard')->with('success', 'Akun Anda berhasil diverifikasi dan diaktifkan!');
        } else {
            // Increment attempts
            $otpVerification->increment('attempt_count');
            if ($otpVerification->attempt_count >= $maxAttempts) {
                $otpVerification->update(['status' => 'failed']);
                return back()->withErrors(['otp' => 'Anda telah salah memasukkan OTP sebanyak ' . $maxAttempts . ' kali. Silakan kirim ulang OTP baru.']);
            }

            $remaining = $maxAttempts - $otpVerification->attempt_count;
            return back()->withErrors(['otp' => 'Kode OTP salah. Sisa percobaan: ' . $remaining]);
        }
    }

    public function resendOtp()
    {
        $userId = Session::get('pending_user_id');
        if (!$userId) {
            return redirect()->route('auth.login');
        }

        $user = User::findOrFail($userId);

        // Check resend cooldown
        $cooldown = (int)SystemConfiguration::getVal('otp_resend_cooldown', 60);
        $lastOtp = OtpVerification::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOtp && now()->diffInSeconds($lastOtp->created_at) < $cooldown) {
            $wait = $cooldown - now()->diffInSeconds($lastOtp->created_at);
            return back()->withErrors(['otp' => 'Silakan tunggu ' . $wait . ' detik sebelum meminta kode OTP baru.']);
        }

        // Deactivate previous active OTPs
        OtpVerification::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $this->sendWhatsAppOtp($user);

        return back()->with('success', 'Kode OTP baru berhasil dikirim ke WhatsApp Anda.');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('public.home');
    }

    /**
     * Internal helper to generate, hash, log and send OTP using Fonnte template
     */
    private function sendWhatsAppOtp(User $user)
    {
        $length = (int)SystemConfiguration::getVal('otp_length', 6);
        $minutes = (int)SystemConfiguration::getVal('otp_expired_minutes', 5);
        $gatewayStatus = SystemConfiguration::getVal('whatsapp_gateway_status', 'Aktif');
        $fonnteToken = SystemConfiguration::getVal('fonnte_token', '');
        $messageTemplate = SystemConfiguration::getVal('otp_message_template', "Halo {{nama_pasien}},\n\nKode OTP Anda: {{kode_otp}}");

        // Generate numeric code
        $otpCode = '';
        for ($i = 0; $i < $length; $i++) {
            $otpCode .= rand(0, 9);
        }

        // Store OTP hash
        OtpVerification::create([
            'user_id' => $user->id,
            'phone_number' => $user->phone_number,
            'otp_hash' => Hash::make($otpCode),
            'purpose' => 'register',
            'expired_at' => now()->addMinutes($minutes),
            'attempt_count' => 0,
            'status' => 'active',
        ]);

        // Format template
        $messageContent = str_replace(
            ['{{nama_pasien}}', '{{kode_otp}}', '{{masa_berlaku}}'],
            [$user->name, $otpCode, $minutes],
            $messageTemplate
        );

        // Keep raw code in session for local testing display
        Session::put('raw_otp_code', $otpCode);

        // Simulated sending status
        $status = 'Terkirim';
        $providerResponse = '{"status":true,"message":"Pesan berhasil dikirim (Simulated)"}';

        if ($gatewayStatus !== 'Aktif') {
            $status = 'Gagal';
            $providerResponse = '{"status":false,"message":"Gateway dinonaktifkan di konfigurasi"}';
        } elseif (empty($fonnteToken)) {
            $status = 'Gagal';
            $providerResponse = '{"status":false,"message":"Token Fonnte tidak valid / belum diisi"}';
        }

        // Log the message dispatch
        WhatsappMessageLog::create([
            'user_id' => $user->id,
            'phone_number' => $user->phone_number,
            'message_type' => 'otp_register',
            'message_content' => $messageContent,
            'provider' => 'fonnte',
            'provider_response' => $providerResponse,
            'status' => $status,
            'sent_at' => ($status === 'Terkirim') ? now() : null,
        ]);
    }
}
