<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Data contoh: akun admin, pengaturan situs, fitur header, grup & aplikasi.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            FeatureSeeder::class,
            AppGroupSeeder::class,
        ]);
    }
}
