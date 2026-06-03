@extends('layouts.admin')

@section('admin-title', 'Overview')

@section('admin-content')
<!-- Stats Widget Grid -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
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
</div>

<!-- Charts & Highlights Container -->
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Metrik Kepuasan Layanan Rumah Sakit</h3>
            <p class="text-xs text-slate-400">Rata-rata skor kepuasan pasien berdasarkan seluruh survey yang masuk</p>
        </div>
        <div class="h-10 w-10 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
        <!-- Text values indicators -->
        <div class="md:col-span-4 space-y-4">
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

        <!-- Radar Canvas -->
        <div class="md:col-span-8 flex justify-center max-h-[300px]">
            <canvas id="adminRadarChart" class="max-h-[300px]"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('adminRadarChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Fasilitas', 'Kebersihan', 'Layanan Dokter', 'Apotek Obat'],
                    datasets: [{
                        label: 'Rata-Rata Skor Kepuasan',
                        data: [
                            {{ $avgFacilities }},
                            {{ $avgCleanliness }},
                            {{ $avgDoctor }},
                            {{ $avgPharmacy }}
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
                    }
                }
            });
        }
    });
</script>
@endsection
