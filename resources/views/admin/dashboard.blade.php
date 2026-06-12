@extends('layouts.admin')

@section('admin-title', 'Overview')

@section('admin-content')
<!-- Stats Widget Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Stat 1: Total Patients -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pasien</span>
            <p class="text-3xl font-extrabold text-navy-950">{{ $totalPatients }}</p>
        </div>
        <div class="h-12 w-12 bg-navy-50 text-navy-900 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <!-- Stat 2: Total Diagnoses -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Diagnosa</span>
            <p class="text-3xl font-extrabold text-navy-950">{{ $totalDiagnoses }}</p>
        </div>
        <div class="h-12 w-12 bg-navy-50 text-navy-900 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-stethoscope"></i>
        </div>
    </div>

    <!-- Stat 3: Completed Surveys -->
    <div class="bg-white border border-slate-200/60 p-6 rounded-3xl shadow-xl flex items-center justify-between">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Survei Selesai</span>
            <p class="text-3xl font-extrabold text-navy-950">{{ $completedSurveysCount }}</p>
        </div>
        <div class="h-12 w-12 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-square-poll-vertical"></i>
        </div>
    </div>

    <!-- Stat 4: Failed Login Warning -->
    <a href="{{ route('admin.patients') }}" class="bg-white border {{ $failedLoginCount > 0 ? 'border-red-200' : 'border-slate-200/60' }} p-6 rounded-3xl shadow-xl flex items-center justify-between hover:shadow-2xl hover:-translate-y-0.5 transition-all">
        <div class="space-y-2">
            <span class="text-xs font-bold {{ $failedLoginCount > 0 ? 'text-red-400' : 'text-slate-400' }} uppercase tracking-wider block">Login Gagal (7 Hari)</span>
            <p class="text-3xl font-extrabold {{ $failedLoginCount > 0 ? 'text-red-600' : 'text-navy-950' }}">{{ $failedLoginCount }}</p>
            <span class="text-[10px] text-slate-400 font-semibold block">Klik untuk lihat detail warning</span>
        </div>
        <div class="h-12 w-12 {{ $failedLoginCount > 0 ? 'bg-red-50 text-red-500' : 'bg-slate-50 text-slate-400' }} rounded-2xl flex items-center justify-center text-lg">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </a>
</div>

<!-- Charts & Highlights Container -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Indeks Kepuasan Layanan Rumah Sakit</h3>
            <p class="text-xs text-slate-400">Tren rata-rata skor kepuasan pasien dari waktu ke waktu berdasarkan survei yang masuk</p>
        </div>
        <div class="h-10 w-10 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-chart-column"></i>
        </div>
    </div>

    <!-- Filter rentang & granularitas -->
    <form action="{{ route('admin.dashboard') }}" method="GET" class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex flex-col lg:flex-row lg:items-end gap-3">
        <div class="lg:w-44">
            <label for="granularity" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tampilan</label>
            <select name="granularity" id="granularity"
                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-semibold">
                <option value="bulanan" {{ $granularity == 'bulanan' ? 'selected' : '' }}>Per Bulan</option>
                <option value="harian" {{ $granularity == 'harian' ? 'selected' : '' }}>Per Hari</option>
            </select>
        </div>
        <div class="flex-grow">
            <label for="start_date" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
            <input type="date" name="start_date" id="start_date" value="{{ $filterStart }}" max="{{ date('Y-m-d') }}"
                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700">
        </div>
        <div class="flex-grow">
            <label for="end_date" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
            <input type="date" name="end_date" id="end_date" value="{{ $filterEnd }}" max="{{ date('Y-m-d') }}"
                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700">
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.dashboard') }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-700 px-3 py-2">Reset</a>
            <button type="submit" class="text-[11px] font-bold text-white bg-navy-900 hover:bg-navy-950 px-5 py-2 rounded-lg shadow transition-all whitespace-nowrap">
                <i class="fa-solid fa-filter mr-1"></i>Terapkan
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <!-- Text values indicators -->
        <div class="md:col-span-4 space-y-4">
            <div class="bg-navy-50 border border-navy-100 p-4 rounded-2xl">
                <span class="text-[10px] text-navy-900/60 font-bold uppercase tracking-wider block">Rata-rata dalam rentang</span>
                <p class="text-xs text-navy-900 font-semibold mt-0.5">{{ \Carbon\Carbon::parse($filterStart)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($filterEnd)->format('d M Y') }}</p>
                <p class="text-[10px] text-slate-500 font-medium mt-1">{{ $rangeSurveyCount }} survei masuk pada periode ini</p>
            </div>

            <!-- Metric item 1 -->
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Kelayakan Fasilitas</span>
                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($avgFacilities, 1) }} / 5.0</span>
                </div>
                <div class="flex text-amber-400 text-xs">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <!-- Metric item 2 -->
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Kebersihan Lingkungan</span>
                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($avgCleanliness, 1) }} / 5.0</span>
                </div>
                <div class="flex text-amber-400 text-xs">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <!-- Metric item 3 -->
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Pelayanan Dokter</span>
                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($avgDoctor, 1) }} / 5.0</span>
                </div>
                <div class="flex text-amber-400 text-xs">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <!-- Metric item 4 -->
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Apotek / Kecepatan Obat</span>
                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($avgPharmacy, 1) }} / 5.0</span>
                </div>
                <div class="flex text-amber-400 text-xs">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
        </div>

        <!-- Trend Line Chart Canvas -->
        <div class="md:col-span-8">
            @if(count($trendLabels) > 0)
            <div class="h-[320px]">
                <canvas id="adminSatisfactionChart"></canvas>
            </div>
            @else
            <div class="h-[320px] flex flex-col items-center justify-center text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                <i class="fa-regular fa-chart-bar text-slate-300 text-3xl mb-2"></i>
                <p class="text-sm font-semibold text-slate-400">Belum ada data survei pada rentang ini.</p>
                <p class="text-xs text-slate-400">Coba ubah rentang tanggal atau pilih tampilan Per Hari/Per Bulan.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('adminSatisfactionChart');
        if (!ctx) return;

        const labels = @json($trendLabels);

        const mkBar = (label, data, color) => ({
            label: label,
            data: data,
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1,
            borderRadius: 5,
            maxBarThickness: 26
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    mkBar('Rata-rata Keseluruhan', @json($trendOverall), 'rgba(0, 135, 81, 0.95)'),
                    mkBar('Fasilitas', @json($trendFacilities), 'rgba(11, 37, 69, 0.85)'),
                    mkBar('Kebersihan', @json($trendCleanliness), 'rgba(2, 132, 199, 0.8)'),
                    mkBar('Layanan Dokter', @json($trendDoctor), 'rgba(217, 119, 6, 0.85)'),
                    mkBar('Apotek Obat', @json($trendPharmacy), 'rgba(148, 163, 184, 0.9)')
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 5,
                        ticks: { stepSize: 1 },
                        title: { display: true, text: 'Skor Rata-Rata (1-5)', font: { size: 10, weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 45, minRotation: 0, autoSkip: true, maxTicksLimit: 12 }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, boxHeight: 12, font: { size: 10 }, usePointStyle: true }
                    },
                    tooltip: {
                        callbacks: {
                            label: (item) => ' ' + item.dataset.label + ': ' + Number(item.raw).toFixed(2) + ' / 5.00'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
