@extends('layouts.dokter')

@section('dokter-title', 'Kelola Penyakit')

@section('dokter-content')
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl" x-data="{ showAdd: false, editId: null, deleteId: null }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Master Data Penyakit</h3>
            <p class="text-xs text-slate-400">Daftar penyakit yang digunakan sebagai acuan verifikasi diagnosa setelah pemeriksaan fisik</p>
        </div>
        <button @click="showAdd = true"
            class="text-xs font-bold text-white bg-gradient-bhayangkara px-5 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all flex items-center space-x-2">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Tambah Penyakit</span>
        </button>
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

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm js-datatable" data-datatable="false">
            <thead>
                <tr class="text-slate-400 border-b border-slate-100">
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Nama Penyakit</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Kode ICD</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Kategori</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Gejala Umum</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider text-right nosort">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($diseases as $disease)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-4 font-bold text-navy-900" data-label="Nama Penyakit" data-card-primary="true">{{ $disease->nama_penyakit }}</td>
                    <td class="py-4 whitespace-nowrap" data-label="Kode ICD">
                        @if($disease->kode_icd)
                            <span class="font-mono text-[11px] font-bold text-navy-900 bg-navy-50 border border-navy-100 px-2 py-1 rounded">{{ $disease->kode_icd }}</span>
                        @else
                            <span class="text-slate-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="py-4 whitespace-nowrap" data-label="Kategori">
                        <span class="text-[10px] font-bold px-2 py-1 rounded border
                            {{ $disease->kategori == 'Gawat Darurat' ? 'bg-red-50 text-red-600 border-red-100' : ($disease->kategori == 'Anak' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-600 border-slate-200') }}">
                            {{ $disease->kategori ?? 'Umum' }}
                        </span>
                    </td>
                    <td class="py-4 font-medium text-slate-600 text-xs max-w-[260px]" data-label="Gejala Umum">{{ $disease->gejala_umum ? Str::limit($disease->gejala_umum, 55) : '-' }}</td>
                    <td class="py-4 text-right whitespace-nowrap space-x-1.5" data-label="Aksi" data-card-action="true">
                        <button @click="editId = {{ $disease->id }}"
                            class="text-xs font-bold text-white bg-navy-800 hover:bg-navy-900 px-3 py-2 rounded-xl transition-all shadow-sm inline-flex items-center space-x-1">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                            <span>Ubah</span>
                        </button>
                        <button @click="deleteId = {{ $disease->id }}"
                            class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-100 px-3 py-2 rounded-xl transition-all inline-flex items-center space-x-1">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                            <span>Hapus</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Ubah & Hapus (dipindah keluar tbody agar tidak dihapus DataTables saat redraw) -->
    @foreach($diseases as $disease)
        <!-- Edit Modal -->
        <div x-show="editId === {{ $disease->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
            <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-lg flex flex-col max-h-[85vh]" @click.away="editId = null">
                <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-2.5">
                        <div class="h-9 w-9 rounded-xl bg-navy-50 flex items-center justify-center text-navy-800">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </div>
                        <h4 class="font-extrabold text-navy-900 text-sm">Ubah Data Penyakit</h4>
                    </div>
                    <button @click="editId = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('dokter.penyakit.update', $disease->id) }}" method="POST" class="p-6 sm:p-8 py-5 overflow-y-auto space-y-4 flex-grow">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Nama Penyakit</label>
                            <input type="text" name="nama_penyakit" required value="{{ $disease->nama_penyakit }}"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Kode ICD (Opsional)</label>
                            <input type="text" name="kode_icd" value="{{ $disease->kode_icd }}" placeholder="Contoh: J06.9"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-mono font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Kategori</label>
                            <select name="kategori" class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                                <option value="Umum" {{ $disease->kategori == 'Umum' ? 'selected' : '' }}>Umum</option>
                                <option value="Anak" {{ $disease->kategori == 'Anak' ? 'selected' : '' }}>Anak</option>
                                <option value="Gawat Darurat" {{ $disease->kategori == 'Gawat Darurat' ? 'selected' : '' }}>Gawat Darurat</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Gejala Umum</label>
                            <textarea name="gejala_umum" rows="2"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-medium text-slate-800">{{ $disease->gejala_umum }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Tindakan / Penanganan</label>
                            <textarea name="tindakan" rows="2"
                                class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-medium text-slate-800">{{ $disease->tindakan }}</textarea>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="editId = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="text-xs font-bold text-white bg-gradient-bhayangkara px-5 py-2.5 rounded-xl hover:shadow-lg transition-all flex items-center space-x-1.5">
                            <i class="fa-solid fa-floppy-disk text-[10px]"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirm Modal -->
        <div x-show="deleteId === {{ $disease->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
            <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-sm p-8 text-center space-y-4" @click.away="deleteId = null">
                <div class="h-14 w-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-trash-can text-xl"></i>
                </div>
                <h4 class="font-extrabold text-navy-900 text-base">Hapus Penyakit Ini?</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    <strong>{{ $disease->nama_penyakit }}</strong> akan dihapus dari master data. Riwayat verifikasi yang sudah ada tidak akan terpengaruh.
                </p>
                <div class="flex justify-center space-x-2 pt-2">
                    <button type="button" @click="deleteId = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                        Batal
                    </button>
                    <form action="{{ route('dokter.penyakit.delete', $disease->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-white bg-red-600 hover:bg-red-700 px-5 py-2.5 rounded-xl transition-all flex items-center space-x-1.5">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                            <span>Ya, Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Add Modal -->
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
        <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-lg flex flex-col max-h-[85vh]" @click.away="showAdd = false">
            <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2.5">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </div>
                    <h4 class="font-extrabold text-navy-900 text-sm">Tambah Penyakit Baru</h4>
                </div>
                <button @click="showAdd = false" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('dokter.penyakit.store') }}" method="POST" class="p-6 sm:p-8 py-5 overflow-y-auto space-y-4 flex-grow">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Nama Penyakit</label>
                        <input type="text" name="nama_penyakit" required placeholder="Contoh: Demam Berdarah Dengue (DBD)"
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Kode ICD (Opsional)</label>
                        <input type="text" name="kode_icd" placeholder="Contoh: A90"
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-mono font-semibold text-slate-800 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Kategori</label>
                        <select name="kategori" class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                            <option value="Umum" selected>Umum</option>
                            <option value="Anak">Anak</option>
                            <option value="Gawat Darurat">Gawat Darurat</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Gejala Umum</label>
                        <textarea name="gejala_umum" rows="2" placeholder="Contoh: Demam tinggi mendadak, nyeri otot, muncul bintik merah..."
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-medium text-slate-800 placeholder-slate-400"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Tindakan / Penanganan</label>
                        <textarea name="tindakan" rows="2" placeholder="Contoh: Observasi IGD, pemeriksaan trombosit, rawat inap bila diperlukan..."
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-medium text-slate-800 placeholder-slate-400"></textarea>
                    </div>
                </div>

                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="showAdd = false" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="text-xs font-bold text-white bg-gradient-bhayangkara px-5 py-2.5 rounded-xl hover:shadow-lg transition-all flex items-center space-x-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Simpan Penyakit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
