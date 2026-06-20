@extends('layouts.app')

@section('title', 'Dasbor Pasien')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ activeModal: null, layanan: null }">

    <!-- Top Grid: Greeting & Profile Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">

        <!-- Greeting Card -->
        <div class="lg:col-span-8 bg-gradient-bhayangkara text-white rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <div class="relative z-10 space-y-4">
                <span class="bg-white/10 border border-white/20 text-emerald-300 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full inline-block">
                    Dasbor Pasien SISMED
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    Selamat datang, <span class="text-emerald-400">{{ $user->name }}</span>!
                </h2>
                <p class="text-sm text-slate-300 leading-relaxed max-w-xl">
                    Pilih layanan sesuai kebutuhan Anda: isi kuisioner diagnosa bila merasa kurang sehat, atau isi survei kepuasan layanan & kebersihan untuk kunjungan kontrol.
                </p>
            </div>

            <div class="flex items-center space-x-6 border-t border-white/10 pt-6 mt-8 text-xs sm:text-sm text-slate-300 font-medium">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-0.5">No. WhatsApp</span>
                    <span class="text-white">+{{ $user->phone_number }}</span>
                </div>
                <div class="h-6 w-[1px] bg-white/10"></div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-0.5">Status Akun</span>
                    <span class="text-emerald-400 font-bold"><i class="fa-solid fa-circle-check mr-1 text-[10px]"></i>Aktif</span>
                </div>
                @if($user->gender || $user->birth_date)
                <div class="h-6 w-[1px] bg-white/10 hidden sm:block"></div>
                <div class="hidden sm:block">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-0.5">Profil</span>
                    <span class="text-white">{{ $user->gender ?? '-' }}{{ $user->age() !== null ? ', ' . $user->age() . ' th' : '' }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Progress/Status Step Card -->
        <div class="lg:col-span-4 bg-white border border-slate-200/60 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-navy-900 text-base mb-1">Status Alur Layanan</h3>
                <p class="text-xs text-slate-400 mb-6">Tahapan penanganan pemeriksaan saat ini</p>

                <div class="space-y-5">
                    <!-- Step 1: Pilih Layanan & Keluhan -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Belum Mengisi Diagnosa' ? 'bg-navy-900 text-white border-navy-900 glow-green' : 'bg-emerald-50 text-emerald-700 border-emerald-600' }}">
                            @if($statusSurvei != 'Belum Mengisi Diagnosa')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @else
                                1
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Belum Mengisi Diagnosa' ? 'text-navy-900' : 'text-slate-500' }}">Pilih Layanan & Isi Keluhan</span>
                    </div>

                    <!-- Step 2: Screening -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Belum Mengisi Screening' ? 'bg-navy-900 text-white border-navy-900 glow-green' : ($statusSurvei == 'Belum Mengisi Diagnosa' ? 'bg-white text-slate-400 border-slate-200' : 'bg-emerald-50 text-emerald-700 border-emerald-600') }}">
                            @if($statusSurvei != 'Belum Mengisi Diagnosa' && $statusSurvei != 'Belum Mengisi Screening')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @else
                                2
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Belum Mengisi Screening' ? 'text-navy-900' : 'text-slate-500' }}">Kuisioner Screening Mandiri</span>
                    </div>

                    <!-- Step 3: Survei -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Belum Mengisi Survei' ? 'bg-navy-900 text-white border-navy-900 glow-green' : (($statusSurvei == 'Belum Mengisi Diagnosa' || $statusSurvei == 'Belum Mengisi Screening') ? 'bg-white text-slate-400 border-slate-200' : 'bg-emerald-50 text-emerald-700 border-emerald-600') }}">
                            @if($statusSurvei == 'Survei Selesai')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @else
                                3
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Belum Mengisi Survei' ? 'text-navy-900' : 'text-slate-500' }}">Survei Kepuasan Layanan</span>
                    </div>

                    <!-- Step 4: Verifikasi Dokter -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ ($statusSurvei == 'Survei Selesai' && $latest && $latest->verification_status == 'Terverifikasi') ? 'bg-emerald-700 text-white border-emerald-700 glow-green' : ($statusSurvei == 'Survei Selesai' ? 'bg-amber-50 text-amber-600 border-amber-400' : 'bg-white text-slate-400 border-slate-200') }}">
                            @if($statusSurvei == 'Survei Selesai' && $latest && $latest->verification_status == 'Terverifikasi')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @elseif($statusSurvei == 'Survei Selesai')
                                <i class="fa-solid fa-user-doctor text-[10px]"></i>
                            @else
                                4
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Survei Selesai' ? ($latest && $latest->verification_status == 'Terverifikasi' ? 'text-emerald-700' : 'text-amber-600') : 'text-slate-500' }}">Verifikasi Dokter IGD</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 mt-6">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">Status Terkini:</span>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200/50 px-3 py-1.5 rounded-lg inline-block">
                    {{ $statusSurvei == 'Belum Mengisi Diagnosa' ? 'Silakan Pilih Layanan' : $statusSurvei }}
                </span>
                @if($statusSurvei == 'Survei Selesai' && $latest)
                    @if($latest->verification_status == 'Terverifikasi')
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-lg inline-block mt-1.5">
                            <i class="fa-solid fa-stamp mr-1"></i>Diagnosa Terverifikasi Dokter IGD
                        </span>
                    @else
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1 rounded-lg inline-block mt-1.5">
                            <i class="fa-regular fa-clock mr-1"></i>Menunggu Verifikasi Dokter IGD
                        </span>
                    @endif
                @endif
            </div>
        </div>

    </div>

    <!-- Event Rumah Sakit -->
    @if(!empty($hospitalEvents))
    <div class="bg-white border border-slate-200/60 rounded-3xl shadow-xl p-6 sm:p-8 mb-10">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-extrabold text-navy-900 text-base">Event Rumah Sakit</h3>
                <p class="text-xs text-slate-400">Informasi kegiatan dan program kesehatan yang dapat diikuti pasien</p>
            </div>
            <div class="h-10 w-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-700">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($hospitalEvents as $event)
            <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl flex items-start space-x-3.5">
                <div class="h-12 w-12 rounded-xl bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center text-navy-900">
                    <i class="fa-solid {{ $event['icon'] ?? 'fa-calendar-check' }}"></i>
                </div>
                <div class="min-w-0">
                    <h5 class="font-bold text-slate-900 text-sm leading-snug">{{ $event['title'] ?? 'Event Rumah Sakit' }}</h5>
                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed mt-1">{{ $event['desc'] ?? '' }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="text-[10px] font-bold text-navy-900 bg-navy-100/70 px-2 py-0.5 rounded inline-flex items-center">
                            <i class="fa-regular fa-calendar mr-1"></i>{{ $event['date'] ?? '-' }}
                        </span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded inline-flex items-center">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $event['time'] ?? '-' }}
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-semibold mt-2">
                        <i class="fa-solid fa-location-dot mr-1 text-emerald-700"></i>{{ $event['location'] ?? 'Area Rumah Sakit' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Active Area: Form inputs depending on current Status -->
    <div class="bg-white border border-slate-200/60 rounded-3xl shadow-xl p-8 sm:p-12 mb-10">

        <!-- Alerts -->
        @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl mb-8 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-lg"></i>
                <p class="text-sm font-semibold text-emerald-900">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if($statusSurvei == 'Belum Mengisi Diagnosa')
            <!-- Step 0: Pilih Layanan -->
            <div x-show="layanan === null" class="max-w-3xl mx-auto space-y-8">
                <div class="text-center space-y-2">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Mulai Layanan</span>
                    <h3 class="text-2xl font-extrabold text-navy-900">Apa yang Anda Butuhkan Hari Ini?</h3>
                    <p class="text-sm text-slate-400">Silakan pilih jenis layanan sesuai kondisi dan tujuan kunjungan Anda</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Pilihan 1: Merasa Kurang Sehat -->
                    <button type="button" @click="layanan = 'sakit'"
                        class="text-left bg-slate-50 border-2 border-slate-100 hover:border-emerald-600 hover:bg-emerald-50/30 p-7 rounded-3xl transition-all group hover:shadow-xl hover:-translate-y-1">
                        <div class="h-14 w-14 rounded-2xl bg-gradient-bhayangkara group-hover:scale-105 transition-transform flex items-center justify-center mb-5 shadow-md">
                            <i class="fa-solid fa-head-side-cough text-white text-xl"></i>
                        </div>
                        <h4 class="font-extrabold text-navy-900 text-lg mb-1.5 group-hover:text-emerald-700 transition-colors">Merasa Kurang Sehat</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Isi kuisioner terkait diagnosa penyakit & screening mandiri. Sistem akan merekomendasikan unit layanan (IGD / Poli), lalu diagnosa diverifikasi Dokter IGD setelah pemeriksaan fisik.
                        </p>
                        <span class="text-xs font-bold text-emerald-700 flex items-center space-x-1.5">
                            <span>Isi Kuisioner Diagnosa</span>
                            <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </button>

                    <!-- Pilihan 2: Kontrol -->
                    <button type="button" @click="layanan = 'kontrol'"
                        class="text-left bg-slate-50 border-2 border-slate-100 hover:border-emerald-600 hover:bg-emerald-50/30 p-7 rounded-3xl transition-all group hover:shadow-xl hover:-translate-y-1">
                        <div class="h-14 w-14 rounded-2xl bg-emerald-700 group-hover:scale-105 transition-transform flex items-center justify-center mb-5 shadow-md">
                            <i class="fa-solid fa-calendar-check text-white text-xl"></i>
                        </div>
                        <h4 class="font-extrabold text-navy-900 text-lg mb-1.5 group-hover:text-emerald-700 transition-colors">Kontrol / Kunjungan Rutin</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            Untuk pasien yang berkunjung kontrol: isi survei kepuasan layanan & kebersihan rumah sakit. Di lokasi, survei ini juga dapat diakses lewat barcode khusus.
                        </p>
                        <span class="text-xs font-bold text-emerald-700 flex items-center space-x-1.5">
                            <span>Isi Survei Kepuasan</span>
                            <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Step 1A: Input Keluhan (jalur Merasa Kurang Sehat) -->
            <div x-show="layanan === 'sakit'" x-cloak class="max-w-2xl mx-auto space-y-6">
                <button type="button" @click="layanan = null" class="text-xs font-bold text-slate-400 hover:text-navy-900 flex items-center space-x-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali ke Pilihan Layanan</span>
                </button>

                <div class="text-center space-y-2 mb-8">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Layanan: Merasa Kurang Sehat</span>
                    <h3 class="text-2xl font-extrabold text-navy-900">Laporkan Keluhan Utama Anda</h3>
                    <p class="text-sm text-slate-400">Tuliskan gejala atau keluhan singkat yang sedang Anda rasakan</p>
                </div>

                <form action="{{ route('patient.diagnosa') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="diagnosa_singkat" class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">Gejala Singkat</label>
                        <textarea name="diagnosa_singkat" id="diagnosa_singkat" rows="3" required placeholder="Contoh: Sakit kepala hebat, demam tinggi naik turun sejak 2 hari yang lalu disertai batuk kering."
                            class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 transition-all text-sm outline-none text-slate-800 placeholder-slate-400 font-medium"></textarea>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full sm:w-auto text-sm font-bold text-white bg-gradient-bhayangkara px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                            <span>Simpan & Lanjutkan Screening</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 1B: Survei Kontrol (jalur Kontrol) -->
            <div x-show="layanan === 'kontrol'" x-cloak class="max-w-3xl mx-auto space-y-8">
                <button type="button" @click="layanan = null" class="text-xs font-bold text-slate-400 hover:text-navy-900 flex items-center space-x-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali ke Pilihan Layanan</span>
                </button>

                <div class="text-center space-y-2">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Layanan: Kontrol / Kunjungan Rutin</span>
                    <h3 class="text-2xl font-extrabold text-navy-900">Survei Kepuasan Layanan & Kebersihan</h3>
                    <p class="text-sm text-slate-400">Penilaian Anda membantu kami mengevaluasi dan meningkatkan mutu pelayanan rumah sakit</p>
                </div>

                <!-- Barcode khusus survei kontrol -->
                <div class="bg-navy-50/60 border border-navy-100 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-5">
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm flex-shrink-0">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('patient.dashboard') . '?layanan=kontrol') }}"
                            alt="Barcode Survei Kontrol" class="h-28 w-28">
                    </div>
                    <div class="text-center sm:text-left">
                        <h4 class="font-extrabold text-navy-900 text-sm mb-1"><i class="fa-solid fa-qrcode mr-1.5 text-emerald-700"></i>Barcode Khusus Survei Kontrol</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Barcode ini dipasang di area rumah sakit (ruang tunggu poli, farmasi, kasir). Pasien kontrol cukup memindai untuk langsung membuka halaman survei ini &mdash; atau isi langsung formulir di bawah.
                        </p>
                    </div>
                </div>

                <form action="{{ route('patient.kontrol.survey') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Ratings Container -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        <!-- Aspek 1 -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                            <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                <i class="fa-solid fa-hospital-user mr-1.5 text-emerald-700"></i>Kelayakan Fasilitas
                            </label>
                            <p class="text-[11px] text-slate-400 leading-tight">Nilai kebersihan gedung, kecukupan ruang tunggu, kenyamanan tempat tidur perawatan.</p>
                            <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                <input type="range" name="survey_facilities" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                            </div>
                        </div>

                        <!-- Aspek 2 -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                            <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                <i class="fa-solid fa-soap mr-1.5 text-emerald-700"></i>Kebersihan Lingkungan
                            </label>
                            <p class="text-[11px] text-slate-400 leading-tight">Nilai sterilitas alat, sanitasi toilet, kebersihan lantai koridor, pembuangan sampah medis.</p>
                            <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                <input type="range" name="survey_cleanliness" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                            </div>
                        </div>

                        <!-- Aspek 3 -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                            <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                <i class="fa-solid fa-user-doctor mr-1.5 text-emerald-700"></i>Layanan Dokter
                            </label>
                            <p class="text-[11px] text-slate-400 leading-tight">Nilai keramahan, kejelasan penjelasan medis, kedisiplinan waktu kehadiran dokter.</p>
                            <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                <input type="range" name="survey_doctor" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                            </div>
                        </div>

                        <!-- Aspek 4 -->
                        <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                            <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                <i class="fa-solid fa-clock-rotate-left mr-1.5 text-emerald-700"></i>Kecepatan Penyerahan Obat
                            </label>
                            <p class="text-[11px] text-slate-400 leading-tight">Nilai waktu tunggu resep obat non-racikan dan racikan di apotek depo rumah sakit.</p>
                            <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                <input type="range" name="survey_pharmacy" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                            </div>
                        </div>

                    </div>

                    <div class="pt-4 text-right">
                        <button type="submit" class="w-full sm:w-auto text-sm font-bold text-white bg-gradient-bhayangkara px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                            <span>Kirim Survei Kontrol</span>
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

        @elseif($statusSurvei == 'Belum Mengisi Screening')
            <!-- Step 2: Screening Form Wizard -->
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="text-center space-y-2 mb-8">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Langkah 2 dari 3 &mdash; Layanan: Merasa Kurang Sehat</span>
                    <h3 class="text-2xl font-extrabold text-navy-900">Kuisioner Screening Kesehatan Mandiri</h3>
                    <p class="text-sm text-slate-400">Jawab beberapa pertanyaan di bawah ini untuk mendapatkan rekomendasi penanganan</p>
                </div>

                <form action="{{ route('patient.screening', $activeDiagnosisId) }}" method="POST" class="space-y-8">
                    @csrf

                    @foreach($questions as $q)
                    <div class="bg-slate-50/50 border border-slate-100 p-6 rounded-2xl space-y-4">
                        <p class="text-sm font-bold text-navy-900 flex items-start">
                            <span class="bg-navy-900 text-white rounded-lg h-5 w-5 flex items-center justify-center text-[10px] font-bold mr-2.5 mt-0.5">{{ $q['id'] }}</span>
                            <span>{{ $q['question'] }}</span>
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pl-7">
                            @foreach($q['options'] as $oIdx => $opt)
                            <label class="border border-slate-200 hover:border-emerald-700/50 bg-white p-4 rounded-xl flex items-center space-x-3 cursor-pointer transition-all hover:bg-slate-50/50">
                                <input type="radio" name="q_{{ $q['id'] }}" value="{{ $opt['text'] }}" required
                                    class="h-4 w-4 text-emerald-700 focus:ring-emerald-700 border-slate-300">
                                <span class="text-xs font-semibold text-slate-700">{{ $opt['text'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="pt-4 flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-medium">Harap jawab semua pertanyaan dengan jujur demi keselamatan medis Anda.</span>
                        <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center space-x-2">
                            <span>Selesaikan Screening</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

        @elseif($statusSurvei == 'Belum Mengisi Survei')
            <!-- Step 3: Screening Outcome Banner + Satisfaction Survey -->
            <div class="max-w-3xl mx-auto space-y-10">

                <!-- Screening Result Panel -->
                <div class="bg-gradient-bhayangkara text-white rounded-3xl p-8 relative overflow-hidden border border-white/10">
                    <div class="absolute -right-10 -bottom-10 h-32 w-32 rounded-full bg-emerald-500/10 blur-xl"></div>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 relative z-10">
                        <div class="space-y-3 text-center sm:text-left">
                            <span class="bg-emerald-700 text-white font-bold text-[10px] uppercase px-3 py-1 rounded">Hasil Screening Keluar</span>
                            <h4 class="text-2xl font-black uppercase tracking-wide">
                                {{ $latest->screening_result }}
                            </h4>
                            <p class="text-xs text-slate-300 max-w-md">
                                Berdasarkan analisis keluhan dan jawaban screening, sistem merekomendasikan Anda untuk langsung mendatangi unit pelayanan tersebut.
                            </p>
                            <p class="text-[11px] text-amber-300/90 max-w-md font-semibold">
                                <i class="fa-regular fa-clock mr-1"></i>Hasil ini akan diverifikasi oleh Dokter IGD setelah pemeriksaan fisik di rumah sakit.
                            </p>
                        </div>

                        <div class="flex-shrink-0">
                            @if($latest->screening_result == 'Disarankan ke IGD')
                                <div class="h-16 w-16 bg-red-500 rounded-full flex items-center justify-center glow-green border border-red-400">
                                    <i class="fa-solid fa-truck-medical text-white text-2xl animate-pulse"></i>
                                </div>
                            @elseif($latest->screening_result == 'Disarankan ke Poli Anak')
                                <div class="h-16 w-16 bg-emerald-700 rounded-full flex items-center justify-center shadow-lg border border-emerald-600">
                                    <i class="fa-solid fa-child-reaching text-white text-2xl"></i>
                                </div>
                            @else
                                <div class="h-16 w-16 bg-emerald-700 rounded-full flex items-center justify-center shadow-lg border border-emerald-600">
                                    <i class="fa-solid fa-user-doctor text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Satisfaction Survey Form -->
                <div class="space-y-6">
                    <div class="text-center space-y-2 mb-8">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Langkah 3 dari 3</span>
                        <h3 class="text-2xl font-extrabold text-navy-900">Survei Kepuasan Layanan</h3>
                        <p class="text-sm text-slate-400">Kami sangat menghargai feedback Anda untuk terus mengevaluasi dan meningkatkan mutu pelayanan</p>
                    </div>

                    <form action="{{ route('patient.survey', $activeDiagnosisId) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Ratings Container -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <!-- Aspek 1 -->
                            <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                                <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                    <i class="fa-solid fa-hospital-user mr-1.5 text-emerald-700"></i>Kelayakan Fasilitas
                                </label>
                                <p class="text-[11px] text-slate-400 leading-tight">Nilai kebersihan gedung, kecukupan ruang tunggu, kenyamanan tempat tidur perawatan.</p>
                                <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                    <input type="range" name="survey_facilities" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                    <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                                </div>
                            </div>

                            <!-- Aspek 2 -->
                            <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                                <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                    <i class="fa-solid fa-soap mr-1.5 text-emerald-700"></i>Kebersihan Lingkungan
                                </label>
                                <p class="text-[11px] text-slate-400 leading-tight">Nilai sterilitas alat, sanitasi toilet, kebersihan lantai koridor, pembuangan sampah medis.</p>
                                <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                    <input type="range" name="survey_cleanliness" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                    <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                                </div>
                            </div>

                            <!-- Aspek 3 -->
                            <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                                <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                    <i class="fa-solid fa-user-doctor mr-1.5 text-emerald-700"></i>Layanan Dokter
                                </label>
                                <p class="text-[11px] text-slate-400 leading-tight">Nilai keramahan, kejelasan penjelasan medis, kedisiplinan waktu kehadiran dokter.</p>
                                <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                    <input type="range" name="survey_doctor" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                    <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                                </div>
                            </div>

                            <!-- Aspek 4 -->
                            <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl space-y-3">
                                <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                                    <i class="fa-solid fa-clock-rotate-left mr-1.5 text-emerald-700"></i>Kecepatan Penyerahan Obat
                                </label>
                                <p class="text-[11px] text-slate-400 leading-tight">Nilai waktu tunggu resep obat non-racikan dan racikan di apotek depo rumah sakit.</p>
                                <div class="flex items-center justify-between pt-2" x-data="{ score: 5 }">
                                    <input type="range" name="survey_pharmacy" min="1" max="5" x-model="score" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-emerald-700 mr-4">
                                    <span class="h-9 w-9 bg-emerald-700 text-white font-bold rounded-lg flex items-center justify-center text-sm shadow-md" x-text="score">5</span>
                                </div>
                            </div>

                        </div>

                        <div class="pt-6 text-right">
                            <button type="submit" class="w-full sm:w-auto text-sm font-bold text-white bg-gradient-bhayangkara px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                                <span>Kirim Jawaban Survei</span>
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        @elseif($statusSurvei == 'Survei Selesai')
            <!-- Step 4: Overview completion, Verification Status, Survey Charts -->
            <div class="max-w-3xl mx-auto space-y-12">
                <div class="text-center space-y-4">
                    <div class="h-16 w-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto shadow-inner">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-navy-900">Seluruh Alur Pemeriksaan & Survei Selesai</h3>
                    <p class="text-sm text-slate-500 max-w-lg mx-auto">
                        Terima kasih banyak atas partisipasi aktif Anda. Data keluhan, screening, dan hasil survei Anda sudah aman tercatat dalam sistem kami.
                    </p>
                </div>

                <!-- Verification Status Banner -->
                @if($latest->verification_status == 'Terverifikasi')
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-5">
                    <div class="h-14 w-14 bg-emerald-700 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-stamp text-white text-xl"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <h4 class="font-extrabold text-emerald-900 text-sm uppercase tracking-wide mb-1">Diagnosa Terverifikasi Dokter IGD</h4>
                        <p class="text-sm font-bold text-emerald-800 mb-1">{{ $latest->verified_penyakit }}</p>
                        @if($latest->catatan_dokter)
                        <p class="text-xs text-emerald-700/80 leading-relaxed">{{ $latest->catatan_dokter }}</p>
                        @endif
                        <p class="text-[10px] text-emerald-600 mt-1.5 font-semibold">
                            <i class="fa-regular fa-clock mr-1"></i>Diverifikasi {{ $latest->verified_at ? $latest->verified_at->format('d-m-Y H:i') : '' }} &mdash; data telah dikirim untuk rekam medis
                        </p>
                    </div>
                </div>
                @else
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center gap-5">
                    <div class="h-14 w-14 bg-amber-400 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-user-doctor text-white text-xl"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <h4 class="font-extrabold text-amber-900 text-sm uppercase tracking-wide mb-1">Menunggu Verifikasi Dokter IGD</h4>
                        <p class="text-xs text-amber-800/80 leading-relaxed">
                            Hasil screening mandiri Anda akan diverifikasi oleh Dokter IGD setelah pemeriksaan fisik di rumah sakit. Status verifikasi dapat Anda pantau di halaman ini.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Grid Details: Result Badge & Chart -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center border-t border-slate-100 pt-8">
                    <!-- Text parameters -->
                    <div class="md:col-span-5 bg-slate-50 border border-slate-100 p-6 rounded-2xl space-y-4">
                        <h4 class="font-bold text-navy-900 text-sm tracking-wide uppercase">Rincian Hasil</h4>

                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Keluhan Terlaporkan</span>
                            <p class="text-sm font-bold text-slate-800">{{ $latest->diagnosa_singkat }}</p>
                        </div>

                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Hasil Screening Mandiri</span>
                            <span class="inline-block bg-emerald-700 text-white text-xs font-bold px-3 py-1 rounded mt-0.5">
                                {{ $latest->screening_result }}
                            </span>
                        </div>

                        @if($latest->verification_status == 'Terverifikasi')
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Diagnosa Dokter IGD</span>
                            <span class="inline-block bg-navy-900 text-white text-xs font-bold px-3 py-1 rounded mt-0.5">
                                {{ $latest->verified_penyakit }}
                            </span>
                        </div>
                        @endif

                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Tanggal Pemeriksaan</span>
                            <p class="text-xs text-slate-500 font-semibold">{{ $latest->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Interactive chart canvas (diagram kotak) -->
                    <div class="md:col-span-7 space-y-2">
                        <h4 class="font-bold text-navy-900 text-sm tracking-wide uppercase text-center mb-3">Diagram Penilaian Anda</h4>
                        <div class="max-h-[220px] flex justify-center">
                            <canvas id="mySurveyChart" class="max-h-[220px]"></canvas>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-8 text-center">
                    <p class="text-xs text-slate-400 mb-4 font-semibold">Butuh layanan lainnya? Anda dapat memulai layanan baru (kurang sehat / kontrol).</p>
                    <a href="{{ route('patient.dashboard') }}?new_diagnosis=1"
                        class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.02] transition-all inline-flex items-center space-x-2">
                        <i class="fa-solid fa-circle-plus text-xs"></i>
                        <span>Mulai Layanan Baru</span>
                    </a>
                </div>
            </div>

            <!-- Handle logic reset diagnosa baru -->
            @if(request()->has('new_diagnosis'))
                <?php
                    // Force update latest diagnosis survey status in DB so the patient can input again
                    // In a clean MVC it should hit controller, but doing it safely here for direct workflow simulation
                    $latest->update(['status_survei' => 'Selesai & Diarsipkan']);
                    header("Location: " . route('patient.dashboard'));
                    exit;
                ?>
            @endif
        @endif

    </div>

    <!-- Riwayat Diagnosa Table -->
    <div class="bg-white border border-slate-200/60 rounded-3xl shadow-xl p-6 sm:p-10">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
            <div>
                <h3 class="font-extrabold text-navy-900 text-lg">Riwayat Layanan & Kunjungan Anda</h3>
                <p class="text-xs text-slate-400">Seluruh riwayat keluhan, screening, survei, dan verifikasi diagnosa Anda</p>
            </div>
            <div class="h-10 w-10 bg-navy-50 rounded-xl flex items-center justify-center text-navy-900">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" data-mobile-cards="true">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Layanan</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Gejala / Keperluan</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Hasil Screening</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Verifikasi Dokter</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($diagnoses as $diag)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 font-semibold text-slate-500 whitespace-nowrap" data-label="Tanggal">{{ $diag->created_at->format('d M Y, H:i') }}</td>
                        <td class="py-4 whitespace-nowrap" data-label="Layanan">
                            @if($diag->jenis_layanan == 'kontrol')
                                <span class="text-[10px] font-bold text-navy-900 bg-navy-50 border border-navy-100 px-2 py-1 rounded">
                                    <i class="fa-solid fa-calendar-check mr-1"></i>Kontrol
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded">
                                    <i class="fa-solid fa-head-side-cough mr-1"></i>Kurang Sehat
                                </span>
                            @endif
                        </td>
                        <td class="py-4 font-bold text-slate-900" data-label="Gejala / Keperluan" data-card-primary="true">{{ Str::limit($diag->diagnosa_singkat, 30) }}</td>
                        <td class="py-4 whitespace-nowrap" data-label="Hasil Screening">
                            @if(empty($diag->screening_result))
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">{{ $diag->jenis_layanan == 'kontrol' ? 'Tidak Perlu' : 'Belum Screening' }}</span>
                            @else
                                <span class="inline-flex items-center bg-emerald-50 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded border border-emerald-100/50">
                                    {{ $diag->screening_result }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 whitespace-nowrap" data-label="Verifikasi Dokter">
                            @if($diag->verification_status == 'Terverifikasi')
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded">
                                    <i class="fa-solid fa-stamp mr-1"></i>Terverifikasi
                                </span>
                            @elseif($diag->verification_status == 'Menunggu Verifikasi')
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1 rounded">
                                    <i class="fa-regular fa-clock mr-1"></i>Menunggu
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">-</span>
                            @endif
                        </td>
                        <td class="py-4 text-right whitespace-nowrap" data-label="Aksi" data-card-action="true">
                            <button @click="activeModal = {{ $diag->id }}"
                                class="text-xs font-bold text-white bg-navy-800 hover:bg-navy-900 px-3.5 py-2 rounded-xl transition-all shadow-sm flex items-center justify-center space-x-1 ml-auto">
                                <i class="fa-solid fa-eye text-[10px]"></i>
                                <span>Lihat Detail</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Detail Diagnosis Modal View (Tailwind + Alpine Overlay) -->
                    <div x-show="activeModal === {{ $diag->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6 transition-all">

                        <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-lg flex flex-col max-h-[85vh] transform transition-all" @click.away="activeModal = null">

                            <!-- Header Modal -->
                            <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                                <div class="flex items-center space-x-2.5">
                                    <div class="h-9 w-9 rounded-xl bg-navy-50 flex items-center justify-center text-navy-800">
                                        <i class="fa-solid fa-clipboard-list text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-navy-900 text-sm">Detail {{ $diag->jenis_layanan == 'kontrol' ? 'Kunjungan Kontrol' : 'Pemeriksaan Mandiri' }}</h4>
                                        <p class="text-[10px] text-slate-400 font-semibold">{{ $diag->created_at->format('d-m-Y H:i') }}</p>
                                    </div>
                                </div>
                                <button @click="activeModal = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <!-- Content details (scrollable) -->
                            <div class="p-6 sm:p-8 py-4 overflow-y-auto space-y-5 flex-grow text-sm">

                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Nama Pasien</span>
                                    <p class="font-bold text-slate-800">{{ $user->name }}</p>
                                </div>

                                <div class="border-t border-slate-50 pt-3">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">{{ $diag->jenis_layanan == 'kontrol' ? 'Jenis Layanan' : 'Gejala Singkat' }}</span>
                                    <p class="font-semibold text-slate-800 bg-slate-50 border border-slate-100/50 p-3 rounded-xl leading-relaxed">{{ $diag->diagnosa_singkat }}</p>
                                </div>

                                @if($diag->jenis_layanan != 'kontrol')
                                <div class="border-t border-slate-50 pt-3">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Hasil Screening Mandiri</span>
                                    <div>
                                        @if(empty($diag->screening_result))
                                            <span class="text-xs font-semibold text-slate-450 block mt-1">Belum diisi</span>
                                        @else
                                            <span class="inline-block bg-emerald-700 text-white text-[10px] font-bold px-2.5 py-1 rounded">
                                                {{ $diag->screening_result }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Verifikasi Dokter IGD -->
                                @if(!empty($diag->screening_result))
                                <div class="border-t border-slate-50 pt-3">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1.5">Verifikasi Dokter IGD</span>
                                    @if($diag->verification_status == 'Terverifikasi')
                                    <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl space-y-1.5">
                                        <p class="text-xs font-bold text-emerald-800">
                                            <i class="fa-solid fa-stamp mr-1"></i>{{ $diag->verified_penyakit }}
                                        </p>
                                        @if($diag->catatan_dokter)
                                        <p class="text-[11px] text-emerald-700/90 leading-relaxed">{{ $diag->catatan_dokter }}</p>
                                        @endif
                                        <p class="text-[10px] text-emerald-600 font-semibold">Diverifikasi {{ $diag->verified_at ? $diag->verified_at->format('d-m-Y H:i') : '' }}</p>
                                    </div>
                                    @else
                                    <div class="bg-amber-50 border border-amber-100 p-3 rounded-xl">
                                        <p class="text-[11px] font-semibold text-amber-800 leading-relaxed">
                                            <i class="fa-regular fa-clock mr-1"></i>Menunggu verifikasi Dokter IGD setelah pemeriksaan fisik di rumah sakit.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                @endif

                                @if(!empty($diag->screening_answers))
                                 @php
                                     $answers = $diag->screening_answers;
                                     if (is_string($answers)) {
                                         $answers = json_decode($answers, true);
                                     }
                                 @endphp
                                 @if(is_array($answers))
                                 <div class="border-t border-slate-50 pt-3">
                                     <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1.5">Tanya Jawab Screening</span>
                                     <div class="space-y-2 bg-slate-50 border border-slate-100/50 p-3 rounded-xl text-xs font-medium text-slate-700 leading-relaxed">
                                         @foreach($answers as $qId => $ans)
                                             <p><span class="text-[10px] text-slate-400 uppercase font-bold">Pertanyaan #{{ $qId }}:</span> {{ $ans }}</p>
                                         @endforeach
                                     </div>
                                 </div>
                                 @endif
                                 @endif

                                @if(!is_null($diag->survey_facilities))
                                <div class="border-t border-slate-50 pt-3">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-2">Penilaian Kepuasan Layanan</span>
                                    <div class="grid grid-cols-2 gap-3 text-xs font-bold text-navy-950">
                                        <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl flex items-center justify-between">
                                            <span class="font-semibold text-slate-500">Fasilitas</span>
                                            <span class="text-emerald-700 bg-emerald-50 h-6 w-6 rounded flex items-center justify-center">{{ $diag->survey_facilities }}</span>
                                        </div>
                                        <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl flex items-center justify-between">
                                            <span class="font-semibold text-slate-500">Kebersihan</span>
                                            <span class="text-emerald-700 bg-emerald-50 h-6 w-6 rounded flex items-center justify-center">{{ $diag->survey_cleanliness }}</span>
                                        </div>
                                        <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl flex items-center justify-between">
                                            <span class="font-semibold text-slate-500">Layanan Dokter</span>
                                            <span class="text-emerald-700 bg-emerald-50 h-6 w-6 rounded flex items-center justify-center">{{ $diag->survey_doctor }}</span>
                                        </div>
                                        <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xl flex items-center justify-between">
                                            <span class="font-semibold text-slate-500">Apotek Obat</span>
                                            <span class="text-emerald-700 bg-emerald-50 h-6 w-6 rounded flex items-center justify-center">{{ $diag->survey_pharmacy }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>

                            <!-- Footer Modal -->
                            <div class="p-6 sm:p-8 pt-4 border-t border-slate-100 text-right flex-shrink-0">
                                <button @click="activeModal = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                                    Tutup Detail
                                </button>
                            </div>

                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            Belum ada riwayat layanan yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
@if($statusSurvei == 'Survei Selesai' && $latest)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('mySurveyChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Fasilitas', 'Kebersihan', 'Layanan Dokter', 'Apotek Obat'],
                    datasets: [{
                        label: 'Skor Penilaian',
                        data: [
                            {{ $latest->survey_facilities ?? 0 }},
                            {{ $latest->survey_cleanliness ?? 0 }},
                            {{ $latest->survey_doctor ?? 0 }},
                            {{ $latest->survey_pharmacy ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(0, 135, 81, 0.75)',
                            'rgba(11, 37, 69, 0.75)',
                            'rgba(0, 135, 81, 0.55)',
                            'rgba(11, 37, 69, 0.55)'
                        ],
                        borderColor: [
                            'rgba(0, 135, 81, 1)',
                            'rgba(11, 37, 69, 1)',
                            'rgba(0, 135, 81, 1)',
                            'rgba(11, 37, 69, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 10,
                        maxBarThickness: 56
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 5,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endif
@endsection
