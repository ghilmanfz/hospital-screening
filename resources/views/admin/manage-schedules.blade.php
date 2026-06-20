@extends('layouts.admin')

@section('admin-title', 'Kelola Jadwal Dokter')

@section('admin-content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6"
    x-data="{ schedules: {{ json_encode(array_values($doctorSchedules)) }} }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Kelola Jadwal Praktik Dokter</h3>
            <p class="text-xs text-slate-400">Atur dokter spesialis beserta foto, jabatan/spesialisasi, jadwal praktik, dan lokasi ruang. Tampil di halaman utama publik.</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
    </div>

    <!-- Alert success -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl flex items-center">
        <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ route('admin.schedules') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <template x-for="(sched, index) in schedules" :key="index">
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl relative">
                    <button type="button" @click="schedules.splice(index, 1)"
                        class="absolute top-4 right-4 h-7 w-7 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 pr-8">
                        <!-- Foto preview + upload -->
                        <div class="lg:col-span-3" x-data="{ localPreview: '' }">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Foto Dokter</label>
                            <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col items-center text-center space-y-3">
                                <template x-if="localPreview || sched.foto">
                                    <img :src="localPreview || sched.foto" alt="Foto dokter"
                                        class="h-20 w-20 rounded-2xl object-cover border border-slate-200 shadow-sm">
                                </template>
                                <template x-if="!localPreview && !sched.foto">
                                    <div class="h-20 w-20 rounded-2xl bg-navy-50 border border-navy-100 flex items-center justify-center text-navy-300 text-2xl">
                                        <i class="fa-solid fa-user-doctor"></i>
                                    </div>
                                </template>
                                <input type="text" :name="'doctor['+index+'][foto]'" x-model="sched.foto" placeholder="URL Foto..."
                                    class="block w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[11px] outline-none focus:border-emerald-700 font-medium">
                                <input type="file" :name="'doctor['+index+'][foto_file]'" accept="image/*"
                                    @change="localPreview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : ''"
                                    class="block w-full text-[10px] text-slate-500 file:mr-1 file:py-1 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-navy-50 file:text-navy-900 hover:file:bg-navy-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- Data fields -->
                        <div class="lg:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Dokter</label>
                                <input type="text" :name="'doctor['+index+'][nama]'" x-model="sched.nama" required placeholder="Contoh: dr. Andi Wijaya, Sp.PD"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Jabatan / Spesialisasi</label>
                                <input type="text" :name="'doctor['+index+'][spesialis]'" x-model="sched.spesialis" required placeholder="Contoh: Spesialis Penyakit Dalam"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Waktu / Jadwal Praktik</label>
                                <input type="text" :name="'doctor['+index+'][jadwal]'" x-model="sched.jadwal" required placeholder="Contoh: Senin & Kamis, 09.00 - 12.00"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lokasi / Ruang Praktik</label>
                                <input type="text" :name="'doctor['+index+'][lokasi]'" x-model="sched.lokasi" required placeholder="Contoh: Poliklinik Lantai 2"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty state -->
            <template x-if="schedules.length === 0">
                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-8 text-center">
                    <i class="fa-solid fa-user-doctor text-slate-300 text-3xl mb-2"></i>
                    <p class="text-sm font-semibold text-slate-400">Belum ada jadwal dokter. Klik tombol di bawah untuk menambahkan.</p>
                </div>
            </template>

            <button type="button" @click="schedules.push({nama: '', spesialis: '', jadwal: '', lokasi: '', foto: ''})"
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center justify-center space-x-1 py-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Jadwal Dokter Baru</span>
            </button>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 pt-5">
            <p class="text-[10px] text-slate-400 font-semibold">
                <i class="fa-solid fa-circle-info mr-1"></i>Jadwal dokter tampil di halaman utama publik pada bagian "Jadwal Dokter Spesialis".
            </p>
            <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Jadwal Dokter</span>
            </button>
        </div>
    </form>
</div>
@endsection
