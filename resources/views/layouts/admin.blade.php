@extends('layouts.app')

@section('title')
    Admin - @yield('admin-title')
@endsection

@section('content')
<div class="bg-slate-50 min-h-[calc(100vh-80px-200px)] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Admin Sidebar Navigation -->
            <div class="lg:col-span-3 bg-white border border-slate-200/60 rounded-3xl p-6 shadow-xl space-y-6">
                <div>
                    <h3 class="font-extrabold text-navy-950 text-base">Dashboard Admin</h3>
                    <p class="text-[11px] text-slate-400">Kelola operasional & konfigurasi portal</p>
                </div>

                <nav class="space-y-1.5">
                    <!-- Dashboard Overview -->
                    <a href="{{ route('admin.dashboard') }}" 
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.dashboard' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-chart-pie text-sm"></i>
                        <span>Ringkasan Statistik</span>
                    </a>

                    <!-- Data Pasien -->
                    <a href="{{ route('admin.patients') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.patients' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-users text-sm"></i>
                        <span>Data Pasien</span>
                    </a>

                    <!-- Landing Page Manager -->
                    <a href="{{ route('admin.landing') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.landing' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-regular fa-window-restore text-sm"></i>
                        <span>Kelola Landing Page</span>
                    </a>

                    <!-- Screening Manager -->
                    <a href="{{ route('admin.screening') }}" 
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.screening' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-file-waveform text-sm"></i>
                        <span>Kelola Screening</span>
                    </a>

                    <!-- Diagnosis Index -->
                    <a href="{{ route('admin.diagnoses.index') }}" 
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.diagnoses.index' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-folder-open text-sm"></i>
                        <span>Indeks Diagnosa Pasien</span>
                    </a>



                    <!-- Kelola Akun -->
                    <a href="{{ route('admin.accounts') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.accounts' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-user-gear text-sm"></i>
                        <span>Kelola Akun</span>
                    </a>

                    <!-- Configurations -->
                    <a href="{{ route('admin.settings') }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition-all
                        {{ Route::currentRouteName() == 'admin.settings' ? 'bg-navy-900 text-white shadow-md' : 'text-slate-650 hover:bg-slate-50 hover:text-navy-900' }}">
                        <i class="fa-solid fa-gears text-sm"></i>
                        <span>Konfigurasi Sistem</span>
                    </a>
                </nav>

                <div class="border-t border-slate-100 pt-5 text-[10px] text-slate-450 text-center font-semibold">
                    Logged in as Admin Utama
                </div>
            </div>

            <!-- Admin Content Panel -->
            <div class="lg:col-span-9 space-y-8">
                @yield('admin-content')
            </div>

        </div>
    </div>
</div>
@endsection
