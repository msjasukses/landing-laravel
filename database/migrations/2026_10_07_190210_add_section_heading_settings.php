<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Judul & sub judul seksi daftar aplikasi (pengaturan baru).
     * insertOrIgnore agar nilai yang sudah diubah admin tidak tertimpa.
     */
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'section_title',
                'value' => 'Semua layanan sekolah dalam satu portal',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'section_subtitle',
                'value' => 'Pilih aplikasi untuk mulai bekerja. Gunakan pencarian atau filter grup untuk menemukannya lebih cepat.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['section_title', 'section_subtitle'])->delete();
    }
};
