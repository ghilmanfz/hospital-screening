{{-- Shared screening question configurator (digunakan Admin & Dokter IGD). Variabel: $formAction --}}
<div class="bg-white border border-slate-200/60 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6" x-data="{ questions: {{ json_encode($questions) }} }">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h3 class="font-extrabold text-navy-900 text-lg">Konfigurasi Pertanyaan Screening Mandiri</h3>
            <p class="text-xs text-slate-400">Atur daftar pertanyaan beserta bobot pilihan jawaban untuk merekomendasikan unit IGD, Poli Umum, atau Poli Anak</p>
        </div>
        <div class="h-10 w-10 bg-navy-50 text-navy-900 rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-file-waveform"></i>
        </div>
    </div>

    <!-- Alert success -->
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-700 p-4 rounded-xl flex items-center">
        <i class="fa-solid fa-circle-check text-emerald-700 mr-3 text-base"></i>
        <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ $formAction }}" method="POST" class="space-y-8">
        @csrf

        <div class="space-y-6">
            <template x-for="(q, qIndex) in questions" :key="qIndex">
                <div class="bg-slate-50 border border-slate-100 p-6 rounded-2xl relative space-y-4">
                    <!-- Delete button -->
                    <button type="button" @click="questions.splice(qIndex, 1)"
                        class="absolute top-4 right-4 h-7 w-7 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>

                    <div class="space-y-4">
                        <!-- Question Text -->
                        <div class="pr-8">
                            <label class="block text-xs font-bold text-navy-900 uppercase tracking-wider mb-2">
                                Pertanyaan #<span x-text="qIndex+1"></span>
                            </label>
                            <input type="text" :name="'questions['+qIndex+'][question]'" x-model="q.question" required placeholder="Tuliskan pertanyaan screening..."
                                class="block w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-700/20 focus:border-emerald-700 outline-none text-sm font-medium text-slate-800">
                        </div>

                        <!-- Options loop -->
                        <div class="pl-4 border-l-2 border-slate-200 space-y-3">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Opsi Jawaban & Bobot Keputusan</label>

                            <template x-for="(opt, optIndex) in q.options" :key="optIndex">
                                <div class="flex items-center space-x-3">
                                    <input type="text" :name="'questions['+qIndex+'][options]['+optIndex+'][text]'" x-model="opt.text" required placeholder="Teks opsi jawaban..."
                                        class="block w-3/5 px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-medium">

                                    <select :name="'questions['+qIndex+'][options]['+optIndex+'][weight]'" x-model="opt.weight"
                                        class="block w-2/5 px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs outline-none focus:border-emerald-700 font-bold text-slate-700">
                                        <option value="normal">Ringan / Normal (Poli Umum)</option>
                                        <option value="mild">Gejala Sedang (Poli Umum)</option>
                                        <option value="severe">Gejala Berat / Kritis (IGD)</option>
                                        <option value="severe_temp">Demam Tinggi (Poli Umum/IGD)</option>
                                        <option value="mild_temp">Demam Ringan (Poli Umum)</option>
                                        <option value="pediatric">Gejala Anak (Poli Anak)</option>
                                    </select>

                                    <!-- Delete option button -->
                                    <button type="button" @click="q.options.splice(optIndex, 1)"
                                        class="text-red-400 hover:text-red-650 h-6 w-6 flex items-center justify-center">
                                        <i class="fa-solid fa-circle-minus text-sm"></i>
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="q.options.push({text: '', weight: 'normal'})"
                                class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 flex items-center space-x-1 py-1">
                                <i class="fa-solid fa-plus-circle"></i>
                                <span>Tambah Pilihan Jawaban</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Add new question button -->
            <button type="button" @click="questions.push({question: '', options: [{text: '', weight: 'normal'}]})"
                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center justify-center space-x-1 py-2">
                <i class="fa-solid fa-plus-circle text-sm"></i>
                <span>Tambah Pertanyaan Screening Baru</span>
            </button>
        </div>

        <!-- Rules Info Alert -->
        <div class="bg-navy-50 border border-navy-100 rounded-2xl p-4 text-xs text-navy-900 leading-relaxed font-medium">
            <p class="font-bold mb-1.5"><i class="fa-solid fa-circle-info mr-1 text-navy-800"></i>Panduan Logika Keputusan:</p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Jika ada satu atau lebih jawaban bernilai <strong class="text-red-600">Gejala Berat / Kritis (IGD)</strong>, sistem akan secara otomatis mengeluarkan rekomendasi <strong class="text-red-600">Disarankan ke IGD</strong>.</li>
                <li>Jika tidak ada gejala berat, namun ada bobot <strong class="text-emerald-700">Gejala Anak (Poli Anak)</strong>, rekomendasi yang keluar adalah <strong class="text-emerald-700">Disarankan ke Poli Anak</strong>.</li>
                <li>Gejala lainnya (Demam Tinggi/Ringan/Sedang/Normal) diarahkan ke <strong class="text-navy-950">Poli Umum</strong>.</li>
            </ul>
        </div>

        <div class="text-right">
            <button type="submit" class="text-sm font-bold text-white bg-gradient-bhayangkara px-6 py-3 rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all inline-flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Konfigurasi Screening</span>
            </button>
        </div>
    </form>
</div>
