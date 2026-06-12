<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - SISMED | Sistem Informasi & Diagnosa Medis Terpadu</title>
    
    <!-- Google Fonts: Inter & Instrument Sans / Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js CDN for Dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js for interactive UI components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS (Vite build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tailwind Play CDN fallback to ensure layout renders instantly without compiling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f4f8',
                            100: '#d9e2ec',
                            200: '#bcccdc',
                            300: '#9fb3c8',
                            400: '#829ab1',
                            500: '#627d98',
                            600: '#486581',
                            700: '#334e68',
                            800: '#102a43', // Deloitte Deep Navy
                            900: '#0b2545', // Bhayangkara deep navy
                            950: '#00205b', // Deloitte Brand Navy
                        },
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#008751', // Bhayangkara green
                            800: '#065f46',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Outfit', 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .bg-gradient-bhayangkara {
            background: linear-gradient(135deg, #0b2545 0%, #00205b 100%);
        }
        .text-gradient-bhayangkara {
            background: linear-gradient(135deg, #0b2545 0%, #008751 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glow-green {
            box-shadow: 0 0 15px rgba(0, 135, 81, 0.4);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col text-slate-800">

    <!-- Header Navbar -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm backdrop-blur-md bg-white/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('public.home') }}" class="flex items-center space-x-3 group">
                @php
                    $hospitalLogo = \App\Models\SystemConfiguration::getVal('hospital_logo');
                    $hospitalName = \App\Models\SystemConfiguration::getVal('hospital_name', 'Rumah Sakit Bhayangkara LEMDIKLAT');
                @endphp
                @if($hospitalLogo)
                    <img src="{{ $hospitalLogo }}" alt="{{ $hospitalName }}" class="h-11 w-11 object-contain rounded-xl">
                @else
                    <div class="h-11 w-11 rounded-xl bg-gradient-bhayangkara flex items-center justify-center shadow-md shadow-navy-900/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-staff-snake text-white text-lg"></i>
                    </div>
                @endif
                <div>
                    <div class="flex items-center space-x-1.5">
                        <span class="font-extrabold text-lg sm:text-xl text-navy-900 tracking-tight leading-tight">SISMED</span>
                        <span class="hidden sm:inline-block bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] font-bold uppercase px-1.5 py-0.5 rounded">{{ $hospitalName }}</span>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold tracking-wide text-slate-400 block -mt-0.5">Sistem Informasi & Diagnosa Medis Terpadu</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ route('public.home') }}" class="text-sm font-medium text-slate-600 hover:text-navy-900 transition-colors">Beranda</a>
                <a href="{{ route('public.home') }}#features" class="text-sm font-medium text-slate-600 hover:text-navy-900 transition-colors">Fitur Unggulan</a>
                <a href="{{ route('public.home') }}#services" class="text-sm font-medium text-slate-600 hover:text-navy-900 transition-colors">Layanan</a>
                <a href="{{ route('public.home') }}#doctors" class="text-sm font-medium text-slate-600 hover:text-navy-900 transition-colors">Jadwal Dokter</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-navy-900 border border-navy-900 px-4 py-2 rounded-xl hover:bg-navy-50 transition-all flex items-center space-x-2">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Dasbor Admin</span>
                        </a>
                    @elseif(Auth::user()->isDokter())
                        <a href="{{ route('dokter.dashboard') }}" class="text-sm font-medium text-navy-900 border border-navy-900 px-4 py-2 rounded-xl hover:bg-navy-50 transition-all flex items-center space-x-2">
                            <i class="fa-solid fa-user-doctor"></i>
                            <span>Dasbor Dokter IGD</span>
                        </a>
                    @else
                        <a href="{{ route('patient.dashboard') }}" class="text-sm font-medium text-navy-900 border border-navy-900 px-4 py-2 rounded-xl hover:bg-navy-50 transition-all flex items-center space-x-2">
                            <i class="fa-solid fa-hospital-user"></i>
                            <span>Dasbor Pasien</span>
                        </a>
                    @endif
                    <a href="{{ route('auth.logout') }}" class="text-sm font-medium text-white bg-gradient-bhayangkara px-5 py-2.5 rounded-xl hover:shadow-md hover:scale-[1.02] transition-all">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i>Logout
                    </a>
                @else
                    <a href="{{ route('auth.login') }}" class="text-sm font-medium text-slate-700 hover:text-navy-900 px-4 py-2 transition-colors">Masuk</a>
                    <a href="{{ route('auth.register') }}" class="text-sm font-medium text-white bg-gradient-bhayangkara px-5 py-2.5 rounded-xl hover:shadow-lg hover:shadow-navy-950/20 hover:scale-[1.02] transition-all flex items-center space-x-2">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Daftar Akun</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-navy-950 text-slate-400 py-12 border-t border-navy-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Profile -->
                <div class="space-y-4 col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3">
                        @if($hospitalLogo)
                            <img src="{{ $hospitalLogo }}" alt="{{ $hospitalName }}" class="h-10 w-10 object-contain brightness-0 invert">
                        @else
                            <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center">
                                <i class="fa-solid fa-staff-snake text-white text-md"></i>
                            </div>
                        @endif
                        <div>
                            <span class="font-bold text-lg text-white block leading-tight">SISMED</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $hospitalName }}</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 max-w-sm">
                        SISMED (Sistem Informasi & Diagnosa Medis Terpadu) memfasilitasi screening kesehatan mandiri, verifikasi diagnosa oleh Dokter IGD, dan survei kepuasan layanan dalam satu portal terintegrasi.
                    </p>
                    <div class="flex space-x-4 pt-2">
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-900 hover:bg-emerald-700 transition-colors flex items-center justify-center text-white"><i class="fa-brands fa-facebook-f text-sm"></i></a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-900 hover:bg-emerald-700 transition-colors flex items-center justify-center text-white"><i class="fa-brands fa-instagram text-sm"></i></a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-900 hover:bg-emerald-700 transition-colors flex items-center justify-center text-white"><i class="fa-brands fa-twitter text-sm"></i></a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-navy-900 hover:bg-emerald-700 transition-colors flex items-center justify-center text-white"><i class="fa-brands fa-youtube text-sm"></i></a>
                    </div>
                </div>

                <!-- Fast Links -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Layanan</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#services" class="hover:text-white transition-colors">PCR Swab Test</a></li>
                        <li><a href="#services" class="hover:text-white transition-colors">Vaksin Influenza</a></li>
                        <li><a href="#doctors" class="hover:text-white transition-colors">Spesialis Penyakit Dalam</a></li>
                        <li><a href="#doctors" class="hover:text-white transition-colors">Kedokteran Olahraga</a></li>
                    </ul>
                </div>

                <!-- Call Center Info -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Darurat & Kontak</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-phone text-emerald-500"></i>
                            <div>
                                <span class="text-xs text-slate-500 block">Call Center</span>
                                <span class="text-white font-medium">150770</span>
                            </div>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-truck-medical text-red-500"></i>
                            <div>
                                <span class="text-xs text-slate-500 block">Emergency 24/7</span>
                                <span class="text-red-400 font-bold">150990</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-navy-900 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} SISMED &mdash; {{ $hospitalName }}. All Rights Reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
