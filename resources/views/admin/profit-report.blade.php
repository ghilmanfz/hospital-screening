@extends('layouts.admin')

@section('admin-title', 'Laporan Profit')

@section('admin-content')
<!-- Profit summary grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <!-- Category 1: IGD -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Profit IGD</span>
            <p class="text-2xl font-extrabold text-red-500">Rp {{ number_format($profitIgd, 0, ',', '.') }}</p>
            <span class="text-[10px] text-slate-400 block font-semibold">Tarif Tetap: Rp 1.250.000 / pas</span>
        </div>
        <div class="h-12 w-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-truck-medical animate-pulse"></i>
        </div>
    </div>

    <!-- Category 2: Poli Umum -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Profit Poli Umum</span>
            <p class="text-2xl font-extrabold text-navy-950">Rp {{ number_format($profitPoliUmum, 0, ',', '.') }}</p>
            <span class="text-[10px] text-slate-400 block font-semibold">Tarif Tetap: Rp 120k - 150k / pas</span>
        </div>
        <div class="h-12 w-12 bg-navy-50 text-navy-950 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
    </div>

    <!-- Category 3: Poli Anak -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Profit Poli Anak</span>
            <p class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($profitPoliAnak, 0, ',', '.') }}</p>
            <span class="text-[10px] text-slate-400 block font-semibold">Tarif Tetap: Rp 245.000 / pas</span>
        </div>
        <div class="h-12 w-12 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-child-reaching"></i>
        </div>
    </div>
</div>

<!-- Comparison Chart -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Distribusi Profit Pelayanan</h3>
            <p class="text-xs text-slate-400">Analisis perbandingan total profit yang dihasilkan antar kategori screening unit pelayanan</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-chart-bar"></i>
        </div>
    </div>

    <div class="max-h-[250px] flex justify-center">
        <canvas id="profitBarChart" class="max-h-[250px]"></canvas>
    </div>
</div>

<!-- Detailed Profit Logs -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-2">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Log Penerimaan Keuntungan Layanan</h3>
            <p class="text-xs text-slate-400">Rincian profit masuk dari aktivitas konsultasi & screening mandiri pasien</p>
        </div>
        <div class="h-10 w-10 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-list-check"></i>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-slate-400 border-b border-slate-100">
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Nama Pasien</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider">Rekomendasi Layanan</th>
                    <th class="py-4 font-bold text-xs uppercase tracking-wider text-right">Profit Diterima</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($diagnoses as $diag)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="py-4 font-semibold text-slate-550 whitespace-nowrap">{{ $diag->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-4 font-bold text-navy-900 whitespace-nowrap">{{ $diag->user->name }}</td>
                    <td class="py-4 whitespace-nowrap">
                        <span class="inline-flex items-center bg-slate-50 text-slate-800 text-[10px] font-bold px-2.5 py-1 rounded border border-slate-150">
                            {{ $diag->screening_result ?? 'Pemeriksaan Awal' }}
                        </span>
                    </td>
                    <td class="py-4 text-right whitespace-nowrap font-extrabold text-emerald-700">
                        Rp {{ number_format($diag->profit_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-400">
                        Belum ada log penerimaan keuntungan yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('profitBarChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Unit IGD', 'Poli Umum', 'Poli Anak'],
                    datasets: [{
                        label: 'Total Keuntungan (Rupiah)',
                        data: [
                            {{ $profitIgd }},
                            {{ $profitPoliUmum }},
                            {{ $profitPoliAnak }}
                        ],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.75)',  // Red for IGD
                            'rgba(11, 37, 69, 0.85)',   // Navy for Poli Umum
                            'rgba(0, 135, 81, 0.75)'   // Emerald for Poli Anak
                        ],
                        borderColor: [
                            '#ef4444',
                            '#0b2545',
                            '#008751'
                        ],
                        borderWidth: 1.5,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
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
@endsection
