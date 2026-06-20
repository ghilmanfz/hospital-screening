@extends('layouts.admin')

@section('admin-title', 'Kelola Akun')

@section('admin-content')
@php
    $roleMeta = [
        'admin'  => ['label' => 'Admin', 'class' => 'bg-navy-50 text-navy-900 border-navy-100', 'icon' => 'fa-user-shield'],
        'dokter' => ['label' => 'Dokter IGD', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'icon' => 'fa-user-doctor'],
        'pasien' => ['label' => 'Pasien', 'class' => 'bg-slate-50 text-slate-600 border-slate-200', 'icon' => 'fa-hospital-user'],
    ];
@endphp

<div x-data="{ showAdd: false, editId: null, resetId: null, deleteId: null }">

    <!-- Role count cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Akun Admin</span>
                <p class="text-3xl font-extrabold text-navy-950">{{ $counts['admin'] }}</p>
            </div>
            <div class="h-12 w-12 bg-navy-50 text-navy-900 rounded-2xl flex items-center justify-center text-lg">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
        <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Akun Dokter IGD</span>
                <p class="text-3xl font-extrabold text-emerald-700">{{ $counts['dokter'] }}</p>
            </div>
            <div class="h-12 w-12 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center text-lg">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
        </div>
        <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Akun Pasien</span>
                <p class="text-3xl font-extrabold text-navy-950">{{ $counts['pasien'] }}</p>
            </div>
            <div class="h-12 w-12 bg-slate-50 text-slate-500 rounded-2xl flex items-center justify-center text-lg">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
            <div>
                <h3 class="font-extrabold text-navy-900 text-lg">Manajemen Akun Sistem</h3>
                <p class="text-xs text-slate-400">Buat, ubah, reset password, dan kelola akun seluruh role pengguna SISMED</p>
            </div>
            <button @click="showAdd = true"
                class="text-xs font-bold text-white bg-gradient-bhayangkara px-5 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all flex items-center space-x-2 flex-shrink-0">
                <i class="fa-solid fa-user-plus text-[10px]"></i>
                <span>Tambah Akun</span>
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

        <!-- Filter Role (pencarian teks memakai kotak cari bawaan tabel) -->
        <form action="{{ route('admin.accounts') }}" method="GET" class="bg-slate-50 border border-slate-100 p-4 rounded-2xl mb-8 flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
            <div class="sm:w-52">
                <label for="role" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Filter Role</label>
                <select name="role" id="role"
                    class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-semibold">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="dokter" {{ request('role') == 'dokter' ? 'selected' : '' }}>Dokter IGD</option>
                    <option value="pasien" {{ request('role') == 'pasien' ? 'selected' : '' }}>Pasien</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.accounts') }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-700 px-3 py-2">Reset</a>
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
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Nama Akun</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Kontak</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Role</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider">Status</th>
                        <th class="py-4 font-bold text-xs uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($accounts as $acc)
                    @php $isSelf = $acc->id === auth()->id(); @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 whitespace-nowrap" data-label="Nama Akun" data-card-primary="true">
                            <span class="font-bold text-navy-900 block">
                                {{ $acc->name }}
                                @if($isSelf)
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded ml-1 align-middle">AKUN ANDA</span>
                                @endif
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">Terdaftar {{ $acc->created_at->format('d-m-Y') }}</span>
                        </td>
                        <td class="py-4 text-xs" data-label="Kontak">
                            <span class="font-semibold text-slate-700 block">+{{ $acc->phone_number }}</span>
                            <span class="text-[10px] text-slate-400">{{ $acc->email ?? '— tanpa email —' }}</span>
                        </td>
                        <td class="py-4 whitespace-nowrap" data-label="Role">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded border {{ $roleMeta[$acc->role]['class'] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                <i class="fa-solid {{ $roleMeta[$acc->role]['icon'] ?? 'fa-user' }} mr-1"></i>{{ $roleMeta[$acc->role]['label'] ?? ucfirst($acc->role) }}
                            </span>
                        </td>
                        <td class="py-4 whitespace-nowrap" data-label="Status">
                            @if($acc->status === 'active')
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded"><i class="fa-solid fa-circle-check mr-1"></i>Aktif</span>
                            @elseif($acc->status === 'blocked')
                                <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-2 py-1 rounded"><i class="fa-solid fa-ban mr-1"></i>Diblokir</span>
                            @else
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1 rounded"><i class="fa-regular fa-clock mr-1"></i>Belum Verifikasi</span>
                            @endif
                        </td>
                        <td class="py-4 text-right whitespace-nowrap space-x-1.5" data-label="Aksi" data-card-action="true">
                            <button @click="editId = {{ $acc->id }}"
                                class="text-xs font-bold text-white bg-navy-800 hover:bg-navy-900 px-3 py-2 rounded-xl transition-all shadow-sm inline-flex items-center space-x-1">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                                <span>Ubah</span>
                            </button>
                            <button @click="resetId = {{ $acc->id }}"
                                class="text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-100 px-3 py-2 rounded-xl transition-all inline-flex items-center space-x-1">
                                <i class="fa-solid fa-key text-[10px]"></i>
                                <span>Reset PW</span>
                            </button>
                            @unless($isSelf)
                            <button @click="deleteId = {{ $acc->id }}"
                                class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-100 px-3 py-2 rounded-xl transition-all inline-flex items-center space-x-1">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                <span>Hapus</span>
                            </button>
                            @endunless
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div x-show="editId === {{ $acc->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
                        <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-lg flex flex-col max-h-[88vh]" @click.away="editId = null">
                            <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                                <div class="flex items-center space-x-2.5">
                                    <div class="h-9 w-9 rounded-xl bg-navy-50 flex items-center justify-center text-navy-800"><i class="fa-solid fa-pen text-sm"></i></div>
                                    <h4 class="font-extrabold text-navy-900 text-sm">Ubah Akun</h4>
                                </div>
                                <button @click="editId = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                            </div>
                            <form action="{{ route('admin.accounts.update', $acc->id) }}" method="POST" class="p-6 sm:p-8 py-5 overflow-y-auto space-y-4 flex-grow">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                                        <input type="text" name="name" required value="{{ $acc->name }}"
                                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">No. WhatsApp</label>
                                        <input type="text" name="phone_number" required value="{{ $acc->phone_number }}"
                                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                                        <input type="email" name="email" value="{{ $acc->email }}"
                                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Role</label>
                                        <select name="role" {{ $isSelf ? 'disabled' : '' }}
                                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 {{ $isSelf ? 'opacity-60 cursor-not-allowed' : '' }}">
                                            <option value="admin" {{ $acc->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="dokter" {{ $acc->role == 'dokter' ? 'selected' : '' }}>Dokter IGD</option>
                                            <option value="pasien" {{ $acc->role == 'pasien' ? 'selected' : '' }}>Pasien</option>
                                        </select>
                                        @if($isSelf)<input type="hidden" name="role" value="admin">@endif
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Status</label>
                                        <select name="status" {{ $isSelf ? 'disabled' : '' }}
                                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 {{ $isSelf ? 'opacity-60 cursor-not-allowed' : '' }}">
                                            <option value="active" {{ $acc->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="blocked" {{ $acc->status == 'blocked' ? 'selected' : '' }}>Diblokir</option>
                                            <option value="pending_verification" {{ $acc->status == 'pending_verification' ? 'selected' : '' }}>Belum Verifikasi</option>
                                        </select>
                                        @if($isSelf)<input type="hidden" name="status" value="active">@endif
                                    </div>
                                </div>
                                @if($isSelf)
                                <p class="text-[10px] text-amber-700 font-semibold"><i class="fa-solid fa-circle-info mr-1"></i>Role & status akun Anda sendiri tidak dapat diubah demi keamanan akses.</p>
                                @endif
                                <div class="pt-2 flex justify-end space-x-2">
                                    <button type="button" @click="editId = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">Batal</button>
                                    <button type="submit" class="text-xs font-bold text-white bg-gradient-bhayangkara px-5 py-2.5 rounded-xl hover:shadow-lg transition-all flex items-center space-x-1.5">
                                        <i class="fa-solid fa-floppy-disk text-[10px]"></i><span>Simpan Perubahan</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Reset Password Modal -->
                    <div x-show="resetId === {{ $acc->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
                        <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-md flex flex-col" @click.away="resetId = null">
                            <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                                <div class="flex items-center space-x-2.5">
                                    <div class="h-9 w-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600"><i class="fa-solid fa-key text-sm"></i></div>
                                    <div>
                                        <h4 class="font-extrabold text-navy-900 text-sm">Reset Password</h4>
                                        <p class="text-[10px] text-slate-400 font-semibold">{{ $acc->name }}</p>
                                    </div>
                                </div>
                                <button @click="resetId = null" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                            </div>
                            <form action="{{ route('admin.accounts.reset', $acc->id) }}" method="POST" class="p-6 sm:p-8 py-5 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Password Baru</label>
                                    <input type="text" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                                        class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 placeholder-slate-400">
                                    <span class="text-[10px] text-slate-400 block mt-1">Sampaikan password baru ini ke pemilik akun secara aman.</span>
                                </div>
                                <div class="pt-1 flex justify-end space-x-2">
                                    <button type="button" @click="resetId = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">Batal</button>
                                    <button type="submit" class="text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 px-5 py-2.5 rounded-xl transition-all flex items-center space-x-1.5">
                                        <i class="fa-solid fa-key text-[10px]"></i><span>Reset Password</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Confirm Modal -->
                    @unless($isSelf)
                    <div x-show="deleteId === {{ $acc->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
                        <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-sm p-8 text-center space-y-4" @click.away="deleteId = null">
                            <div class="h-14 w-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto"><i class="fa-solid fa-trash-can text-xl"></i></div>
                            <h4 class="font-extrabold text-navy-900 text-base">Hapus Akun Ini?</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Akun <strong>{{ $acc->name }}</strong> ({{ $roleMeta[$acc->role]['label'] ?? $acc->role }}) akan dihapus permanen.
                                @if($acc->role === 'pasien')
                                    <span class="text-red-600 font-semibold block mt-1">Perhatian: seluruh riwayat layanan & diagnosa pasien ini juga akan ikut terhapus.</span>
                                @endif
                            </p>
                            <div class="flex justify-center space-x-2 pt-2">
                                <button type="button" @click="deleteId = null" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">Batal</button>
                                <form action="{{ route('admin.accounts.delete', $acc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-white bg-red-600 hover:bg-red-700 px-5 py-2.5 rounded-xl transition-all flex items-center space-x-1.5">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i><span>Ya, Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endunless

                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">Tidak ada akun yang cocok dengan pencarian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/40 backdrop-blur-sm p-4 sm:p-6">
        <div class="bg-white border border-slate-100 shadow-2xl rounded-3xl w-full max-w-lg flex flex-col max-h-[88vh]" @click.away="showAdd = false">
            <div class="p-6 sm:p-8 pb-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2.5">
                    <div class="h-9 w-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700"><i class="fa-solid fa-user-plus text-sm"></i></div>
                    <h4 class="font-extrabold text-navy-900 text-sm">Tambah Akun Baru</h4>
                </div>
                <button @click="showAdd = false" class="h-8 w-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('admin.accounts.store') }}" method="POST" class="p-6 sm:p-8 py-5 overflow-y-auto space-y-4 flex-grow">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: dr. Andi / Dokter IGD"
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Role</label>
                        <select name="role" required class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                            <option value="dokter" {{ old('role') == 'dokter' ? 'selected' : '' }}>Dokter IGD</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pasien" {{ old('role') == 'pasien' ? 'selected' : '' }}>Pasien</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" required class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800">
                            <option value="active" selected>Aktif</option>
                            <option value="blocked">Diblokir</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">No. WhatsApp</label>
                        <input type="text" name="phone_number" required value="{{ old('phone_number') }}" placeholder="0899... / 62899..."
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="akun@hospital.com"
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 placeholder-slate-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-navy-900 uppercase tracking-wider mb-1.5">Password</label>
                        <input type="text" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                            class="block w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-emerald-700 font-semibold text-slate-800 placeholder-slate-400">
                    </div>
                </div>
                <div class="bg-navy-50 border border-navy-100 rounded-xl p-3 text-[10px] text-navy-900/80 font-medium leading-relaxed">
                    <i class="fa-solid fa-circle-info mr-1"></i>Akun yang dibuat admin langsung berstatus aktif tanpa perlu verifikasi OTP WhatsApp.
                </div>
                <div class="pt-1 flex justify-end space-x-2">
                    <button type="button" @click="showAdd = false" class="text-xs font-bold text-slate-700 border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="text-xs font-bold text-white bg-gradient-bhayangkara px-5 py-2.5 rounded-xl hover:shadow-lg transition-all flex items-center space-x-1.5">
                        <i class="fa-solid fa-user-plus text-[10px]"></i><span>Simpan Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
