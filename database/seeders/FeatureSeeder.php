<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['icon' => 'lock', 'title' => 'Login Protect', 'subtitle' => 'Keamanan Login akun'],
            ['icon' => 'shield', 'title' => 'Protected', 'subtitle' => 'Ujian lebih aman & terjaga'],
            ['icon' => 'refresh', 'title' => 'Data Integration', 'subtitle' => 'Sinkron data real-time'],
        ];

        foreach ($features as $i => $feature) {
            Feature::create($feature + ['sort_order' => $i + 1]);
        }
    }
}
