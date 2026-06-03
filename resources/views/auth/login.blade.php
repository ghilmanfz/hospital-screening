@extends('layouts.app')

@section('title', 'Masuk Akun')

@section('content')
<section class="py-20 bg-slate-50 flex items-center justify-center min-h-[calc(100vh-80px-200px)]">
    <div class="max-w-md w-full mx-4">
        
        <!-- Login Card -->
        <div class="bg-white border border-slate-200/60 shadow-xl rounded-3xl p-8 sm:p-10 relative overflow-hidden">
            <!-- Header branding -->
            <div class="text-center space-y-2 mb-8">
                <div class="h-12 w-12 rounded-2xl bg-gradient-mayapada flex items-center justify-center mx-auto shadow-md">
                    <i class="fa-solid fa-right-to-bracket text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-navy-900">Selamat Datang Kembali</h2>
                <p class="text-xs text-slate-500">Masuk ke Portal Rumah Sakit untuk akses cepat</p>
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

            @if(session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl mb-6">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-info text-blue-500 mr-3"></i>
                    <p class="text-xs font-semibold text-blue-800">{{ session('info') }}</p>
                </div>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('auth.login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email_phone" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Username / Email / No WhatsApp</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </span>
                        <input type="text" name="email_phone" id="email_phone" placeholder="budi@example.com atau 62899..." required
                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">Password</label>
                        <a href="#" class="text-xs font-semibold text-emerald-700 hover:underline">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" id="password" placeholder="Masukkan password Anda" required
                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full text-sm font-bold text-white bg-gradient-mayapada py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                    </button>
                </div>
            </form>

            <!-- Footer links -->
            <div class="border-t border-slate-100 mt-8 pt-6 text-center">
                <p class="text-xs text-slate-500">
                    Belum memiliki akun pasien? 
                    <a href="{{ route('auth.register') }}" class="font-bold text-emerald-700 hover:underline ml-1">Daftar Sekarang</a>
                </p>
                <div class="mt-4 bg-navy-50 rounded-xl p-3 text-[11px] text-navy-900/80 font-medium leading-relaxed">
                    <p class="font-bold mb-1"><i class="fa-solid fa-circle-info mr-1 text-navy-800"></i>Akun Uji Coba Default:</p>
                    <p class="font-bold text-left px-4">
                        Admin: <span class="font-mono text-emerald-700">admin@hospital.com</span> / <span class="font-mono text-emerald-700">admin123</span>
                    </p>
                    <p class="font-bold text-left px-4">
                        Pasien: <span class="font-mono text-emerald-700">budi@example.com</span> / <span class="font-mono text-emerald-700">pasien123</span>
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
