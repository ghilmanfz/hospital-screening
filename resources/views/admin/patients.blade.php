@extends('layouts.admin')

@section('admin-title', 'Data Pasien')

@section('admin-content')
<!-- Patients Master Table -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl" x-data="{ activePatient: null }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Data Pasien Terdaftar</h3>
            <p class="text-xs text-slate-400">Data lengkap pasien: jenis kelamin, tanggal lahir, dan alamat rumah</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <!-- Pencarian kini memakai kotak cari bawaan tabel (datatable) di atas tabel -->

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm js-datatable" data-datatable="false">
            <thead>
                <tr class="text-slate-400 border-b border-slate-100">
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Nama Pasien</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Tgl Lahir (Usia)</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Alamat Rumah</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Kunjungan</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider text-right nosort">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($patients as $p)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-4 whitespace-nowrap" data-label="Nama Pasien" data-card-primary="true">
                        <span class="font-bold text-navy-900 block">{{ $p->name }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold">+{{ $p->phone_number }}{{ $p->email ? ' · ' . $p->email : '' }}</span>
                    </td>
                    <td class="py-4 whitespace-nowrap" data-label="Jenis Kelamin">
                        @if($p->gender)
                            <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $p->gender == 'Laki-laki' ? 'bg-navy-50 text-navy-900 border-navy-100' : 'bg-pink-50 text-pink-700 border-pink-100' }}">
                                <i class="fa-solid {{ $p->gender == 'Laki-laki' ? 'fa-mars' : 'fa-venus' }} mr-1"></i>{{ $p->gender }}
                            </span>
                        @else
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">Belum diisi</span>
                        @endif
                    </td>
                    <td class="py-4 whitespace-nowrap font-semibold text-slate-600 text-xs" data-label="Tgl Lahir (Usia)">
                        @if($p->birth_date)
                            {{ $p->birth_date->format('d-m-Y') }}
                            <span class="text-[10px] text-emerald-700 font-bold">({{ $p->age() }} th)</span>
                        @else
                            <span class="text-slate-400">Belum diisi</span>
                        @endif
                    </td>
                    <td class="py-4 font-medium text-slate-600 text-xs max-w-[220px]" data-label="Alamat Rumah">
                        {{ $p->address ? Str::limit($p->address, 45) : 'Belum diisi' }}
                    </td>
                    <td class="py-4 whitespace-nowrap" data-label="Kunjungan">
                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded">
                            {{ $p->diagnoses_count }}x layanan
                        </span>
                    </td>
                    <td class="py-4 text-right whitespace-nowrap" data-label="Aksi" data-card-action="true">
                        <button @click="activePatient = {{ $p->id }}"
                            class="text-xs font-bold text-white bg-navy-800 hover:bg-navy-900 px-3.5 py-2 rounded-xl transition-all shadow-sm flex items-center justify-center space-x-1 ml-auto">
                            <i class="fa-solid fa-eye text-[10px]"></i>
                            <span>Detail</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal detail pasien (dipindah keluar tbody agar tidak dihapus DataTables saat redraw) -->
    @foreach($patients as $p)
                <!-- Patient detail modal -->
                <div x-show="activePatient === {{ $p->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6 transition-all">
                    <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-md flex flex-col max-h-[85vh]" @click.away="activePatient = null">
                        <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                            <div class="flex items-center space-x-2.5">
                                <div class="h-9 w-9 rounded-xl bg-navy-50 flex items-center justify-center text-navy-800">
                                    <i class="fa-solid fa-address-card text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-navy-900 text-sm">Profil Lengkap Pasien</h4>
                                    <p class="text-[10px] text-slate-400 font-semibold">Terdaftar {{ $p->created_at->format('d-m-Y') }}</p>
                                </div>
                            </div>
                            <button @click="activePatient = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <div class="p-6 sm:p-8 py-4 overflow-y-auto space-y-4 flex-grow text-sm">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Nama Lengkap</span>
                                <p class="font-bold text-slate-800">{{ $p->name }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 border-t border-slate-50 pt-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Jenis Kelamin</span>
                                    <p class="font-semibold text-slate-700 text-xs">{{ $p->gender ?? 'Belum diisi' }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Tanggal Lahir</span>
                                    <p class="font-semibold text-slate-700 text-xs">
                                        {{ $p->birth_date ? $p->birth_date->format('d-m-Y') . ' (' . $p->age() . ' th)' : 'Belum diisi' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">No. WhatsApp</span>
                                    <p class="font-semibold text-slate-700 text-xs">+{{ $p->phone_number }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Email</span>
                                    <p class="font-semibold text-slate-700 text-xs">{{ $p->email ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="border-t border-slate-50 pt-3">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Alamat Rumah</span>
                                <p class="font-semibold text-slate-700 text-xs bg-slate-50 border border-slate-100/50 p-3 rounded-xl leading-relaxed">{{ $p->address ?? 'Belum diisi' }}</p>
                            </div>
                            <div class="border-t border-slate-50 pt-3 grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Status Akun</span>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $p->status == 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                        {{ $p->status == 'active' ? 'Aktif' : ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Total Layanan</span>
                                    <p class="font-bold text-navy-900 text-xs">{{ $p->diagnoses_count }} kali</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 sm:p-8 pt-4 border-t border-slate-100 text-right flex-shrink-0">
                            <button @click="activePatient = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
    @endforeach
</div>

<!-- Failed Login Monitoring -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Peringatan: Pasien Gagal Login</h3>
            <p class="text-xs text-slate-400">Monitoring percobaan login yang gagal (salah username atau password)</p>
        </div>
        <div class="h-10 w-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm js-datatable" data-datatable="false">
            <thead>
                <tr class="text-slate-400 border-b border-slate-100">
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Waktu</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Username / Input</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Pasien Terkait</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Keterangan</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Alamat IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($loginAttempts as $attempt)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-4 font-semibold text-slate-500 whitespace-nowrap text-xs" data-label="Waktu">{{ $attempt->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-4 font-bold text-navy-900 text-xs" data-label="Username / Input" data-card-primary="true">{{ $attempt->identifier }}</td>
                    <td class="py-4 whitespace-nowrap text-xs" data-label="Pasien Terkait">
                        @if($attempt->user)
                            <span class="font-bold text-slate-700">{{ $attempt->user->name }}</span>
                        @else
                            <span class="text-slate-400 font-semibold italic">Tidak dikenal</span>
                        @endif
                    </td>
                    <td class="py-4 whitespace-nowrap" data-label="Keterangan">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded border bg-red-50 text-red-600 border-red-100">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $attempt->reason ?? 'Login gagal' }}
                        </span>
                    </td>
                    <td class="py-4 font-mono text-[11px] text-slate-500" data-label="Alamat IP">{{ $attempt->ip_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
