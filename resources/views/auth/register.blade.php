@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
<section class="py-20 bg-slate-50 flex items-center justify-center min-h-[calc(100vh-80px-200px)]">
    <div class="max-w-md w-full mx-4">
        
        <!-- Register Card -->
        <div class="bg-white border border-slate-200/60 shadow-xl rounded-3xl p-8 sm:p-10 relative overflow-hidden">
            <!-- Header branding -->
            <div class="text-center space-y-2 mb-8">
                <div class="h-12 w-12 rounded-2xl bg-gradient-bhayangkara flex items-center justify-center mx-auto shadow-md">
                    <i class="fa-solid fa-user-plus text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-navy-900">Daftar Akun Pasien</h2>
                <p class="text-xs text-slate-500">Buat akun SISMED untuk kuisioner diagnosa, screening mandiri & survei layanan</p>
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

            <!-- Form -->
            <form action="{{ route('auth.register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </span>
                        <input type="text" name="name" id="name" placeholder="Contoh: Budi Santoso" required value="{{ old('name') }}"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                </div>

                <div>
                    <label for="phone_number" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-brands fa-whatsapp text-base font-bold"></i>
                        </span>
                        <input type="tel" name="phone_number" id="phone_number" placeholder="Contoh: 089987654321 atau 62899..." required value="{{ old('phone_number') }}"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                    <span class="text-[10px] text-slate-400 block mt-1">Gunakan nomor aktif untuk menerima kode verifikasi OTP WhatsApp.</span>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" id="email" placeholder="Contoh: budi@example.com" value="{{ old('email') }}"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-solid fa-venus-mars text-sm"></i>
                            </span>
                            <select name="gender" id="gender" required
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih...</option>
                                <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="birth_date" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-regular fa-calendar text-sm"></i>
                            </span>
                            <input type="date" name="birth_date" id="birth_date" required value="{{ old('birth_date') }}" max="{{ date('Y-m-d') }}"
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Alamat Rumah</label>
                    <div class="relative">
                        <span class="absolute top-3 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-location-dot text-sm"></i>
                        </span>
                        <textarea name="address" id="address" rows="2" required placeholder="Contoh: Jl. Merdeka No. 12, RT 03/RW 05, Jakarta Selatan"
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="password" placeholder="Min. 6 karakter" required
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-1.5">Konfirmasi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" required
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full text-sm font-bold text-white bg-gradient-bhayangkara py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                        <span>Daftar & Kirim OTP WhatsApp</span>
                        <i class="fa-brands fa-whatsapp text-sm font-bold"></i>
                    </button>
                </div>
            </form>

            <!-- Footer links -->
            <div class="border-t border-slate-100 mt-8 pt-6 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun pasien? 
                    <a href="{{ route('auth.login') }}" class="font-bold text-emerald-700 hover:underline ml-1">Masuk Saja</a>
                </p>
            </div>

        </div>
    </div>
</section>
@endsection
