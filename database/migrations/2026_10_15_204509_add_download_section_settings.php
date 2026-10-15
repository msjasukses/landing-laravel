<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Label, judul & keterangan seksi Unduhan di landing page.
     * insertOrIgnore agar nilai yang sudah diubah admin tidak tertimpa.
     */
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insertOrIgnore([
            ['key' => 'downloads_label', 'value' => 'UNDUHAN', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'downloads_title', 'value' => 'Unduh aplikasi pendukung', 'created_at' => $now, 'updated_at' => $now],
            [
                'key' => 'downloads_subtitle',
                'value' => 'Pasang aplikasi resmi sekolah di perangkat Anda. Semua file tersedia langsung dari server sekolah.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['downloads_label', 'downloads_title', 'downloads_subtitle'])->delete();
    }
};
