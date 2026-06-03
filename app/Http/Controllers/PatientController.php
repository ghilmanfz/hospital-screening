<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\SystemConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    private function checkAuth()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }
        $user = Auth::user();
        if ($user->isPendingVerification()) {
            return redirect()->route('auth.otp');
        }
        if (!$user->isPasien()) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            abort(403, 'Akses ditolak.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $user = Auth::user();
        
        // Fetch patient diagnosis history
        $diagnoses = Diagnosis::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get latest diagnosis to determine current active step/status
        $latest = $diagnoses->first();
        
        $statusSurvei = 'Belum Mengisi Diagnosa';
        $activeDiagnosisId = null;

        if ($latest) {
            $statusSurvei = $latest->status_survei;
            $activeDiagnosisId = $latest->id;
        }

        // Load configured screening questions
        $questions = json_decode(SystemConfiguration::getVal('screening_questions', '[]'), true);

        return view('patient.dashboard', compact('user', 'diagnoses', 'latest', 'statusSurvei', 'activeDiagnosisId', 'questions'));
    }

    public function inputDiagnosa(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'diagnosa_singkat' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Create new diagnosis record
        Diagnosis::create([
            'user_id' => $user->id,
            'diagnosa_singkat' => $request->diagnosa_singkat,
            'status_survei' => 'Belum Mengisi Screening',
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Diagnosa singkat berhasil disimpan! Silakan lanjutkan ke tahap Screening.');
    }

    public function submitScreening(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $diagnosis = Diagnosis::findOrFail($id);
        
        // Ensure this belongs to the logged in patient
        if ($diagnosis->user_id !== Auth::id()) {
            abort(403);
        }

        $answers = $request->except('_token');
        
        // Compute recommendation based on weights in answers
        $questions = json_decode(SystemConfiguration::getVal('screening_questions', '[]'), true);
        
        $hasSevere = false;
        $isPediatric = false;
        $hasFever = false;
        
        $formattedAnswers = [];

        foreach ($questions as $q) {
            $qId = $q['id'];
            $ansText = $answers['q_' . $qId] ?? 'Tidak menjawab';
            
            // Find option metadata
            foreach ($q['options'] as $opt) {
                if ($opt['text'] === $ansText) {
                    $weight = $opt['weight'] ?? 'normal';
                    if ($weight === 'severe') {
                        $hasSevere = true;
                    }
                    if ($weight === 'pediatric') {
                        $isPediatric = true;
                    }
                    if ($weight === 'severe_temp' || $weight === 'mild_temp') {
                        $hasFever = true;
                    }
                }
            }

            $formattedAnswers[$qId] = $ansText;
        }

        // Core business logic rules:
        if ($hasSevere) {
            $result = 'Disarankan ke IGD';
            $profit = 1250000;
        } elseif ($isPediatric) {
            $result = 'Disarankan ke Poli Anak';
            $profit = 245000;
        } elseif ($hasFever) {
            $result = 'Disarankan ke Poli Umum';
            $profit = 150000;
        } else {
            $result = 'Disarankan ke Poli Umum';
            $profit = 120000;
        }

        $diagnosis->update([
            'screening_answers' => $formattedAnswers,
            'screening_result' => $result,
            'profit_amount' => $profit,
            'status_survei' => 'Belum Mengisi Survei',
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Screening selesai! Hasil arahan pelayanan Anda telah keluar.');
    }

    public function submitSurvey(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'survey_facilities' => 'required|integer|between:1,5',
            'survey_cleanliness' => 'required|integer|between:1,5',
            'survey_doctor' => 'required|integer|between:1,5',
            'survey_pharmacy' => 'required|integer|between:1,5',
        ]);

        $diagnosis = Diagnosis::findOrFail($id);

        if ($diagnosis->user_id !== Auth::id()) {
            abort(403);
        }

        $diagnosis->update([
            'survey_facilities' => $request->survey_facilities,
            'survey_cleanliness' => $request->survey_cleanliness,
            'survey_doctor' => $request->survey_doctor,
            'survey_pharmacy' => $request->survey_pharmacy,
            'status_survei' => 'Survei Selesai',
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Terima kasih atas penilaian Anda! Survei berhasil disimpan.');
    }

    public function showProfile()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $user = Auth::user();
        return view('patient.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
