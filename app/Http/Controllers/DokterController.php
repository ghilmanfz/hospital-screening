<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Disease;
use App\Models\SystemConfiguration;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DokterController extends Controller
{
    private function checkAuth()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }
        $user = Auth::user();
        if (!$user->isDokter()) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isPasien()) {
                return redirect()->route('patient.dashboard');
            }
            abort(403, 'Akses khusus Dokter IGD.');
        }
        return null;
    }

    /**
     * Dasbor Dokter IGD: antrian verifikasi diagnosa pasca pemeriksaan fisik
     */
    public function index(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        // Statistik ringkas
        $pendingCount = Diagnosis::where('verification_status', 'Menunggu Verifikasi')->count();
        $verifiedCount = Diagnosis::where('verification_status', 'Terverifikasi')->count();
        $totalDiseases = Disease::count();

        // Antrian verifikasi: hanya pasien jalur "kurang sehat" yang sudah punya hasil screening
        $query = Diagnosis::with('user')->whereNotNull('screening_result');

        // Pencarian pasien berdasarkan nama atau nomor WhatsApp
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter status verifikasi
        if ($request->filled('verifikasi')) {
            $query->where('verification_status', $request->verifikasi);
        }

        $diagnoses = $query->orderByRaw("CASE WHEN verification_status = 'Menunggu Verifikasi' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $diseases = Disease::orderBy('nama_penyakit')->get();

        return view('dokter.dashboard', compact('pendingCount', 'verifiedCount', 'totalDiseases', 'diagnoses', 'diseases'));
    }

    /**
     * Verifikasi diagnosa setelah pemeriksaan fisik.
     * Jika hasil screening keliru, dokter cukup tidak memverifikasi (tetap "Menunggu Verifikasi").
     */
    public function verify(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'verified_penyakit' => 'required|string|max:255',
            'catatan_dokter' => 'nullable|string|max:1000',
        ]);

        $diagnosis = Diagnosis::findOrFail($id);

        $diagnosis->update([
            'verification_status' => 'Terverifikasi',
            'verified_penyakit' => $request->verified_penyakit,
            'catatan_dokter' => $request->catatan_dokter,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'activity' => 'Verifikasi diagnosa pasien #' . $diagnosis->id . ' (' . $request->verified_penyakit . ')',
            'module' => 'Verifikasi Dokter IGD',
            'old_value' => $diagnosis->screening_result,
            'new_value' => $request->verified_penyakit,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Diagnosa pasien berhasil diverifikasi dan tercatat untuk rekam medis.');
    }

    /**
     * Kelola pertanyaan screening (fitur yang sama dengan milik Admin)
     */
    public function manageScreening()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $questions = json_decode(SystemConfiguration::getVal('screening_questions', '[]'), true);
        return view('dokter.manage-screening', compact('questions'));
    }

    public function updateScreening(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $questionsInput = $request->input('questions', []);
        $questions = [];

        foreach ($questionsInput as $qIndex => $qData) {
            if (!empty($qData['question'])) {
                $options = [];
                if (isset($qData['options'])) {
                    foreach ($qData['options'] as $opt) {
                        if (!empty($opt['text'])) {
                            $options[] = [
                                'text' => $opt['text'],
                                'weight' => $opt['weight'] ?? 'normal'
                            ];
                        }
                    }
                }

                $questions[] = [
                    'id' => $qIndex + 1,
                    'question' => $qData['question'],
                    'options' => $options
                ];
            }
        }

        SystemConfiguration::setVal('screening_questions', json_encode($questions), Auth::id());

        AuditLog::create([
            'admin_id' => Auth::id(),
            'activity' => 'Dokter IGD memperbarui Pertanyaan Screening',
            'module' => 'Screening',
            'old_value' => 'Screening update',
            'new_value' => json_encode($questions),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Konfigurasi pertanyaan screening berhasil disimpan!');
    }

    /**
     * Kelola master data penyakit
     */
    public function diseaseIndex()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $diseases = Disease::orderBy('nama_penyakit')->get();
        return view('dokter.manage-penyakit', compact('diseases'));
    }

    public function diseaseStore(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'nama_penyakit' => 'required|string|max:255',
            'kode_icd' => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'gejala_umum' => 'nullable|string|max:1000',
            'tindakan' => 'nullable|string|max:1000',
        ]);

        Disease::create($request->only(['nama_penyakit', 'kode_icd', 'kategori', 'gejala_umum', 'tindakan']));

        return back()->with('success', 'Data penyakit baru berhasil ditambahkan.');
    }

    public function diseaseUpdate(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'nama_penyakit' => 'required|string|max:255',
            'kode_icd' => 'nullable|string|max:20',
            'kategori' => 'nullable|string|max:100',
            'gejala_umum' => 'nullable|string|max:1000',
            'tindakan' => 'nullable|string|max:1000',
        ]);

        $disease = Disease::findOrFail($id);
        $disease->update($request->only(['nama_penyakit', 'kode_icd', 'kategori', 'gejala_umum', 'tindakan']));

        return back()->with('success', 'Data penyakit berhasil diperbarui.');
    }

    public function diseaseDestroy($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        Disease::findOrFail($id)->delete();

        return back()->with('success', 'Data penyakit berhasil dihapus.');
    }
}
