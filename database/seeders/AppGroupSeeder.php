<?php

namespace Database\Seeders;

use App\Models\AppGroup;
use Illuminate\Database\Seeder;

class AppGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'DEMO',
                'tagline' => 'Sistem Informasi Sekolah Terintegrasi',
                'apps' => [
                    ['name' => 'DATA CENTER', 'description' => 'Pusat Data Sekolah', 'icon' => 'users', 'color' => 'indigo'],
                    ['name' => 'PRESENSI', 'description' => 'Manajemen Kehadiran', 'icon' => 'clock', 'color' => 'green'],
                    ['name' => 'JURNAL', 'description' => 'Jurnal Pembelajaran', 'icon' => 'book', 'color' => 'violet'],
                    ['name' => 'CBT', 'description' => 'Computer Based Test', 'icon' => 'monitor', 'color' => 'sky'],
                ],
            ],
            [
                'name' => 'LAYANAN SEKOLAH',
                'tagline' => 'Aplikasi pendukung kesiswaan & kurikulum',
                'apps' => [
                    ['name' => 'KESISWAAN', 'description' => 'Poin & Prestasi Siswa', 'icon' => 'award', 'color' => 'amber'],
                    ['name' => 'BIMBINGAN KONSELING', 'description' => 'Layanan BK Siswa', 'icon' => 'message', 'color' => 'rose'],
                    ['name' => 'KURIKULUM', 'description' => 'Jadwal & Perangkat Ajar', 'icon' => 'layers', 'color' => 'teal'],
                    ['name' => 'E-RAPOR', 'description' => 'Penilaian & Rapor Digital', 'icon' => 'file-text', 'color' => 'blue'],
                    // Contoh aplikasi nonaktif: tersimpan di database tapi tidak tampil.
                    ['name' => 'PPDB ONLINE', 'description' => 'Penerimaan Peserta Didik Baru', 'icon' => 'graduation', 'color' => 'orange', 'is_active' => false],
                ],
            ],
        ];

        foreach ($groups as $g => $data) {
            $group = AppGroup::create([
                'name' => $data['name'],
                'tagline' => $data['tagline'],
                'sort_order' => $g + 1,
            ]);

            foreach ($data['apps'] as $i => $app) {
                $group->applications()->create($app + [
                    'url' => '#',
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}
