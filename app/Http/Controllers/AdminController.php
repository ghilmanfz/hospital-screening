<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Diagnosis;
use App\Models\LoginAttempt;
use App\Models\SystemConfiguration;
use App\Models\WhatsappMessageLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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

    public function index(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        // Overview statistics
        $totalPatients = User::where('role', 'pasien')->count();
        $totalDiagnoses = Diagnosis::count();
        $completedSurveysCount = Diagnosis::where('status_survei', 'Survei Selesai')->count();
        $totalProfit = Diagnosis::sum('profit_amount');

        // ===== Indeks Kepuasan dari waktu ke waktu (filter rentang & granularitas) =====
        $granularity = in_array($request->input('granularity'), ['harian', 'bulanan']) ? $request->input('granularity') : 'bulanan';

        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::today();
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date);
        } else {
            // Default: 30 hari terakhir (harian) atau 6 bulan terakhir (bulanan)
            $startDate = $granularity === 'harian'
                ? Carbon::today()->subDays(29)
                : Carbon::today()->startOfMonth()->subMonthsNoOverflow(5);
        }
        if ($startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate->copy(), $startDate->copy()];
        }

        $rangeStart = $startDate->copy()->startOfDay();
        $rangeEnd = $endDate->copy()->endOfDay();

        $periodExpr = $granularity === 'harian'
            ? "DATE(created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $trendRows = Diagnosis::whereNotNull('survey_facilities')
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw("$periodExpr as period")
            ->selectRaw('AVG(survey_facilities) as avg_f')
            ->selectRaw('AVG(survey_cleanliness) as avg_c')
            ->selectRaw('AVG(survey_doctor) as avg_d')
            ->selectRaw('AVG(survey_pharmacy) as avg_p')
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw($periodExpr)
            ->orderByRaw("$periodExpr ASC")
            ->get();

        $trendLabels = [];
        $trendFacilities = [];
        $trendCleanliness = [];
        $trendDoctor = [];
        $trendPharmacy = [];
        $trendOverall = [];

        foreach ($trendRows as $row) {
            $trendLabels[] = $granularity === 'harian'
                ? Carbon::parse($row->period)->format('d M y')
                : Carbon::parse($row->period . '-01')->format('M Y');
            $f = round((float) $row->avg_f, 2);
            $c = round((float) $row->avg_c, 2);
            $d = round((float) $row->avg_d, 2);
            $p = round((float) $row->avg_p, 2);
            $trendFacilities[] = $f;
            $trendCleanliness[] = $c;
            $trendDoctor[] = $d;
            $trendPharmacy[] = $p;
            $trendOverall[] = round(($f + $c + $d + $p) / 4, 2);
        }

        // Rata-rata ringkas dalam rentang terpilih (untuk kartu di samping grafik)
        $rangeAgg = Diagnosis::whereNotNull('survey_facilities')
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('AVG(survey_facilities) as f, AVG(survey_cleanliness) as c, AVG(survey_doctor) as d, AVG(survey_pharmacy) as p, COUNT(*) as total')
            ->first();

        $avgFacilities = $rangeAgg->f ?? 0;
        $avgCleanliness = $rangeAgg->c ?? 0;
        $avgDoctor = $rangeAgg->d ?? 0;
        $avgPharmacy = $rangeAgg->p ?? 0;
        $rangeSurveyCount = (int) ($rangeAgg->total ?? 0);

        $filterStart = $startDate->format('Y-m-d');
        $filterEnd = $endDate->format('Y-m-d');

        // Warning login gagal 7 hari terakhir
        $failedLoginCount = LoginAttempt::where('status', 'Gagal')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return view('admin.dashboard', compact(
            'totalPatients',
            'totalDiagnoses',
            'completedSurveysCount',
            'totalProfit',
            'avgFacilities',
            'avgCleanliness',
            'avgDoctor',
            'avgPharmacy',
            'failedLoginCount',
            'granularity',
            'filterStart',
            'filterEnd',
            'rangeSurveyCount',
            'trendLabels',
            'trendFacilities',
            'trendCleanliness',
            'trendDoctor',
            'trendPharmacy',
            'trendOverall'
        ));
    }

    /**
     * Data Pasien lengkap (gender, tanggal lahir, alamat) + monitoring login gagal
     */
    public function patientIndex(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $query = User::where('role', 'pasien')->withCount('diagnoses');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')->get();

        // Log percobaan login gagal (warning salah username/password)
        $loginAttempts = LoginAttempt::with('user')
            ->where('status', 'Gagal')
            ->orderBy('created_at', 'desc')
            ->take(30)
            ->get();

        return view('admin.patients', compact('patients', 'loginAttempts'));
    }

    /**
     * Kelola Akun: manajemen seluruh akun (Admin, Dokter IGD, Pasien)
     */
    public function accountIndex(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderByRaw("FIELD(role, 'admin', 'dokter', 'pasien')")->orderBy('name')->get();

        $counts = [
            'admin' => User::where('role', 'admin')->count(),
            'dokter' => User::where('role', 'dokter')->count(),
            'pasien' => User::where('role', 'pasien')->count(),
        ];

        return view('admin.accounts', compact('accounts', 'counts'));
    }

    /**
     * Normalize nomor telepon: ganti 0 di depan dengan kode negara default
     */
    private function normalizePhone(string $phone): string
    {
        if (str_starts_with($phone, '0')) {
            $countryCode = SystemConfiguration::getVal('default_country_code', '62');
            return $countryCode . substr($phone, 1);
        }
        return $phone;
    }

    public function accountStore(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,dokter,pasien',
            'phone_number' => 'required|string|unique:users,phone_number',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'status' => 'required|in:active,blocked',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $this->normalizePhone($request->phone_number),
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
            // Akun dibuat oleh admin → langsung aktif tanpa perlu verifikasi OTP
            'phone_verified_at' => now(),
        ]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'activity' => 'Membuat akun baru: ' . $user->name . ' (' . $user->role . ')',
            'module' => 'Kelola Akun',
            'old_value' => null,
            'new_value' => $user->email ?? $user->phone_number,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Akun "' . $user->name . '" (' . ucfirst($user->role) . ') berhasil dibuat.');
    }

    public function accountUpdate(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,dokter,pasien',
            'phone_number' => 'required|string|unique:users,phone_number,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'status' => 'required|in:active,blocked,pending_verification',
        ]);

        // Cegah admin menurunkan role / menonaktifkan akunnya sendiri (agar tidak terkunci dari sistem)
        if ($user->id === Auth::id() && ($request->role !== 'admin' || $request->status !== 'active')) {
            return back()->withErrors(['account' => 'Anda tidak dapat mengubah role atau menonaktifkan akun Anda sendiri.']);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $this->normalizePhone($request->phone_number),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'activity' => 'Memperbarui akun: ' . $user->name . ' (' . $user->role . ', ' . $user->status . ')',
            'module' => 'Kelola Akun',
            'old_value' => 'Update akun #' . $user->id,
            'new_value' => $user->role . ' / ' . $user->status,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Akun "' . $user->name . '" berhasil diperbarui.');
    }

    public function accountResetPassword(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $request->validate([
            'password' => 'required|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'activity' => 'Reset password akun: ' . $user->name,
            'module' => 'Kelola Akun',
            'old_value' => null,
            'new_value' => 'Password direset',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Password akun "' . $user->name . '" berhasil direset.');
    }

    public function accountDestroy(Request $request, $id)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['account' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $name = $user->name;
        $role = $user->role;
        $user->delete();

        AuditLog::create([
            'admin_id' => Auth::id(),
            'activity' => 'Menghapus akun: ' . $name . ' (' . $role . ')',
            'module' => 'Kelola Akun',
            'old_value' => $name,
            'new_value' => null,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Akun "' . $name . '" berhasil dihapus.');
    }

    public function manageLanding()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $hospitalName = SystemConfiguration::getVal('hospital_name', 'Rumah Sakit Bhayangkara LEMDIKLAT');
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

    // ============================================================
    // Kelola Acara Rumah Sakit (Hospital Events)
    // ============================================================
    public function manageEvents()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $events = json_decode(SystemConfiguration::getVal('hospital_events', '[]'), true);
        if (!is_array($events) || empty($events)) {
            // Tampilkan acara bawaan yang sama dengan yang tampil di dashboard pasien
            $events = [
                [
                    'title' => 'Bakti Sosial & Pemeriksaan Kesehatan',
                    'date' => 'Setiap Jumat',
                    'time' => '08.00 - 11.00 WIB',
                    'location' => 'Lobi Utama Rumah Sakit',
                    'desc' => 'Pemeriksaan tekanan darah, gula darah sewaktu, dan konsultasi kesehatan singkat.',
                    'icon' => 'fa-hand-holding-medical',
                ],
                [
                    'title' => 'Edukasi Kesehatan Keluarga',
                    'date' => 'Minggu ke-2 setiap bulan',
                    'time' => '09.00 - 10.30 WIB',
                    'location' => 'Aula Edukasi Pasien',
                    'desc' => 'Sesi edukasi pencegahan penyakit, pola hidup sehat, dan kesiapsiagaan keluarga.',
                    'icon' => 'fa-people-group',
                ],
                [
                    'title' => 'Donor Darah Rumah Sakit',
                    'date' => 'Setiap Rabu',
                    'time' => '09.00 - 13.00 WIB',
                    'location' => 'Unit Transfusi Darah',
                    'desc' => 'Kegiatan donor darah rutin untuk mendukung kebutuhan layanan pasien.',
                    'icon' => 'fa-droplet',
                ],
            ];
        }

        return view('admin.manage-events', compact('events'));
    }

    public function updateEvents(Request $request)
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $adminId = Auth::id();
        $events = [];

        if ($request->has('event')) {
            foreach ($request->event as $ev) {
                if (!empty($ev['title'])) {
                    $events[] = [
                        'title' => $ev['title'],
                        'desc' => $ev['desc'] ?? '',
                        'date' => $ev['date'] ?? '',
                        'time' => $ev['time'] ?? '',
                        'location' => $ev['location'] ?? '',
                        'icon' => $ev['icon'] ?? 'fa-calendar-check',
                    ];
                }
            }
        }

        SystemConfiguration::setVal('hospital_events', json_encode($events), $adminId);

        AuditLog::create([
            'admin_id' => $adminId,
            'activity' => 'Memperbarui Daftar Acara Rumah Sakit',
            'module' => 'Acara',
            'old_value' => 'Events update',
            'new_value' => json_encode($events),
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Daftar Acara Rumah Sakit berhasil disimpan!');
    }

    // ============================================================
    // Kelola Jadwal Praktik Dokter (halaman tersendiri)
    // ============================================================
    public function manageSchedules()
    {
        if ($redirect = $this->checkAuth()) {
            return $redirect;
        }

        $doctorSchedules = json_decode(SystemConfiguration::getVal('doctor_schedules', '[]'), true);
        if (!is_array($doctorSchedules)) {
            $doctorSchedules = [];
        }

        return view('admin.manage-schedules', compact('doctorSchedules'));
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
