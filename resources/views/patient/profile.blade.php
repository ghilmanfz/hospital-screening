@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="bg-white border border-slate-200/60 shadow-xl rounded-3xl p-8 sm:p-10">
        <!-- Header branding -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
            <div>
                <h3 class="font-extrabold text-navy-900 text-lg">Profil Saya</h3>
                <p class="text-xs text-slate-400">Atur informasi dasar akun pasien Anda</p>
            </div>
            <div class="h-10 w-10 bg-navy-50 rounded-xl flex items-center justify-center text-navy-900">
                <i class="fa-solid fa-address-card"></i>
            </div>
        </div>

        <!-- Alert messages -->
        @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl mb-6">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-emerald-700 mr-3"></i>
                <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-exclamation text-red-500 mr-3"></i>
                <p class="text-xs font-semibold text-red-800">{{ $errors->first() }}</p>
            </div>
        </div>
        @endif

        <form action="{{ route('patient.profile') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                </div>

                <!-- No WhatsApp (Readonly) -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nomor WhatsApp (Terkunci)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-450">
                            <i class="fa-brands fa-whatsapp text-slate-400 text-base font-bold"></i>
                        </span>
                        <input type="text" readonly disabled value="+{{ $user->phone_number }}"
                            class="block w-full pl-10 pr-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 font-bold text-sm cursor-not-allowed select-none">
                    </div>
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="budi@example.com"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 text-right">
                <button type="submit" class="text-sm font-bold text-white bg-gradient-mayapada px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2 ml-auto">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
