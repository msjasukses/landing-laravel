<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put([
            'site_name' => 'Digital Learning Management System',
            'site_tagline' => 'Sistem Informasi Sekolah Terintegrasi',
            'meta_description' => 'Portal aplikasi sekolah terintegrasi: data center, presensi, jurnal, dan CBT.',
            'favicon' => null,
            'hero_title' => 'Digital Learning Management System',
            'hero_subtitle' => 'Selamat datang di Sistem Manajemen Pembelajaran Digital DEMO.',
            'hero_badge' => 'Sistem Informasi Sekolah Terintegrasi',
            'hero_image' => null,
            'hero_overlay' => '65',
            'section_label' => 'AVAILABLE APPS',
            'accent_color' => '#e11d48',
            'primary_color' => '#4f46e5',
            'show_search' => '1',
            'footer_text' => '© '.date('Y').' Digital Learning Management System. All rights reserved.',
            'contact_email' => 'admin@sekolah.sch.id',
            'contact_phone' => '(021) 1234-5678',
            'contact_address' => 'Jl. Pendidikan No. 1, Jakarta',
        ]);
    }
}
