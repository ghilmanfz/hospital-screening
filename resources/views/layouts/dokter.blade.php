@extends('layouts.app')

@section('title')
    Dokter IGD - @yield('dokter-title')
@endsection

@section('content')
<div class="bg-slate-50 min-h-[calc(100vh-80px-200px)] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Dokter Sidebar Navigation -->
            <div class="lg:col-span-3 bg-white border border-slate-200/60 rounded-3xl p-6 shadow-xl space-y-6">
                <div>
                    <h3 class="font-extrabold text-navy-950 text-base">Dashboard Dokter IGD</h3>
                    <p class="text-[11px] text-slate-400">Verifikasi diagnosa, kelola screening & penyakit</p>
                </div>

                <nav class="space-y-1.5">
                    <!-- Verifikasi Diagnosa -->
                    <a href="{{ route('dokter.dashboard') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'dokter.dashboard' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-stamp text-sm"></i>
                        <span>Verifikasi Diagnosa</span>
                    </a>

                    <!-- Kelola Screening -->
                    <a href="{{ route('dokter.screening') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'dokter.screening' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-file-waveform text-sm"></i>
                        <span>Kelola Screening</span>
                    </a>

                    <!-- Kelola Penyakit -->
                    <a href="{{ route('dokter.penyakit') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'dokter.penyakit' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-disease text-sm"></i>
                        <span>Kelola Penyakit</span>
                    </a>
                </nav>

                <div class="border-t border-slate-100 pt-5 space-y-2">
                    <div class="bg-navy-50 border border-navy-100 rounded-xl p-3 text-[10px] text-navy-900/80 font-medium leading-relaxed">
                        <p class="font-bold mb-0.5"><i class="fa-solid fa-circle-info mr-1"></i>Akun Bersama IGD</p>
                        <p>Akun ini digunakan bersama oleh tenaga kesehatan IGD (dokter/perawat) dan dibuatkan oleh Admin sistem.</p>
                    </div>
                </div>
            </div>

            <!-- Dokter Content Panel -->
            <div class="lg:col-span-9 space-y-8">
                @yield('dokter-content')
            </div>

        </div>
    </div>
</div>
@endsection
