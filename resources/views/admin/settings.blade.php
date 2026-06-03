@extends('layouts.admin')

@section('admin-title', 'Konfigurasi Sistem')

@section('admin-content')
<!-- Configuration Tabs Panel -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Konfigurasi Gateway & Verifikasi OTP</h3>
            <p class="text-xs text-slate-400">Atur kredensial WhatsApp Fonnte, masa berlaku OTP pendaftaran, dan audit sistem</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-gears"></i>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl flex items-center mb-6">
        <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl flex items-center mb-6">
        <i class="fa-solid fa-circle-exclamation text-red-500 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-red-800">{{ $errors->first() }}</p>
    </div>
    @endif

    <!-- Main Config Form -->
    <form action="{{ route('admin.settings') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Column 1: Fonnte WhatsApp Credentials -->
            <div class="space-y-6">
                <h4 class="font-extrabold text-navy-950 text-sm tracking-wide border-b border-slate-100 pb-2 flex items-center">
                    <i class="fa-brands fa-whatsapp text-emerald-700 mr-2 text-base"></i>WhatsApp Gateway (Fonnte)
                </h4>

                <!-- Status Gateway -->
                <div>
                    <label for="whatsapp_gateway_status" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Status Gateway</label>
                    <select name="whatsapp_gateway_status" id="whatsapp_gateway_status"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                        <option value="Aktif" {{ $configs['whatsapp_gateway_status'] == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ $configs['whatsapp_gateway_status'] == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- API Key / Token Fonnte -->
                <div>
                    <label for="fonnte_token" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">API Token Fonnte</label>
                    <input type="text" name="fonnte_token" id="fonnte_token" placeholder="Masukkan token Fonnte..." value="{{ $configs['fonnte_token_masked'] }}"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-mono">
                    <span class="text-[10px] text-slate-400 block mt-1">Token disembunyikan demi keamanan. Tulis token baru jika ingin memperbarui.</span>
                </div>

                <!-- Default Country Code -->
                <div>
                    <label for="default_country_code" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Default Country Code</label>
                    <input type="text" name="default_country_code" id="default_country_code" required value="{{ $configs['default_country_code'] }}"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                    <span class="text-[10px] text-slate-400 block mt-1">Misal: 62 untuk format nomor telepon Indonesia.</span>
                </div>
            </div>

            <!-- Column 2: OTP Verification Parameters -->
            <div class="space-y-6">
                <h4 class="font-extrabold text-navy-950 text-sm tracking-wide border-b border-slate-100 pb-2 flex items-center">
                    <i class="fa-solid fa-key text-emerald-750 mr-2 text-sm text-emerald-700"></i>Parameter Verifikasi OTP
                </h4>

                <!-- OTP Status -->
                <div>
                    <label for="otp_status" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Verifikasi OTP WhatsApp</label>
                    <select name="otp_status" id="otp_status"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                        <option value="Aktif" {{ $configs['otp_status'] == 'Aktif' ? 'selected' : '' }}>Aktif (Verifikasi saat Daftar)</option>
                        <option value="Tidak Aktif" {{ $configs['otp_status'] == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- Grid OTP dimensions -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- OTP Length -->
                    <div>
                        <label for="otp_length" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Panjang Kode</label>
                        <select name="otp_length" id="otp_length"
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                            <option value="4" {{ $configs['otp_length'] == '4' ? 'selected' : '' }}>4 Digit</option>
                            <option value="5" {{ $configs['otp_length'] == '5' ? 'selected' : '' }}>5 Digit</option>
                            <option value="6" {{ $configs['otp_length'] == '6' ? 'selected' : '' }}>6 Digit</option>
                        </select>
                    </div>

                    <!-- Expiry Minutes -->
                    <div>
                        <label for="otp_expired_minutes" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Masa Berlaku</label>
                        <select name="otp_expired_minutes" id="otp_expired_minutes"
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                            <option value="3" {{ $configs['otp_expired_minutes'] == '3' ? 'selected' : '' }}>3 Menit</option>
                            <option value="5" {{ $configs['otp_expired_minutes'] == '5' ? 'selected' : '' }}>5 Menit</option>
                            <option value="10" {{ $configs['otp_expired_minutes'] == '10' ? 'selected' : '' }}>10 Menit</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Max Attempts -->
                    <div>
                        <label for="otp_max_attempt" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Batas Percobaan</label>
                        <input type="number" name="otp_max_attempt" id="otp_max_attempt" required value="{{ $configs['otp_max_attempt'] }}"
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                    </div>

                    <!-- Resend Cooldown -->
                    <div>
                        <label for="otp_resend_cooldown" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Jeda Kirim Ulang</label>
                        <input type="number" name="otp_resend_cooldown" id="otp_resend_cooldown" required value="{{ $configs['otp_resend_cooldown'] }}"
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                    </div>
                </div>
            </div>

            <!-- OTP Message Template Textarea (Full Width) -->
            <div class="lg:col-span-2">
                <label for="otp_message_template" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Template Pesan OTP WhatsApp</label>
                <textarea name="otp_message_template" id="otp_message_template" rows="5" required
                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-850 font-mono leading-relaxed">{{ $configs['otp_message_template'] }}</textarea>
                <span class="text-[10px] text-slate-400 block mt-1.5 leading-tight">
                    *Gunakan placeholder berikut: <strong>@{{nama_pasien}}</strong> (nama lengkap), <strong>@{{kode_otp}}</strong> (angka verifikasi), dan <strong>@{{masa_berlaku}}</strong> (dalam menit).
                </span>
            </div>

        </div>

        <div class="text-right">
            <button type="submit" class="text-sm font-bold text-white bg-gradient-mayapada px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Konfigurasi Sistem</span>
            </button>
        </div>
    </form>
</div>

<!-- Test Dispatch WhatsApp Box -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Uji Coba Pengiriman WhatsApp</h3>
            <p class="text-xs text-slate-400">Kirim pesan testing ke nomor tertentu untuk memastikan integrasi token API Fonnte berjalan lancar</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-paper-plane"></i>
        </div>
    </div>

    <form action="{{ route('admin.settings.test-whatsapp') }}" method="POST" class="space-y-5 max-w-2xl">
        @csrf
        
        <div>
            <label for="phone" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Nomor Telepon WhatsApp Tujuan</label>
            <input type="text" name="phone" id="phone" required placeholder="Contoh: 628998765432"
                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
        </div>

        <div>
            <label for="message" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Isi Pesan Test</label>
            <textarea name="message" id="message" rows="3" required placeholder="Tuliskan pesan percobaan di sini..."
                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">Testing koneksi WhatsApp Gateway Fonnte - Portal Rumah Sakit Mayapada.</textarea>
        </div>

        <div class="pt-2">
            <button type="submit" class="text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-600 px-6 py-3 rounded-xl hover:shadow-lg transition-all inline-flex items-center space-x-2">
                <i class="fa-brands fa-whatsapp text-base font-bold"></i>
                <span>Test Kirim Pesan</span>
            </button>
        </div>
    </form>
</div>

<!-- Delivery logs & System Audits (Vertical Side by Side or Tabs) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- WhatsApp logs -->
    <div class="lg:col-span-7 bg-white border border-slate-200/60 rounded-3xl p-8 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h3 class="font-extrabold text-navy-900 text-sm">Log Pengiriman WhatsApp</h3>
                <p class="text-[11px] text-slate-400">Log pengiriman kode verifikasi OTP WhatsApp via Fonnte</p>
            </div>
            <div class="h-8 w-8 bg-emerald-50 text-emerald-700 rounded-lg flex items-center justify-center">
                <i class="fa-regular fa-message text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto max-h-[400px]">
            <table class="w-full text-left text-xs font-medium">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 pb-2">
                        <th class="py-2.5 font-bold uppercase">Waktu</th>
                        <th class="py-2.5 font-bold uppercase">No. WhatsApp</th>
                        <th class="py-2.5 font-bold uppercase">Status</th>
                        <th class="py-2.5 font-bold uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 leading-normal">
                    @forelse($logs as $l)
                    <tr>
                        <td class="py-3 font-semibold text-slate-500 whitespace-nowrap">{{ $l->created_at->format('d-m-y H:i') }}</td>
                        <td class="py-3 font-bold text-navy-950 whitespace-nowrap">+{{ $l->phone_number }}</td>
                        <td class="py-3 whitespace-nowrap">
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded border
                                {{ $l->status == 'Terkirim' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-650 border-red-100' }}">
                                {{ $l->status }}
                            </span>
                        </td>
                        <td class="py-3 max-w-[200px] truncate" title="{{ $l->message_content }}">{{ $l->message_content }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-400">Belum ada log pengiriman pesan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- System Audits -->
    <div class="lg:col-span-5 bg-white border border-slate-200/60 rounded-3xl p-8 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h3 class="font-extrabold text-navy-900 text-sm">Audit Log Aktivitas</h3>
                <p class="text-[11px] text-slate-400">Riwayat pencatatan aktivitas perubahan konfigurasi sistem</p>
            </div>
            <div class="h-8 w-8 bg-navy-50 text-navy-900 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-list-check text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto max-h-[400px]">
            <table class="w-full text-left text-xs font-medium">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100 pb-2">
                        <th class="py-2.5 font-bold uppercase">Waktu</th>
                        <th class="py-2.5 font-bold uppercase">Aktivitas</th>
                        <th class="py-2.5 font-bold uppercase">Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 leading-normal">
                    @forelse($auditLogs as $a)
                    <tr>
                        <td class="py-3 font-semibold text-slate-500 whitespace-nowrap">{{ $a->created_at->format('d-m H:i') }}</td>
                        <td class="py-3 text-slate-750" title="Module: {{ $a->module }}">{{ $a->activity }}</td>
                        <td class="py-3 font-bold text-navy-900 whitespace-nowrap">{{ $a->admin->name ?? 'Admin' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-slate-400">Belum ada aktivitas audit log.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
