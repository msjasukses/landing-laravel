<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AppGroupController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\DownloadFileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');
Route::get('unduh/{download}', DownloadFileController::class)->name('downloads.file');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');

        // Daftar + form tambah/ubah ada di satu halaman (index & edit).
        $sortable = [
            'features' => [FeatureController::class, 'feature'],
            'groups' => [AppGroupController::class, 'group'],
            'apps' => [ApplicationController::class, 'application'],
            'downloads' => [DownloadController::class, 'download'],
        ];

        foreach ($sortable as $uri => [$controller, $param]) {
            Route::resource($uri, $controller)
                ->except(['create', 'show'])
                ->parameters([$uri => $param]);

            Route::patch("$uri/{{$param}}/toggle", [$controller, 'toggle'])->name("$uri.toggle");
            Route::patch("$uri/{{$param}}/move/{direction}", [$controller, 'move'])
                ->whereIn('direction', ['up', 'down'])
                ->name("$uri.move");
        }
    });
});

// Portal Siswa: login memakai akun siswa Data Center, data dari aplikasi
// Absensi, Kurikulum, dan Kesiswaan.
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::middleware('guest:siswa')->group(function () {
        Route::get('login', [Siswa\AuthController::class, 'create'])->name('login');
        Route::post('login', [Siswa\AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    });

    Route::middleware('auth:siswa')->group(function () {
        Route::post('logout', [Siswa\AuthController::class, 'destroy'])->name('logout');

        Route::get('/', Siswa\DashboardController::class)->name('dashboard');
        Route::get('absensi', Siswa\AbsensiController::class)->name('absensi');
        Route::get('agenda', Siswa\AgendaController::class)->name('agenda');
        Route::get('materi', Siswa\MateriController::class)->name('materi');
        Route::get('prestasi', Siswa\PrestasiController::class)->name('prestasi');
        Route::get('pelanggaran', Siswa\PelanggaranController::class)->name('pelanggaran');

        Route::get('tugas', [Siswa\TugasController::class, 'index'])->name('tugas.index');
        Route::get('tugas/{tugas}', [Siswa\TugasController::class, 'show'])->name('tugas.show');
        Route::post('tugas/{tugas}', [Siswa\TugasController::class, 'store'])->name('tugas.store');

        Route::get('jurnal-pkl', [Siswa\JurnalPklController::class, 'index'])->name('jurnal.index');
        Route::get('jurnal-pkl/isi', [Siswa\JurnalPklController::class, 'form'])->name('jurnal.form');
        Route::post('jurnal-pkl', [Siswa\JurnalPklController::class, 'store'])->name('jurnal.store');
        Route::get('jurnal-pkl/cetak', [Siswa\JurnalPklController::class, 'cetak'])->name('jurnal.cetak');
        Route::get('jurnal-pkl/{jurnal}', [Siswa\JurnalPklController::class, 'show'])->name('jurnal.show');
        Route::delete('jurnal-pkl/{jurnal}', [Siswa\JurnalPklController::class, 'destroy'])->name('jurnal.destroy');

        Route::prefix('berkas')->name('berkas.')->controller(Siswa\BerkasController::class)->group(function () {
            Route::get('materi/{materi}', 'materi')->name('materi');
            Route::get('tugas/{tugas}', 'tugas')->name('tugas');
            Route::get('pengumpulan/{pengumpulan}', 'pengumpulan')->name('pengumpulan');
            Route::get('jurnal/{jurnal}', 'jurnal')->name('jurnal');
        });
    });
});
