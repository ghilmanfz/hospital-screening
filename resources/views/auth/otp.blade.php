@extends('layouts.app')

@section('title', 'Verifikasi WhatsApp OTP')

@section('content')
<section class="py-20 bg-slate-50 flex flex-col items-center justify-center min-h-[calc(100vh-80px-200px)]">
    <div class="max-w-md w-full mx-4">
        
        <!-- OTP Card -->
        <div class="bg-white border border-slate-200/60 shadow-xl rounded-3xl p-8 sm:p-10 relative overflow-hidden">
            <!-- Header branding -->
            <div class="text-center space-y-2 mb-8">
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-navy-900 font-sans">Verifikasi No. WhatsApp</h2>
                <p class="text-xs text-slate-500">Kami telah mengirimkan kode verifikasi OTP ke nomor:</p>
                <p class="font-bold text-navy-900 text-sm tracking-wide bg-slate-50 border border-slate-100 rounded-lg px-3 py-1.5 inline-block">
                    <i class="fa-brands fa-whatsapp text-emerald-700 mr-1 text-base align-middle"></i>
                    <span>+{{ $user->phone_number }}</span>
                </p>
            </div>

            <!-- Alerts -->
            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mr-3"></i>
                    <p class="text-xs font-semibold text-red-800">{{ $errors->first() }}</p>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-xl mb-6">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check text-emerald-600 mr-3"></i>
                    <p class="text-xs font-semibold text-emerald-850">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <!-- Verification Form -->
            <form action="{{ route('auth.otp') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="otp" class="block text-xs font-bold text-navy-900 uppercase tracking-wider text-center mb-3">Masukkan Kode OTP</label>
                    <div class="relative max-w-[240px] mx-auto">
                        <input type="text" name="otp" id="otp" maxlength="8" placeholder="######" required autofocus autocomplete="off"
                            class="block w-full text-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-xl font-bold tracking-[0.6em] outline-none text-slate-800 placeholder-slate-300">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full text-sm font-bold text-white bg-gradient-mayapada py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        <span>Verifikasi OTP</span>
                    </button>
                </div>
            </form>

            <!-- Resend controls with countdown and JS timer -->
            <div class="border-t border-slate-100 mt-8 pt-6 text-center space-y-4">
                <div x-data="otpTimer(300, 60)" class="text-xs text-slate-500">
                    <p class="mb-3">
                        Berlaku selama: <span class="font-bold text-navy-900" x-text="formatTime(expirySeconds)">05:00</span>
                    </p>
                    
                    <div class="flex items-center justify-center space-x-2">
                        <template x-if="cooldownSeconds > 0">
                            <span class="text-slate-400 font-medium">
                                Kirim ulang OTP tersedia dalam <span class="font-bold" x-text="cooldownSeconds">60</span>s
                            </span>
                        </template>
                        <template x-if="cooldownSeconds <= 0">
                            <a href="{{ route('auth.otp.resend') }}" class="font-bold text-emerald-700 hover:underline flex items-center justify-center space-x-1">
                                <i class="fa-solid fa-arrow-rotate-right text-[10px]"></i>
                                <span>Kirim Ulang OTP</span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- MOCK SIMULATION BOX (WOW FACTOR FOR USER TESTING) -->
            @if(!empty($rawOtpForTesting))
            <div class="mt-8 bg-emerald-900 text-white rounded-2xl p-4 shadow-inner border border-emerald-800 relative">
                <!-- Glowing indicator -->
                <span class="absolute top-3 right-3 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-300"></span>
                </span>
                
                <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest block mb-2">
                    <i class="fa-brands fa-whatsapp text-emerald-300 text-sm align-middle mr-1"></i>Simulasi WhatsApp (Fonnte Gateway)
                </span>
                
                <div class="bg-emerald-950/80 rounded-xl p-3 border border-emerald-900/50 text-xs font-mono select-all leading-relaxed whitespace-pre-line text-slate-200">
Halo {{ $user->name }},

Kode OTP pendaftaran akun Portal Rumah Sakit Anda adalah:

<span class="text-lg font-bold text-white tracking-widest my-1 block text-center bg-emerald-900/50 py-1 rounded border border-emerald-800">{{ $rawOtpForTesting }}</span>

Kode ini berlaku selama 5 menit.
Jangan berikan kode ini kepada siapa pun.

Terima kasih.
Portal Rumah Sakit
                </div>
                <span class="text-[9px] text-emerald-400 block mt-2 text-center font-sans font-medium">
                    *Gunakan kode di atas untuk memverifikasi pendaftaran dalam demo ini.
                </span>
            </div>
            @endif

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function otpTimer(expirySec, cooldownSec) {
        return {
            expirySeconds: expirySec,
            cooldownSeconds: cooldownSec,
            init() {
                // Countdown timer
                let expiryInterval = setInterval(() => {
                    if (this.expirySeconds > 0) {
                        this.expirySeconds--;
                    } else {
                        clearInterval(expiryInterval);
                    }
                }, 1000);

                // Cooldown timer for resend button
                let cooldownInterval = setInterval(() => {
                    if (this.cooldownSeconds > 0) {
                        this.cooldownSeconds--;
                    } else {
                        clearInterval(cooldownInterval);
                    }
                }, 1000);
            },
            formatTime(seconds) {
                const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                const s = (seconds % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            }
        }
    }
</script>
@endsection
