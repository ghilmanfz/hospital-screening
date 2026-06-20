@extends('layouts.admin')

@section('admin-title', 'Indeks Diagnosa')

@section('admin-content')
<!-- Diagnosis Index Table -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl" x-data="{ activeModal: null }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Indeks Diagnosa Pasien</h3>
            <p class="text-xs text-slate-400">Seluruh laporan keluhan, hasil screening, dan skor kepuasan pasien rumah sakit</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-folder-open"></i>
        </div>
    </div>

    <!-- Filter Card -->
    <form action="{{ route('admin.diagnoses.index') }}" method="GET" class="bg-slate-50 border border-slate-100 p-5 rounded-2xl mb-8 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Filter Date -->
            <div>
                <label for="date" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}"
                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700">
            </div>

            <!-- Filter Screening Outcome -->
            <div>
                <label for="result" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hasil Screening</label>
                <select name="result" id="result"
                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700">
                    <option value="">Semua Rekomendasi</option>
                    <option value="Disarankan ke IGD" {{ request('result') == 'Disarankan ke IGD' ? 'selected' : '' }}>Disarankan ke IGD</option>
                    <option value="Disarankan ke Poli Umum" {{ request('result') == 'Disarankan ke Poli Umum' ? 'selected' : '' }}>Disarankan ke Poli Umum</option>
                    <option value="Disarankan ke Poli Anak" {{ request('result') == 'Disarankan ke Poli Anak' ? 'selected' : '' }}>Disarankan ke Poli Anak</option>
                </select>
            </div>

            <!-- Filter Survey Status -->
            <div>
                <label for="status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Status Survei</label>
                <select name="status" id="status"
                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700">
                    <option value="">Semua Status</option>
                    <option value="Survei Selesai" {{ request('status') == 'Survei Selesai' ? 'selected' : '' }}>Survei Selesai</option>
                    <option value="Belum Mengisi Diagnosa" {{ request('status') == 'Belum Mengisi Diagnosa' ? 'selected' : '' }}>Belum Mengisi Diagnosa</option>
                    <option value="Belum Mengisi Screening" {{ request('status') == 'Belum Mengisi Screening' ? 'selected' : '' }}>Belum Mengisi Screening</option>
                    <option value="Belum Mengisi Survei" {{ request('status') == 'Belum Mengisi Survei' ? 'selected' : '' }}>Belum Mengisi Survei</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <span class="text-[10px] text-slate-450 font-bold">Hasil filter: <span class="text-navy-950 font-black">{{ $diagnoses->count() }}</span> data ditemukan.</span>
            
            <div class="flex space-x-2">
                <a href="{{ route('admin.diagnoses.index') }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-700 px-3 py-2">
                    Reset Filter
                </a>
                <button type="submit" class="text-[11px] font-bold text-white bg-navy-900 hover:bg-navy-950 px-5 py-2 rounded-lg shadow transition-all">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Table content -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm js-datatable" data-datatable="false">
            <thead>
                <tr class="text-slate-400 border-b border-slate-100">
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Nama Pasien</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Gejala Singkat</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Hasil Screening</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Verifikasi Dokter</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Status Survei</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider text-right nosort">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($diagnoses as $diag)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-4 font-semibold text-slate-500 whitespace-nowrap" data-label="Tanggal">{{ $diag->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-4 font-bold text-navy-900 whitespace-nowrap" data-label="Nama Pasien" data-card-primary="true">{{ $diag->user->name }}</td>
                    <td class="py-4 font-semibold text-slate-750" data-label="Gejala Singkat">{{ Str::limit($diag->diagnosa_singkat, 30) }}</td>
                    <td class="py-4 whitespace-nowrap" data-label="Hasil Screening">
                        @if(empty($diag->screening_result))
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">Belum Screening</span>
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
                    <td class="py-4 whitespace-nowrap" data-label="Status Survei">
                        <span class="text-[10px] font-bold px-2 py-1 rounded border
                            {{ $diag->status_survei == 'Survei Selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                            {{ $diag->status_survei }}
                        </span>
                    </td>
                    <td class="py-4 text-right whitespace-nowrap" data-label="Aksi" data-card-action="true">
                        <button @click="activeModal = {{ $diag->id }}" 
                            class="text-xs font-bold text-white bg-navy-800 hover:bg-navy-900 px-3.5 py-2 rounded-xl transition-all shadow-sm flex items-center justify-center space-x-1 ml-auto">
                            <i class="fa-solid fa-eye text-[10px]"></i>
                            <span>Lihat Detail</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal detail (dipindah keluar tbody agar tidak dihapus DataTables saat redraw) -->
    @foreach($diagnoses as $diag)
                <!-- Admin detail modal overlay -->
                <div x-show="activeModal === {{ $diag->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6 transition-all">
                    
                    <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-lg flex flex-col max-h-[85vh] transform transition-all" @click.away="activeModal = null">
                        
                        <!-- Header modal -->
                        <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                            <div class="flex items-center space-x-2.5">
                                <div class="h-9 w-9 rounded-xl bg-navy-50 flex items-center justify-center text-navy-800">
                                    <i class="fa-solid fa-clipboard-list text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-navy-900 text-sm">Detail Diagnosa & Evaluasi</h4>
                                    <p class="text-[10px] text-slate-400 font-semibold">{{ $diag->created_at->format('d-m-Y H:i') }}</p>
                                </div>
                            </div>
                            <button @click="activeModal = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <!-- Content details (scrollable) -->
                        <div class="p-6 sm:p-8 py-4 overflow-y-auto space-y-5 flex-grow text-sm">
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Nama Pasien</span>
                                    <p class="font-bold text-slate-800">{{ $diag->user->name }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">No. WhatsApp</span>
                                    <p class="font-semibold text-slate-600">+{{ $diag->user->phone_number }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Jenis Kelamin / Usia</span>
                                    <p class="font-semibold text-slate-600 text-xs">{{ $diag->user->gender ?? '-' }}{{ $diag->user->age() !== null ? ', ' . $diag->user->age() . ' th' : '' }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Tanggal Lahir</span>
                                    <p class="font-semibold text-slate-600 text-xs">{{ $diag->user->birth_date ? $diag->user->birth_date->format('d-m-Y') : '-' }}</p>
                                </div>
                            </div>

                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Alamat Rumah</span>
                                <p class="font-semibold text-slate-600 text-xs leading-relaxed">{{ $diag->user->address ?? 'Belum diisi' }}</p>
                            </div>

                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Gejala Singkat Dilaporkan</span>
                                <p class="font-semibold text-slate-800 bg-slate-50 border border-slate-100/50 p-3 rounded-xl leading-relaxed">{{ $diag->diagnosa_singkat }}</p>
                            </div>

                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Hasil Screening</span>
                                <div>
                                    @if(empty($diag->screening_result))
                                        <span class="text-xs font-semibold text-slate-400">Belum diisi</span>
                                    @else
                                        <span class="inline-block bg-emerald-700 text-white text-[10px] font-bold px-2.5 py-1 rounded">
                                            {{ $diag->screening_result }}
                                        </span>
                                    @endif
                                </div>
                            </div>

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
                                        <i class="fa-regular fa-clock mr-1"></i>Menunggu verifikasi Dokter IGD setelah pemeriksaan fisik.
                                    </p>
                                </div>
                                @endif
                            </div>
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
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1.5">Jawaban Pertanyaan Screening</span>
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

                        <!-- Footer modal -->
                        <div class="p-6 sm:p-8 pt-4 border-t border-slate-100 text-right flex-shrink-0">
                            <button @click="activeModal = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                                Tutup Detail
                            </button>
                        </div>

                    </div>
                </div>
    @endforeach
</div>
@endsection
