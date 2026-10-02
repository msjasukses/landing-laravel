<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppGroup;
use App\Models\Application;
use App\Models\Feature;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'apps' => Application::count(),
                'apps_active' => Application::active()->count(),
                'groups' => AppGroup::count(),
                'groups_active' => AppGroup::active()->count(),
                'features' => Feature::active()->count(),
            ],
            'recent' => Application::with('group')->latest('id')->limit(6)->get(),
        ]);
    }
}
