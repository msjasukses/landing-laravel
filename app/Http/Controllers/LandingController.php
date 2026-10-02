<?php

namespace App\Http\Controllers;

use App\Models\AppGroup;
use App\Models\Feature;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $groups = AppGroup::active()->ordered()
            ->with(['applications' => fn ($query) => $query->active()->ordered()])
            ->get();

        return view('landing', [
            'groups' => $groups,
            'features' => Feature::active()->ordered()->get(),
            'totalApps' => $groups->sum(fn (AppGroup $group) => $group->applications->count()),
        ]);
    }
}
