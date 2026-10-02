<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Support\Icon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeatureController extends Controller
{
    public function index(): View
    {
        return $this->page();
    }

    public function edit(Feature $feature): View
    {
        return $this->page($feature);
    }

    public function store(Request $request): RedirectResponse
    {
        Feature::create($this->validated($request));

        return redirect()->route('admin.features.index')->with('success', 'Fitur ditambahkan.');
    }

    public function update(Request $request, Feature $feature): RedirectResponse
    {
        $feature->update($this->validated($request));

        return redirect()->route('admin.features.index')->with('success', 'Fitur diperbarui.');
    }

    public function destroy(Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()->route('admin.features.index')->with('success', 'Fitur dihapus.');
    }

    public function toggle(Feature $feature): RedirectResponse
    {
        $feature->toggleActive();

        return back()->with('success', 'Status fitur diperbarui.');
    }

    public function move(Feature $feature, string $direction): RedirectResponse
    {
        $feature->move($direction);

        return back();
    }

    private function page(?Feature $editing = null): View
    {
        return view('admin.features.index', [
            'rows' => Feature::ordered()->get(),
            'editing' => $editing,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:150'],
            'icon' => ['required', Rule::in(Icon::names())],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
