<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemConfiguration;

class PublicController extends Controller
{
    public function index()
    {
        $hospitalName = SystemConfiguration::getVal('hospital_name', 'Mayapada Hospital');
        $heroTitle = SystemConfiguration::getVal('hospital_hero_title', 'Empowering Your Health');
        $heroSubtitle = SystemConfiguration::getVal('hospital_hero_subtitle', 'Portal Layanan Rumah Sakit');
        $hospitalImage = SystemConfiguration::getVal('hospital_image', 'https://images.unsplash.com/photo-1586773860418-d3b3b998de55?auto=format&fit=crop&w=1200&q=80');

        $doctorSchedules = json_decode(SystemConfiguration::getVal('doctor_schedules', '[]'), true);
        $hospitalServices = json_decode(SystemConfiguration::getVal('hospital_services', '[]'), true);

        return view('public.home', compact(
            'hospitalName',
            'heroTitle',
            'heroSubtitle',
            'hospitalImage',
            'doctorSchedules',
            'hospitalServices'
        ));
    }
}
