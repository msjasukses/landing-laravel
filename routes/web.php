<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AppGroupController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

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
