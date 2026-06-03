<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Diagnosis;
use App\Models\SystemConfiguration;
use App\Models\WhatsappMessageLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function checkAuth()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Admin.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        // Overview statistics
        $totalPatients = User::where('role', 'pasien')->count();
        $totalDiagnoses = Diagnosis::count();
        $completedSurveysCount = Diagnosis::where('status_survei', 'Survei Selesai')->count();
        $totalProfit = Diagnosis::sum('profit_amount');

        // Averages for Satisfaction Chart
        $avgFacilities = Diagnosis::whereNotNull('survey_facilities')->avg('survey_facilities') ?? 0;
        $avgCleanliness = Diagnosis::whereNotNull('survey_cleanliness')->avg('survey_cleanliness') ?? 0;
        $avgDoctor = Diagnosis::whereNotNull('survey_doctor')->avg('survey_doctor') ?? 0;
        $avgPharmacy = Diagnosis::whereNotNull('survey_pharmacy')->avg('survey_pharmacy') ?? 0;

        return view('admin.dashboard', compact(
            'totalPatients',
            'totalDiagnoses',
            'completedSurveysCount',
            'totalProfit',
            'avgFacilities',
            'avgCleanliness',
            'avgDoctor',
            'avgPharmacy'
        ));
    }

    public function manageLanding()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $hospitalName = SystemConfiguration::getVal('hospital_name', 'Mayapada Hospital');
        $hospitalLogo = SystemConfiguration::getVal('hospital_logo', '');
        $heroTitle = SystemConfiguration::getVal('hospital_hero_title', 'Empowering Your Health');
        $heroSubtitle = SystemConfiguration::getVal('hospital_hero_subtitle', 'Portal Layanan Rumah Sakit');
        $hospitalImage = SystemConfiguration::getVal('hospital_image', '');

        $doctorSchedules = json_decode(SystemConfiguration::getVal('doctor_schedules', '[]'), true);
        $hospitalServices = json_decode(SystemConfiguration::getVal('hospital_services', '[]'), true);

        return view('admin.manage-landing', compact(
            'hospitalName', 'hospitalLogo', 'heroTitle', 'heroSubtitle', 'hospitalImage', 'doctorSchedules', 'hospitalServices'
        ));
    }

    public function updateLanding(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $adminId = Auth::id();
        $oldHeroTitle = SystemConfiguration::getVal('hospital_hero_title');

        SystemConfiguration::setVal('hospital_name', $request->hospital_name, $adminId);
        
        // Handle Logo URL or Logo File Upload
        $logoUrl = $request->hospital_logo;
        if ($request->hasFile('hospital_logo_file')) {
            $file = $request->file('hospital_logo_file');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $logoUrl = '/uploads/' . $filename;
        }
        SystemConfiguration::setVal('hospital_logo', $logoUrl, $adminId);

        SystemConfiguration::setVal('hospital_hero_title', $request->hero_title, $adminId);
        SystemConfiguration::setVal('hospital_hero_subtitle', $request->hero_subtitle, $adminId);

        // Handle Banner URL or Banner File Upload
        $imageUrl = $request->hospital_image;
        if ($request->hasFile('hospital_image_file')) {
            $file = $request->file('hospital_image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $imageUrl = '/uploads/' . $filename;
        }
        SystemConfiguration::setVal('hospital_image', $imageUrl, $adminId);

        AuditLog::create([
            'admin_id' => $adminId,
            'activity' => 'Memperbarui teks & gambar Landing Page',
            'module' => 'Landing Page',
            'old_value' => $oldHeroTitle,
            'new_value' => $request->hero_title,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Landing Page berhasil diperbarui!');
    }

    public function updateSchedules(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $adminId = Auth::id();
        $schedules = [];

        if ($request->has('doctor')) {
            foreach ($request->doctor as $index => $doc) {
                if (!empty($doc['nama'])) {
                    $fotoUrl = $doc['foto'] ?? '';

                    // Check if file is uploaded for this doctor index
                    if ($request->hasFile("doctor.$index.foto_file")) {
                        $file = $request->file("doctor.$index.foto_file");
                        $filename = 'doctor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads'), $filename);
                        $fotoUrl = '/uploads/' . $filename;
                    }

                    $schedules[] = [
                        'nama' => $doc['nama'],
                        'spesialis' => $doc['spesialis'] ?? '',
                        'jadwal' => $doc['jadwal'] ?? '',
                        'lokasi' => $doc['lokasi'] ?? '',
                        'foto' => $fotoUrl
                    ];
                }
            }
        }

        SystemConfiguration::setVal('doctor_schedules', json_encode($schedules), $adminId);

        AuditLog::create([
            'admin_id' => $adminId,
            'activity' => 'Memperbarui Jadwal Praktik Dokter',
            'module' => 'Jadwal Dokter',
            'old_value' => 'Schedules update',
            'new_value' => json_encode($schedules),
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Jadwal Dokter berhasil disimpan!');
    }

    public function updateServices(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $adminId = Auth::id();
        $services = [];

        if ($request->has('service')) {
            foreach ($request->service as $index => $srv) {
                if (!empty($srv['title'])) {
                    $services[] = [
                        'title' => $srv['title'],
                        'desc' => $srv['desc'] ?? '',
                        'icon' => $srv['icon'] ?? 'beaker'
                    ];
                }
            }
        }

        SystemConfiguration::setVal('hospital_services', json_encode($services), $adminId);

        AuditLog::create([
            'admin_id' => $adminId,
            'activity' => 'Memperbarui Informasi Layanan Rumah Sakit',
            'module' => 'Layanan',
            'old_value' => 'Services update',
            'new_value' => json_encode($services),
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Informasi Layanan berhasil disimpan!');
    }

    public function manageScreening()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $questions = json_decode(SystemConfiguration::getVal('screening_questions', '[]'), true);
        return view('admin.manage-screening', compact('questions'));
    }

    public function updateScreening(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $adminId = Auth::id();
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

        SystemConfiguration::setVal('screening_questions', json_encode($questions), $adminId);

        AuditLog::create([
            'admin_id' => $adminId,
            'activity' => 'Memperbarui Pertanyaan dan Alur Screening',
            'module' => 'Screening',
            'old_value' => 'Screening update',
            'new_value' => json_encode($questions),
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Konfigurasi pertanyaan screening berhasil disimpan!');
    }

    public function diagnosisIndex(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $query = Diagnosis::with('user');

        // Search by patient name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by screening result
        if ($request->filled('result')) {
            $query->where('screening_result', $request->result);
        }

        // Filter by survey status
        if ($request->filled('status')) {
            $query->where('status_survei', $request->status);
        }

        $diagnoses = $query->orderBy('created_at', 'desc')->get();

        return view('admin.diagnosis-index', compact('diagnoses'));
    }

    public function diagnosisDetail($id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $diagnosis = Diagnosis::with('user')->findOrFail($id);
        return response()->json($diagnosis);
    }

    public function profitReport()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $diagnoses = Diagnosis::with('user')->orderBy('created_at', 'desc')->get();

        // Calculate summary categories
        $profitIgd = Diagnosis::where('screening_result', 'Disarankan ke IGD')->sum('profit_amount');
        $profitPoliUmum = Diagnosis::where('screening_result', 'Disarankan ke Poli Umum')->sum('profit_amount');
        $profitPoliAnak = Diagnosis::where('screening_result', 'Disarankan ke Poli Anak')->sum('profit_amount');

        return view('admin.profit-report', compact('diagnoses', 'profitIgd', 'profitPoliUmum', 'profitPoliAnak'));
    }

    public function systemSettings()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $configs = [
            'whatsapp_gateway_status' => SystemConfiguration::getVal('whatsapp_gateway_status', 'Aktif'),
            'fonnte_token' => SystemConfiguration::getVal('fonnte_token', ''),
            'otp_status' => SystemConfiguration::getVal('otp_status', 'Aktif'),
            'otp_length' => SystemConfiguration::getVal('otp_length', '6'),
            'otp_expired_minutes' => SystemConfiguration::getVal('otp_expired_minutes', '5'),
            'otp_max_attempt' => SystemConfiguration::getVal('otp_max_attempt', '3'),
            'otp_resend_cooldown' => SystemConfiguration::getVal('otp_resend_cooldown', '60'),
            'default_country_code' => SystemConfiguration::getVal('default_country_code', '62'),
            'otp_message_template' => SystemConfiguration::getVal('otp_message_template', ''),
        ];

        // Format Fonnte Token for security
        $rawToken = $configs['fonnte_token'];
        if (strlen($rawToken) > 8) {
            $configs['fonnte_token_masked'] = substr($rawToken, 0, 5) . str_repeat('*', strlen($rawToken) - 9) . substr($rawToken, -4);
        } else {
            $configs['fonnte_token_masked'] = $rawToken;
        }

        $logs = WhatsappMessageLog::with('user')->orderBy('created_at', 'desc')->take(30)->get();
        $auditLogs = AuditLog::with('admin')->orderBy('created_at', 'desc')->take(30)->get();

        return view('admin.settings', compact('configs', 'logs', 'auditLogs'));
    }

    public function updateSettings(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $adminId = Auth::id();
        $oldGateway = SystemConfiguration::getVal('whatsapp_gateway_status');

        SystemConfiguration::setVal('whatsapp_gateway_status', $request->whatsapp_gateway_status, $adminId);
        
        // Only update token if filled (not masked version)
        if ($request->filled('fonnte_token') && !str_contains($request->fonnte_token, '*')) {
            SystemConfiguration::setVal('fonnte_token', $request->fonnte_token, $adminId);
        }

        SystemConfiguration::setVal('otp_status', $request->otp_status, $adminId);
        SystemConfiguration::setVal('otp_length', $request->otp_length, $adminId);
        SystemConfiguration::setVal('otp_expired_minutes', $request->otp_expired_minutes, $adminId);
        SystemConfiguration::setVal('otp_max_attempt', $request->otp_max_attempt, $adminId);
        SystemConfiguration::setVal('otp_resend_cooldown', $request->otp_resend_cooldown, $adminId);
        SystemConfiguration::setVal('default_country_code', $request->default_country_code, $adminId);
        SystemConfiguration::setVal('otp_message_template', $request->otp_message_template, $adminId);

        AuditLog::create([
            'admin_id' => $adminId,
            'activity' => 'Memperbarui Konfigurasi Sistem (Fonnte & OTP)',
            'module' => 'Konfigurasi Sistem',
            'old_value' => 'Gateway Status: ' . $oldGateway,
            'new_value' => 'Gateway Status: ' . $request->whatsapp_gateway_status,
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Konfigurasi sistem berhasil disimpan!');
    }

    public function testWhatsApp(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $adminId = Auth::id();
        $fonnteToken = SystemConfiguration::getVal('fonnte_token', '');
        $gatewayStatus = SystemConfiguration::getVal('whatsapp_gateway_status', 'Aktif');

        $status = 'Terkirim';
        $response = '{"status":true,"message":"Pesan percobaan berhasil dikirim (Simulated)"}';

        if ($gatewayStatus !== 'Aktif') {
            $status = 'Gagal';
            $response = '{"status":false,"message":"Gateway dinonaktifkan di konfigurasi"}';
        } elseif (empty($fonnteToken)) {
            $status = 'Gagal';
            $response = '{"status":false,"message":"Token Fonnte belum diisi"}';
        }

        WhatsappMessageLog::create([
            'user_id' => null,
            'phone_number' => $request->phone,
            'message_type' => 'test_send',
            'message_content' => $request->message,
            'provider' => 'fonnte',
            'provider_response' => $response,
            'status' => $status,
            'sent_at' => ($status === 'Terkirim') ? now() : null,
        ]);

        if ($status === 'Terkirim') {
            return back()->with('success', 'WhatsApp Percobaan Terkirim! Silakan periksa tabel Log Pengiriman.');
        } else {
            return back()->withErrors(['test_whatsapp' => 'WhatsApp Gagal Dikirim. Response: ' . json_decode($response)->message]);
        }
    }
}
