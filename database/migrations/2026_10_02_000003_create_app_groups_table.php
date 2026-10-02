<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grup / instansi (mis. nama sekolah) yang menaungi daftar aplikasi.
     */
    public function up(): void
    {
        Schema::create('app_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('tagline', 150)->nullable();
            $table->string('logo')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_groups');
    }
};
