@extends('layouts.app')

@section('title', 'Dasbor Pasien')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ activeModal: null }">

    <!-- Top Grid: Greeting & Profile Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
        
        <!-- Greeting Card -->
        <div class="lg:col-span-8 bg-gradient-mayapada text-white rounded-3xl p-8 shadow-xl flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <div class="relative z-10 space-y-4">
                <span class="bg-white/10 border border-white/20 text-emerald-300 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full inline-block">
                    Dasbor Pasien Terverifikasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    Selamat datang, <span class="text-emerald-400">{{ $user->name }}</span>!
                </h2>
                <p class="text-sm text-slate-300 leading-relaxed max-w-xl">
                    Portal ini memfasilitasi Anda untuk melaporkan diagnosa awal, melakukan screening kesehatan mandiri secara cepat, dan menilai kualitas pelayanan kami guna perbaikan terus menerus.
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
            </div>
        </div>

        <!-- Progress/Status Step Card -->
        <div class="lg:col-span-4 bg-white border border-slate-200/60 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-navy-900 text-base mb-1">Status Alur Layanan</h3>
                <p class="text-xs text-slate-400 mb-6">Tahapan penanganan pemeriksaan saat ini</p>
                
                <div class="space-y-5">
                    <!-- Step 1 -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Belum Mengisi Diagnosa' ? 'bg-navy-900 text-white border-navy-900 glow-green' : 'bg-emerald-50 text-emerald-700 border-emerald-600' }}">
                            @if($statusSurvei != 'Belum Mengisi Diagnosa')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @else
                                1
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Belum Mengisi Diagnosa' ? 'text-navy-900' : 'text-slate-500' }}">Input Diagnosa Singkat</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Belum Mengisi Screening' ? 'bg-navy-900 text-white border-navy-900 glow-green' : ($statusSurvei == 'Belum Mengisi Diagnosa' ? 'bg-white text-slate-400 border-slate-200' : 'bg-emerald-50 text-emerald-700 border-emerald-600') }}">
                            @if($statusSurvei != 'Belum Mengisi Diagnosa' && $statusSurvei != 'Belum Mengisi Screening')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @else
                                2
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Belum Mengisi Screening' ? 'text-navy-900' : 'text-slate-500' }}">Screening Mandiri</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Belum Mengisi Survei' ? 'bg-navy-900 text-white border-navy-900 glow-green' : (($statusSurvei == 'Belum Mengisi Diagnosa' || $statusSurvei == 'Belum Mengisi Screening') ? 'bg-white text-slate-400 border-slate-200' : 'bg-emerald-50 text-emerald-700 border-emerald-600') }}">
                            @if($statusSurvei == 'Survei Selesai')
                                <i class="fa-solid fa-check text-[10px]"></i>
                            @else
                                3
                            @endif
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Belum Mengisi Survei' ? 'text-navy-900' : 'text-slate-500' }}">Survei Kepuasan</span>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex items-center space-x-3.5">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                            {{ $statusSurvei == 'Survei Selesai' ? 'bg-emerald-700 text-white border-emerald-700 glow-green' : 'bg-white text-slate-400 border-slate-200' }}">
                            4
                        </div>
                        <span class="text-xs font-bold {{ $statusSurvei == 'Survei Selesai' ? 'text-emerald-700' : 'text-slate-500' }}">Alur Selesai</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-100 mt-6">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">Status Terkini:</span>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200/50 px-3 py-1.5 rounded-lg inline-block">
                    {{ $statusSurvei }}
                </span>
            </div>
        </div>

    </div>

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
            <!-- Step 1: Input Diagnosis -->
            <div class="max-w-2xl mx-auto space-y-6">
                <div class="text-center space-y-2 mb-8">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Langkah 1 dari 3</span>
                    <h3 class="text-2xl font-extrabold text-navy-900">Laporkan Keluhan Utama / Diagnosa Awal</h3>
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
                        <button type="submit" class="w-full sm:w-auto text-sm font-bold text-white bg-gradient-mayapada px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                            <span>Simpan & Lanjutkan Screening</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

        @elseif($statusSurvei == 'Belum Mengisi Screening')
            <!-- Step 2: Screening Form Wizard -->
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="text-center space-y-2 mb-8">
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest block">Langkah 2 dari 3</span>
                    <h3 class="text-2xl font-extrabold text-navy-900">Screening Kesehatan Mandiri</h3>
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
                        <button type="submit" class="text-sm font-bold text-white bg-gradient-mayapada px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center space-x-2">
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
                <div class="bg-gradient-mayapada text-white rounded-3xl p-8 relative overflow-hidden border border-white/10">
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
                            <button type="submit" class="w-full sm:w-auto text-sm font-bold text-white bg-gradient-mayapada px-8 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                                <span>Kirim Jawaban Survei</span>
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        @elseif($statusSurvei == 'Survei Selesai')
            <!-- Step 4: Overview completion, Survey Charts, & Option to reset -->
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

                <!-- Grid Details: Result Badge & Chart -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center border-t border-slate-100 pt-8">
                    <!-- Text parameters -->
                    <div class="md:col-span-5 bg-slate-50 border border-slate-100 p-6 rounded-2xl space-y-4">
                        <h4 class="font-bold text-navy-900 text-sm tracking-wide uppercase">Rincian Hasil</h4>
                        
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Diagnosa Terlaporkan</span>
                            <p class="text-sm font-bold text-slate-800">{{ $latest->diagnosa_singkat }}</p>
                        </div>

                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Hasil Screening Mandiri</span>
                            <span class="inline-block bg-emerald-700 text-white text-xs font-bold px-3 py-1 rounded mt-0.5">
                                {{ $latest->screening_result }}
                            </span>
                        </div>

                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Tanggal Pemeriksaan</span>
                            <p class="text-xs text-slate-500 font-semibold">{{ $latest->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Interactive chart canvas -->
                    <div class="md:col-span-7 space-y-2">
                        <h4 class="font-bold text-navy-900 text-sm tracking-wide uppercase text-center mb-3">Grafik Penilaian Anda</h4>
                        <div class="max-h-[220px] flex justify-center">
                            <canvas id="mySurveyChart" class="max-h-[220px]"></canvas>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-8 text-center">
                    <p class="text-xs text-slate-400 mb-4 font-semibold">Mengalami keluhan kesehatan baru? Anda dapat memasukkan diagnosa lainnya.</p>
                    <a href="{{ route('patient.dashboard') }}?new_diagnosis=1" 
                        class="text-sm font-bold text-white bg-gradient-mayapada px-6 py-3.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.02] transition-all inline-flex items-center space-x-2">
                        <i class="fa-solid fa-circle-plus text-xs"></i>
                        <span>Input Diagnosa Baru</span>
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
    <div class="bg-white border border-slate-200/60 rounded-3xl shadow-xl p-8 sm:p-10">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
            <div>
                <h3 class="font-extrabold text-navy-900 text-lg">Riwayat Diagnosa & Kunjungan Anda</h3>
                <p class="text-xs text-slate-400">Seluruh riwayat laporan keluhan kesehatan mandiri Anda</p>
            </div>
            <div class="h-10 w-10 bg-navy-50 rounded-xl flex items-center justify-center text-navy-900">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Gejala Dilaporkan</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Hasil Screening</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Status Survei</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($diagnoses as $diag)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 font-semibold text-slate-500 whitespace-nowrap">{{ $diag->created_at->format('d M Y, H:i') }}</td>
                        <td class="py-4 font-bold text-slate-900">{{ Str::limit($diag->diagnosa_singkat, 30) }}</td>
                        <td class="py-4 whitespace-nowrap">
                            @if(empty($diag->screening_result))
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">Belum Screening</span>
                            @else
                                <span class="inline-flex items-center bg-emerald-50 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded border border-emerald-100/50">
                                    {{ $diag->screening_result }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 whitespace-nowrap">
                            <span class="text-[10px] font-bold px-2 py-1 rounded border
                                {{ $diag->status_survei == 'Survei Selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                {{ $diag->status_survei }}
                            </span>
                        </td>
                        <td class="py-4 text-right whitespace-nowrap">
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
                                        <h4 class="font-extrabold text-navy-900 text-sm">Detail Pemeriksaan Mandiri</h4>
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
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Gejala Singkat</span>
                                    <p class="font-semibold text-slate-800 bg-slate-50 border border-slate-100/50 p-3 rounded-xl leading-relaxed">{{ $diag->diagnosa_singkat }}</p>
                                </div>

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

                                @if($diag->status_survei == 'Survei Selesai')
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
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            Belum ada riwayat keluhan/diagnosa yang terdaftar.
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
                type: 'radar',
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
                        backgroundColor: 'rgba(0, 135, 81, 0.2)',
                        borderColor: 'rgba(0, 135, 81, 0.8)',
                        pointBackgroundColor: 'rgba(11, 37, 69, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(11, 37, 69, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
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
