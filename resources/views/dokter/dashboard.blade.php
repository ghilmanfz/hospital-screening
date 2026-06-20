@extends('layouts.dokter')

@section('dokter-title', 'Verifikasi Diagnosa')

@section('dokter-content')
<!-- Stats Widget Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <!-- Stat 1: Menunggu Verifikasi -->
    <div class="bg-white border {{ $pendingCount > 0 ? 'border-amber-200' : 'border-slate-200/60' }} p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Menunggu Verifikasi</span>
            <p class="text-3xl font-extrabold {{ $pendingCount > 0 ? 'text-amber-600' : 'text-navy-950' }}">{{ $pendingCount }}</p>
        </div>
        <div class="h-12 w-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-regular fa-clock"></i>
        </div>
    </div>

    <!-- Stat 2: Terverifikasi -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Telah Diverifikasi</span>
            <p class="text-3xl font-extrabold text-navy-950">{{ $verifiedCount }}</p>
        </div>
        <div class="h-12 w-12 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-stamp"></i>
        </div>
    </div>

    <!-- Stat 3: Master Penyakit -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Penyakit Terdaftar</span>
            <p class="text-3xl font-extrabold text-navy-950">{{ $totalDiseases }}</p>
        </div>
        <div class="h-12 w-12 bg-navy-50 text-navy-900 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-disease"></i>
        </div>
    </div>
</div>

<!-- Verification Queue -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl" x-data="{ activeModal: null }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Verifikasi Diagnosa Pasien</h3>
            <p class="text-xs text-slate-400">Verifikasi hasil screening setelah pemeriksaan fisik pasien di IGD. Jika hasil screening tidak sinkron dengan pemeriksaan fisik, diagnosa tidak wajib diverifikasi.</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-stamp"></i>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl mb-6 flex items-center">
        <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6 flex items-center">
        <i class="fa-solid fa-circle-exclamation text-red-500 mr-3"></i>
        <p class="text-xs font-semibold text-red-800">{{ $errors->first() }}</p>
    </div>
    @endif

    <!-- Filter Status (pencarian teks memakai kotak cari bawaan tabel) -->
    <form action="{{ route('dokter.dashboard') }}" method="GET" class="bg-slate-50 border border-slate-100 p-4 rounded-2xl mb-8 flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
        <div class="sm:w-60">
            <label for="verifikasi" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status Verifikasi</label>
            <select name="verifikasi" id="verifikasi"
                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-semibold">
                <option value="">Semua Status</option>
                <option value="Menunggu Verifikasi" {{ request('verifikasi') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="Terverifikasi" {{ request('verifikasi') == 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
            </select>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('dokter.dashboard') }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-700 px-3 py-2">Reset</a>
            <button type="submit" class="text-[11px] font-bold text-white bg-navy-900 hover:bg-navy-950 px-5 py-2 rounded-lg shadow transition-all">
                Terapkan
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" data-mobile-cards="true">
            <thead>
                <tr class="text-slate-400 border-b border-slate-100">
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Pasien</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Keluhan</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Hasil Screening</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Status</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($diagnoses as $diag)
                <tr class="hover:bg-slate-50/50 transition-colors {{ $diag->verification_status == 'Menunggu Verifikasi' ? 'bg-amber-50/30' : '' }}">
                    <td class="py-4 font-semibold text-slate-500 whitespace-nowrap text-xs" data-label="Tanggal">{{ $diag->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-4 whitespace-nowrap" data-label="Pasien" data-card-primary="true">
                        <span class="font-bold text-navy-900 block">{{ $diag->user->name }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold">{{ $diag->user->gender ?? '-' }}{{ $diag->user->age() !== null ? ', ' . $diag->user->age() . ' th' : '' }} &middot; +{{ $diag->user->phone_number }}</span>
                    </td>
                    <td class="py-4 font-semibold text-slate-700 text-xs" data-label="Keluhan">{{ Str::limit($diag->diagnosa_singkat, 28) }}</td>
                    <td class="py-4 whitespace-nowrap" data-label="Hasil Screening">
                        <span class="inline-flex items-center {{ $diag->screening_result == 'Disarankan ke IGD' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-emerald-50 text-emerald-800 border-emerald-100/50' }} text-[10px] font-bold px-2.5 py-1 rounded border">
                            {{ $diag->screening_result }}
                        </span>
                    </td>
                    <td class="py-4 whitespace-nowrap" data-label="Status">
                        @if($diag->verification_status == 'Terverifikasi')
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded">
                                <i class="fa-solid fa-stamp mr-1"></i>Terverifikasi
                            </span>
                        @else
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1 rounded">
                                <i class="fa-regular fa-clock mr-1"></i>Menunggu
                            </span>
                        @endif
                    </td>
                    <td class="py-4 text-right whitespace-nowrap" data-label="Aksi" data-card-action="true">
                        <button @click="activeModal = {{ $diag->id }}"
                            class="text-xs font-bold text-white {{ $diag->verification_status == 'Menunggu Verifikasi' ? 'bg-emerald-700 hover:bg-emerald-800' : 'bg-navy-800 hover:bg-navy-900' }} px-3.5 py-2 rounded-xl transition-all shadow-sm flex items-center justify-center space-x-1 ml-auto">
                            <i class="fa-solid {{ $diag->verification_status == 'Menunggu Verifikasi' ? 'fa-stamp' : 'fa-eye' }} text-[10px]"></i>
                            <span>{{ $diag->verification_status == 'Menunggu Verifikasi' ? 'Periksa & Verifikasi' : 'Lihat Detail' }}</span>
                        </button>
                    </td>
                </tr>

                <!-- Detail & Verify Modal -->
                <div x-show="activeModal === {{ $diag->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6 transition-all">
                    <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-xl flex flex-col max-h-[88vh]" @click.away="activeModal = null">

                        <!-- Header -->
                        <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                            <div class="flex items-center space-x-2.5">
                                <div class="h-9 w-9 rounded-xl bg-navy-50 flex items-center justify-center text-navy-800">
                                    <i class="fa-solid fa-user-doctor text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-navy-900 text-sm">Pemeriksaan & Verifikasi Diagnosa</h4>
                                    <p class="text-[10px] text-slate-400 font-semibold">Screening {{ $diag->created_at->format('d-m-Y H:i') }}</p>
                                </div>
                            </div>
                            <button @click="activeModal = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 sm:p-8 py-4 overflow-y-auto space-y-5 flex-grow text-sm">

                            <!-- Data Pasien -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Nama Pasien</span>
                                    <p class="font-bold text-slate-800">{{ $diag->user->name }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">No. WhatsApp</span>
                                    <p class="font-semibold text-slate-600 text-xs">+{{ $diag->user->phone_number }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Jenis Kelamin / Usia</span>
                                    <p class="font-semibold text-slate-600 text-xs">{{ $diag->user->gender ?? '-' }}{{ $diag->user->age() !== null ? ', ' . $diag->user->age() . ' tahun' : '' }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Alamat</span>
                                    <p class="font-semibold text-slate-600 text-xs">{{ $diag->user->address ? Str::limit($diag->user->address, 50) : '-' }}</p>
                                </div>
                            </div>

                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Keluhan Pasien (Diagnosa Mandiri)</span>
                                <p class="font-semibold text-slate-800 bg-slate-50 border border-slate-100/50 p-3 rounded-xl leading-relaxed">{{ $diag->diagnosa_singkat }}</p>
                            </div>

                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Hasil Screening Sistem</span>
                                <span class="inline-block {{ $diag->screening_result == 'Disarankan ke IGD' ? 'bg-red-600' : 'bg-emerald-700' }} text-white text-[10px] font-bold px-2.5 py-1 rounded">
                                    {{ $diag->screening_result }}
                                </span>
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
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1.5">Jawaban Screening Pasien</span>
                                <div class="space-y-2 bg-slate-50 border border-slate-100/50 p-3 rounded-xl text-xs font-medium text-slate-700 leading-relaxed">
                                    @foreach($answers as $qId => $ans)
                                        <p><span class="text-[10px] text-slate-400 uppercase font-bold">#{{ $qId }}:</span> {{ $ans }}</p>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endif

                            @if($diag->verification_status == 'Terverifikasi')
                            <!-- Sudah diverifikasi -->
                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1.5">Hasil Verifikasi</span>
                                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl space-y-1.5">
                                    <p class="text-sm font-bold text-emerald-800">
                                        <i class="fa-solid fa-stamp mr-1.5"></i>{{ $diag->verified_penyakit }}
                                    </p>
                                    @if($diag->catatan_dokter)
                                    <p class="text-xs text-emerald-700/90 leading-relaxed">{{ $diag->catatan_dokter }}</p>
                                    @endif
                                    <p class="text-[10px] text-emerald-600 font-semibold">
                                        Diverifikasi {{ $diag->verified_at ? $diag->verified_at->format('d-m-Y H:i') : '' }} &mdash; data terkirim untuk rekam medis
                                    </p>
                                </div>
                            </div>
                            @else
                            <!-- Form Verifikasi -->
                            <div class="border-t border-slate-50 pt-4">
                                <span class="text-[10px] text-emerald-700 uppercase font-bold block mb-2"><i class="fa-solid fa-stamp mr-1"></i>Verifikasi Setelah Pemeriksaan Fisik</span>
                                <form action="{{ route('dokter.verify', $diag->id) }}" method="POST" class="space-y-4 bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                                    @csrf
                                    <div>
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Diagnosa Penyakit (Master Penyakit)</label>
                                        <select name="verified_penyakit" required
                                            class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                                            <option value="" disabled selected>Pilih penyakit hasil pemeriksaan fisik...</option>
                                            @foreach($diseases as $disease)
                                                <option value="{{ $disease->nama_penyakit }}">{{ $disease->nama_penyakit }}{{ $disease->kode_icd ? ' (' . $disease->kode_icd . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-[10px] text-slate-400 block mt-1">Belum ada di daftar? Tambahkan dulu lewat menu <strong>Kelola Penyakit</strong>.</span>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Catatan Pemeriksaan Fisik (Opsional)</label>
                                        <textarea name="catatan_dokter" rows="3" placeholder="Contoh: TD 120/80, suhu 38.7°C, hasil screening sinkron dengan kondisi fisik pasien..."
                                            class="block w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-medium text-slate-800 placeholder-slate-400"></textarea>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-[10px] text-amber-700 font-semibold leading-snug flex-1">
                                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>Jika hasil screening tidak sesuai pemeriksaan fisik, tutup jendela ini tanpa verifikasi.
                                        </p>
                                        <button type="submit" class="text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 px-5 py-2.5 rounded-xl transition-all shadow-sm flex items-center space-x-1.5 flex-shrink-0">
                                            <i class="fa-solid fa-stamp text-[10px]"></i>
                                            <span>Verifikasi Diagnosa</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif

                        </div>

                        <!-- Footer -->
                        <div class="p-6 sm:p-8 pt-4 border-t border-slate-100 text-right flex-shrink-0">
                            <button @click="activeModal = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                                Tutup
                            </button>
                        </div>

                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400">
                        Tidak ada data screening pasien yang perlu diverifikasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
