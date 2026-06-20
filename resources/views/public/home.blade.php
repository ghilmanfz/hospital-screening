@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-bhayangkara text-white overflow-hidden py-24 lg:py-32">
    <!-- Background grid decoration -->
    <div class="absolute inset-0 opacity-10 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px]"></div>
    <!-- Soft blue glow gradient overlay -->
    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Texts -->
            <div class="space-y-6 lg:col-span-7">
                <span class="inline-flex items-center space-x-2 bg-emerald-700/30 border border-emerald-500/30 text-emerald-300 text-xs px-3.5 py-1.5 rounded-full font-semibold uppercase tracking-wider animate-pulse">
                    <i class="fa-solid fa-staff-snake mr-1.5"></i>SISMED &mdash; Sistem Informasi & Diagnosa Medis Terpadu
                </span>
                
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-none text-white">
                    {{ $heroTitle }}
                </h1>
                
                <p class="text-lg sm:text-xl text-slate-300 font-light leading-relaxed max-w-xl">
                    {{ $heroSubtitle }}
                </p>
                
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 pt-4">
                    <a href="#services" class="text-sm font-semibold text-white bg-emerald-700 px-7 py-4 rounded-xl hover:bg-emerald-600 hover:shadow-lg hover:shadow-emerald-700/20 hover:scale-[1.02] transition-all flex items-center justify-center space-x-2">
                        <span>Pelajari Layanan</span>
                        <i class="fa-solid fa-arrow-down"></i>
                    </a>
                    
                    @guest
                    <a href="{{ route('auth.register') }}" class="text-sm font-semibold text-slate-200 border border-slate-700 px-7 py-4 rounded-xl hover:bg-white/5 hover:text-white hover:border-slate-500 transition-all flex items-center justify-center space-x-2">
                        <span>Daftar Akun Baru</span>
                        <i class="fa-solid fa-user-plus"></i>
                    </a>
                    @endguest
                </div>
            </div>

            <!-- Hero Image Frame / Mockup -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                    <!-- Deco back drop -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500 to-blue-500 rounded-3xl rotate-3 scale-105 opacity-20 blur-sm"></div>
                    
                    <div class="relative bg-slate-900/50 backdrop-blur border border-white/10 p-2.5 rounded-3xl shadow-2xl overflow-hidden aspect-[4/3] flex items-center justify-center">
                        @if(!empty($hospitalImage))
                            <img src="{{ $hospitalImage }}" alt="Rumah Sakit" class="h-full w-full object-cover rounded-2xl shadow-inner">
                        @else
                            <div class="text-center p-8">
                                <i class="fa-regular fa-image text-white/30 text-5xl mb-4"></i>
                                <p class="text-sm text-white/50">Foto Rumah Sakit</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Fitur Unggulan SISMED -->
<section id="features" class="py-24 bg-white relative overflow-hidden">
    <div class="absolute -top-20 right-0 h-64 w-64 rounded-full bg-emerald-500/5 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center space-y-3 mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Fitur Unggulan</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900">Apa yang Bisa SISMED Lakukan?</h2>
            <p class="text-sm text-slate-500 max-w-2xl mx-auto">Satu sistem terpadu yang menghubungkan pasien, Dokter IGD, dan manajemen rumah sakit — dari keluhan pertama hingga diagnosa terverifikasi.</p>
            <div class="h-1.5 w-16 bg-emerald-700 mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Fitur 1 -->
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-7 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    <i class="fa-solid fa-file-waveform text-navy-800 group-hover:text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">Screening Kesehatan Mandiri</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Merasa kurang sehat? Isi kuisioner diagnosa dari rumah dan jawab pertanyaan screening — tanpa antre di loket pendaftaran.</p>
            </div>

            <!-- Fitur 2 -->
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-7 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    <i class="fa-solid fa-route text-navy-800 group-hover:text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">Rekomendasi Unit Otomatis</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Sistem langsung mengarahkan Anda ke unit yang tepat — IGD, Poli Umum, atau Poli Anak — berdasarkan tingkat kegawatan gejala.</p>
            </div>

            <!-- Fitur 3 -->
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-7 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    <i class="fa-solid fa-user-doctor text-navy-800 group-hover:text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">Verifikasi Diagnosa Dokter IGD</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Hasil screening diverifikasi Dokter IGD setelah pemeriksaan fisik, lalu tercatat resmi sebagai data rekam medis Anda.</p>
            </div>

            <!-- Fitur 4 -->
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-7 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    <i class="fa-solid fa-square-poll-vertical text-navy-800 group-hover:text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">Survei Kepuasan & Kebersihan</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Pasien kontrol dapat menilai fasilitas, kebersihan, pelayanan dokter, dan kecepatan obat melalui barcode survei khusus.</p>
            </div>

            <!-- Fitur 5 -->
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-7 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    <i class="fa-brands fa-whatsapp text-navy-800 group-hover:text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">Keamanan OTP WhatsApp</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Pendaftaran akun diverifikasi lewat kode OTP yang dikirim langsung ke nomor WhatsApp Anda — cepat dan aman.</p>
            </div>

            <!-- Fitur 6 -->
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-7 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    <i class="fa-solid fa-clock-rotate-left text-navy-800 group-hover:text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">Riwayat Pemeriksaan Digital</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Seluruh riwayat keluhan, hasil screening, dan diagnosa terverifikasi tersimpan rapi dan dapat diakses kembali kapan saja.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid Section -->
<section id="services" class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center space-y-3 mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Layanan Kami</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900">Pemeriksaan & Informasi Layanan Utama</h2>
            <div class="h-1.5 w-16 bg-emerald-700 mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($hospitalServices as $srv)
            <div class="bg-slate-50 border border-slate-100 hover:border-emerald-500/30 p-6 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                <div class="h-12 w-12 rounded-xl bg-navy-50 group-hover:bg-emerald-700 transition-colors flex items-center justify-center mb-5">
                    @if(($srv['icon'] ?? '') == 'beaker')
                        <i class="fa-solid fa-flask text-navy-800 group-hover:text-white text-lg"></i>
                    @elseif(($srv['icon'] ?? '') == 'shield-check')
                        <i class="fa-solid fa-shield-virus text-navy-800 group-hover:text-white text-lg"></i>
                    @elseif(($srv['icon'] ?? '') == 'heart')
                        <i class="fa-solid fa-heart-pulse text-navy-800 group-hover:text-white text-lg"></i>
                    @else
                        <i class="fa-solid fa-stethoscope text-navy-800 group-hover:text-white text-lg"></i>
                    @endif
                </div>
                <h3 class="font-bold text-lg text-navy-900 mb-2 group-hover:text-emerald-700 transition-colors">{{ $srv['title'] }}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $srv['desc'] }}</p>
            </div>
            @empty
            <div class="col-span-4 text-center py-12 text-slate-400">
                Belum ada layanan rumah sakit yang dikonfigurasi.
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Doctor Schedules (Matches the uploaded Flyer theme) -->
<section id="doctors" class="py-24 bg-white relative overflow-hidden">
    <!-- Graphic accents -->
    <div class="absolute top-0 right-0 h-40 w-40 rounded-full bg-emerald-500/5 blur-2xl"></div>
    <div class="absolute bottom-0 left-0 h-40 w-40 rounded-full bg-navy-900/5 blur-2xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center space-y-3 mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-navy-900">Jadwal Praktik</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-navy-900">Jadwal Dokter Spesialis</h2>
            <div class="h-1.5 w-16 bg-navy-900 mx-auto rounded-full"></div>
        </div>

        <!-- Custom Card styled as doctor practice schedule -->
        <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200/60 shadow-2xl shadow-navy-900/10 p-8 sm:p-12">
            <!-- Header Logos -->
            <div class="flex items-center border-b border-slate-100 pb-8 mb-8">
                <!-- Hospital Logo -->
                <div class="flex items-center space-x-2">
                    @php
                        $hospitalLogo = \App\Models\SystemConfiguration::getVal('hospital_logo');
                        $hospitalName = \App\Models\SystemConfiguration::getVal('hospital_name', 'Rumah Sakit Bhayangkara LEMDIKLAT');
                    @endphp
                    @if($hospitalLogo)
                        <img src="{{ $hospitalLogo }}" alt="{{ $hospitalName }}" class="h-10 w-10 object-contain rounded-lg">
                    @else
                        <div class="h-10 w-10 bg-gradient-bhayangkara rounded-lg flex items-center justify-center text-white">
                            <i class="fa-solid fa-hospital"></i>
                        </div>
                    @endif
                    <div>
                        <span class="font-bold text-lg text-navy-900 tracking-tight block leading-tight">{{ $hospitalName }}</span>
                        <span class="font-medium text-xs text-emerald-700 tracking-wider block uppercase">Portal Digital</span>
                    </div>
                </div>
            </div>

            <!-- Flyer Title -->
            <div class="text-center mb-10">
                <i class="fa-solid fa-user-doctor text-navy-800 text-3xl mb-2"></i>
                <h3 class="text-3xl sm:text-4xl font-black text-navy-900 leading-none uppercase tracking-tight">
                    Jadwal Praktik Dokter
                </h3>
                <span class="inline-block bg-emerald-700 text-white font-bold text-xs uppercase px-3 py-1 rounded mt-2">
                    Dokter Spesialis & Layanan Poli
                </span>
                <p class="text-sm font-semibold text-slate-600 mt-4 flex items-center justify-center space-x-2">
                    <i class="fa-regular fa-calendar-check text-emerald-700"></i>
                    <span>Jadwal mengikuti praktik masing-masing dokter</span>
                    <span class="text-slate-300">|</span>
                    <i class="fa-solid fa-location-dot text-emerald-700"></i>
                    <span>Poliklinik Rumah Sakit</span>
                </p>
            </div>

            <!-- Doctors Columns (Dynamic list from Admin) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                @php
                    $chunks = array_chunk($doctorSchedules, ceil(count($doctorSchedules) / 2) ?: 1);
                @endphp
                
                @foreach($chunks as $chunkIdx => $chunkDocs)
                <div class="bg-gradient-to-b from-navy-50 to-white border border-slate-200/50 p-6 rounded-2xl relative space-y-6">
                    <div class="absolute -top-3 right-6 bg-{{ $chunkIdx == 0 ? 'navy-900' : 'emerald-700' }} text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
                        {{ $chunkIdx == 0 ? 'Daftar Dokter' : 'Jadwal Lainnya' }}
                    </div>
                    
                    <h4 class="font-bold text-navy-900 text-sm border-b border-navy-100 pb-3 mb-5 uppercase tracking-wider flex items-center">
                        <i class="fa-regular fa-calendar-days mr-2 text-navy-900"></i>Jadwal Praktik
                    </h4>

                    <div class="space-y-6">
                        @foreach($chunkDocs as $sched)
                        <div class="flex items-start space-x-4">
                            <div class="h-14 w-14 rounded-full bg-white border border-slate-200 flex-shrink-0 overflow-hidden shadow-inner flex items-center justify-center">
                                @if(!empty($sched['foto']))
                                    <img src="{{ $sched['foto'] }}" alt="{{ $sched['nama'] }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full bg-navy-50 flex items-center justify-center text-navy-800">
                                        <i class="fa-solid fa-user-doctor text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-navy-900 bg-navy-200/70 px-2 py-0.5 rounded">{{ $sched['jadwal'] }}</span>
                                <h5 class="font-bold text-slate-900 text-base mt-1">{{ $sched['nama'] }}</h5>
                                <p class="text-xs text-slate-500 font-medium">{{ $sched['spesialis'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $sched['lokasi'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

            </div>

            <!-- Schedule Information Footer -->
            <div class="mt-10 bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
                <p class="text-sm text-emerald-800 font-semibold flex items-center justify-center">
                    <i class="fa-solid fa-circle-info mr-2 text-emerald-700"></i>
                    <span>Informasi jadwal dapat berubah sewaktu-waktu. Hubungi call center untuk konfirmasi ketersediaan dokter.</span>
                </p>
            </div>

            <!-- Call Center bottom line -->
            <div class="border-t border-slate-100 mt-10 pt-6 flex flex-col sm:flex-row justify-center items-center space-y-2 sm:space-y-0 sm:space-x-8 text-sm font-bold text-navy-900">
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-500 font-medium uppercase">Call Center</span>
                    <a href="tel:150770" class="hover:text-emerald-700 transition-colors">150770</a>
                </div>
                <span class="hidden sm:inline text-slate-200">|</span>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-500 font-medium uppercase text-red-500">Emergency 24/7</span>
                    <a href="tel:150990" class="text-red-500 hover:text-red-600 transition-colors">150990</a>
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
