<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppGroupController extends Controller
{
    public function index(): View
    {
        return $this->page();
    }

    public function edit(AppGroup $group): View
    {
        return $this->page($group);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('', 'uploads');
        }

        AppGroup::create($data);

        return redirect()->route('admin.groups.index')->with('success', 'Grup ditambahkan.');
    }

    public function update(Request $request, AppGroup $group): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('logo') || $request->boolean('remove_logo')) {
            $this->deleteLogo($group);
            $data['logo'] = $request->hasFile('logo') ? $request->file('logo')->store('', 'uploads') : null;
        }

        $group->update($data);

        return redirect()->route('admin.groups.index')->with('success', 'Grup diperbarui.');
    }

    public function destroy(AppGroup $group): RedirectResponse
    {
        $this->deleteLogo($group);
        $group->delete(); // aplikasi di dalamnya ikut terhapus (cascade)

        return redirect()->route('admin.groups.index')->with('success', 'Grup beserta aplikasi di dalamnya dihapus.');
    }

    public function toggle(AppGroup $group): RedirectResponse
    {
        $group->toggleActive();

        return back()->with('success', 'Status grup diperbarui.');
    }

    public function move(AppGroup $group, string $direction): RedirectResponse
    {
        $group->move($direction);

        return back();
    }

    private function page(?AppGroup $editing = null): View
    {
        return view('admin.groups.index', [
            'rows' => AppGroup::ordered()->withCount('applications')->get(),
            'editing' => $editing,
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'logo' => SettingController::IMAGE_RULES,
        ]);
        unset($data['logo']);

        return $data + ['is_active' => $request->boolean('is_active')];
    }

    private function deleteLogo(AppGroup $group): void
    {
        if ($group->logo) {
            Storage::disk('uploads')->delete($group->logo);
        }
    }
}
