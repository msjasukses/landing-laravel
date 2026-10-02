<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@sekolah.sch.id',
                'password' => 'admin123', // di-hash otomatis oleh cast 'hashed'
            ],
        );
    }
}
