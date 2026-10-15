<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * File unduhan (APK, installer, dokumen) yang tampil di landing page.
     * Isinya berupa file yang diunggah admin atau tautan luar (mis. Play Store).
     */
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 200)->nullable();
            $table->string('version', 30)->nullable();
            $table->string('platform', 20)->default('android');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('external_url')->nullable();
            $table->string('icon', 50)->default('smartphone');
            $table->string('color', 20)->default('green');
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
