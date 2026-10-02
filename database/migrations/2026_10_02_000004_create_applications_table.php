<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kartu aplikasi yang tampil di landing page.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_group_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description', 150)->nullable();
            $table->string('url')->nullable();
            $table->string('icon', 50)->default('grid');
            $table->string('color', 20)->default('indigo');
            $table->boolean('open_in_new_tab')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
