@extends('layouts.admin')

@section('admin-title', 'Kelola Landing Page')

@section('admin-content')
<!-- Landing Page Configurations -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Konfigurasi Teks Utama & Gambar</h3>
            <p class="text-xs text-slate-400">Atur konten hero section yang tampil di halaman publik</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-regular fa-image"></i>
        </div>
    </div>

    <!-- Alert success -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl flex items-center">
        <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ route('admin.landing') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Hospital Name -->
            <div>
                <label for="hospital_name" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Nama Rumah Sakit</label>
                <input type="text" name="hospital_name" id="hospital_name" required value="{{ $hospitalName }}"
                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
            </div>

            <!-- Hospital Logo Link -->
            <div class="space-y-2">
                <label for="hospital_logo" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">Logo Rumah Sakit</label>
                <input type="text" name="hospital_logo" id="hospital_logo" placeholder="Masukkan URL Logo..." value="{{ $hospitalLogo }}"
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-xs outline-none text-slate-800 font-medium">
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Atau Upload:</span>
                    <input type="file" name="hospital_logo_file" id="hospital_logo_file" accept="image/*"
                        class="block w-full text-xs text-slate-550 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-navy-50 file:text-navy-900 hover:file:bg-navy-100 cursor-pointer">
                </div>
            </div>

            <!-- Hero Image Link -->
            <div class="space-y-2">
                <label for="hospital_image" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">Gambar Banner (Hero)</label>
                <input type="text" name="hospital_image" id="hospital_image" value="{{ $hospitalImage }}"
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-xs outline-none text-slate-800 font-medium">
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Atau Upload:</span>
                    <input type="file" name="hospital_image_file" id="hospital_image_file" accept="image/*"
                        class="block w-full text-xs text-slate-550 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-navy-50 file:text-navy-900 hover:file:bg-navy-100 cursor-pointer">
                </div>
            </div>

            <!-- Hero Title -->
            <div class="md:col-span-3">
                <label for="hero_title" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Teks Judul Utama (Hero Title)</label>
                <input type="text" name="hero_title" id="hero_title" required value="{{ $heroTitle }}"
                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">
            </div>

            <!-- Hero Subtitle -->
            <div class="md:col-span-3">
                <label for="hero_subtitle" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Subjudul Pendukung</label>
                <textarea name="hero_subtitle" id="hero_subtitle" rows="3" required
                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 font-medium">{{ $heroSubtitle }}</textarea>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Teks & Banner</span>
            </button>
        </div>
    </form>
</div>

<!-- Doctor Schedules Editor -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6" x-data="{ schedules: {{ json_encode($doctorSchedules) }} }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Kelola Jadwal Praktik Dokter</h3>
            <p class="text-xs text-slate-400">Atur dokter spesialis, jadwal konsultasi musiman, dan ruang lokasi praktik</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
    </div>

    <form action="{{ route('admin.schedules') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="space-y-4">
            <template x-for="(sched, index) in schedules" :key="index">
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl relative space-y-4">
                    <button type="button" @click="schedules.splice(index, 1)" 
                        class="absolute top-4 right-4 h-7 w-7 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
 
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pr-8">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Dokter</label>
                            <input type="text" :name="'doctor['+index+'][nama]'" x-model="sched.nama" required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Spesialis</label>
                            <input type="text" :name="'doctor['+index+'][spesialis]'" x-model="sched.spesialis" required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Waktu / Jadwal</label>
                            <input type="text" :name="'doctor['+index+'][jadwal]'" x-model="sched.jadwal" required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lokasi</label>
                            <input type="text" :name="'doctor['+index+'][lokasi]'" x-model="sched.lokasi" required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Foto Dokter</label>
                            <input type="text" :name="'doctor['+index+'][foto]'" x-model="sched.foto" placeholder="Masukkan URL Foto..."
                                class="block w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            <div class="flex items-center space-x-1">
                                <span class="text-[9px] text-slate-400 font-bold uppercase whitespace-nowrap">Upload:</span>
                                <input type="file" :name="'doctor['+index+'][foto_file]'" accept="image/*"
                                    class="block w-full text-[10px] text-slate-500 file:mr-1 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-navy-50 file:text-navy-900 hover:file:bg-navy-100 cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="schedules.push({nama: '', spesialis: '', jadwal: '', lokasi: '', foto: ''})" 
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center justify-center space-x-1 py-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Jadwal Dokter Baru</span>
            </button>
        </div>

        <div class="text-right">
            <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Jadwal Dokter</span>
            </button>
        </div>
    </form>
</div>

<!-- Hospital Services Editor -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6" x-data="{ services: {{ json_encode($hospitalServices) }} }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Kelola Informasi Layanan</h3>
            <p class="text-xs text-slate-400">Atur kartu-kartu layanan (seperti PCR, Influenza, dll) yang ditampilkan di halaman utama</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-hand-holding-medical"></i>
        </div>
    </div>

    <form action="{{ route('admin.services') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="space-y-4">
            <template x-for="(srv, index) in services" :key="index">
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl relative space-y-4">
                    <button type="button" @click="services.splice(index, 1)" 
                        class="absolute top-4 right-4 h-7 w-7 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 pr-8">
                        <div class="md:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Layanan</label>
                            <input type="text" :name="'service['+index+'][title]'" x-model="srv.title" required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                        </div>
                        
                        <div class="md:col-span-6">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Deskripsi Singkat</label>
                            <input type="text" :name="'service['+index+'][desc]'" x-model="srv.desc" required
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Ikon (FontAwesome)</label>
                            <select :name="'service['+index+'][icon]'" x-model="srv.icon"
                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                                <option value="beaker">Tabung Reaksi (Beaker)</option>
                                <option value="shield-check">Perisai (Shield-check)</option>
                                <option value="heart">Jantung (Heart)</option>
                                <option value="user-group">Dokter / Grup (User-group)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="services.push({title: '', desc: '', icon: 'beaker'})" 
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center justify-center space-x-1 py-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Layanan Baru</span>
            </button>
        </div>

        <div class="text-right">
            <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Layanan</span>
            </button>
        </div>
    </form>
</div>
@endsection
