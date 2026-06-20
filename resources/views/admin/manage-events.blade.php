@extends('layouts.admin')

@section('admin-title', 'Kelola Acara')

@section('admin-content')
@php
    $iconOptions = [
        'fa-calendar-check'      => 'Kalender (Acara Umum)',
        'fa-hand-holding-medical'=> 'Tangan Medis (Bakti Sosial)',
        'fa-people-group'        => 'Grup Orang (Edukasi/Penyuluhan)',
        'fa-droplet'             => 'Tetesan (Donor Darah)',
        'fa-heart-pulse'         => 'Detak Jantung (Senam Sehat)',
        'fa-syringe'             => 'Suntik (Vaksinasi)',
        'fa-stethoscope'         => 'Stetoskop (Pemeriksaan)',
        'fa-notes-medical'       => 'Catatan Medis (Konsultasi)',
        'fa-baby'                => 'Bayi (Posyandu/Anak)',
        'fa-virus'               => 'Virus (Pencegahan Penyakit)',
    ];
@endphp

<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6"
    x-data="{ events: {{ json_encode(array_values($events)) }} }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Kelola Acara Rumah Sakit</h3>
            <p class="text-xs text-slate-400">Atur kegiatan & program kesehatan yang tampil di dashboard pasien (bakti sosial, donor darah, edukasi, dll)</p>
        </div>
        <div class="h-10 w-10 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
    </div>

    <!-- Alert success -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl flex items-center">
        <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ route('admin.events') }}" method="POST" class="space-y-6">
        @csrf

        <div class="space-y-4">
            <template x-for="(ev, index) in events" :key="index">
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl relative">
                    <button type="button" @click="events.splice(index, 1)"
                        class="absolute top-4 right-4 h-7 w-7 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 pr-8">
                        <!-- Live preview chip -->
                        <div class="lg:col-span-3">
                            <div class="bg-white border border-slate-200 rounded-2xl p-4 h-full flex flex-col items-center justify-center text-center space-y-2">
                                <div class="h-12 w-12 rounded-xl bg-navy-50 border border-navy-100 flex items-center justify-center text-navy-900 text-lg">
                                    <i class="fa-solid" :class="ev.icon || 'fa-calendar-check'"></i>
                                </div>
                                <span class="text-[11px] font-bold text-navy-900 leading-snug" x-text="ev.title || 'Nama Acara'"></span>
                                <span class="text-[9px] font-semibold text-emerald-700" x-text="ev.date || 'Jadwal'"></span>
                            </div>
                        </div>

                        <!-- Fields -->
                        <div class="lg:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Acara</label>
                                <input type="text" :name="'event['+index+'][title]'" x-model="ev.title" required placeholder="Contoh: Bakti Sosial & Pemeriksaan Kesehatan"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Deskripsi Singkat</label>
                                <textarea :name="'event['+index+'][desc]'" x-model="ev.desc" rows="2" placeholder="Jelaskan kegiatan acara secara singkat..."
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium"></textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Tanggal / Jadwal</label>
                                <input type="text" :name="'event['+index+'][date]'" x-model="ev.date" placeholder="Contoh: Setiap Jumat"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Waktu</label>
                                <input type="text" :name="'event['+index+'][time]'" x-model="ev.time" placeholder="Contoh: 08.00 - 11.00 WIB"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lokasi</label>
                                <input type="text" :name="'event['+index+'][location]'" x-model="ev.location" placeholder="Contoh: Lobi Utama Rumah Sakit"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Ikon</label>
                                <select :name="'event['+index+'][icon]'" x-model="ev.icon"
                                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">
                                    @foreach($iconOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty state -->
            <template x-if="events.length === 0">
                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-8 text-center">
                    <i class="fa-regular fa-calendar text-slate-300 text-3xl mb-2"></i>
                    <p class="text-sm font-semibold text-slate-400">Belum ada acara. Klik tombol di bawah untuk menambahkan.</p>
                </div>
            </template>

            <button type="button" @click="events.push({title: '', desc: '', date: '', time: '', location: '', icon: 'fa-calendar-check'})"
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center justify-center space-x-1 py-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Tambah Acara Baru</span>
            </button>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 pt-5">
            <p class="text-[10px] text-slate-400 font-semibold">
                <i class="fa-solid fa-circle-info mr-1"></i>Acara akan langsung tampil di dashboard setiap pasien setelah disimpan.
            </p>
            <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Acara</span>
            </button>
        </div>
    </form>
</div>
@endsection
