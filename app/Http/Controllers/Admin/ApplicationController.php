<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppGroup;
use App\Models\Application;
use App\Support\Icon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        return $this->page($request);
    }

    public function edit(Request $request, Application $application): View
    {
        return $this->page($request, $application);
    }

    public function store(Request $request): RedirectResponse
    {
        Application::create($this->validated($request));

        return $this->toList($request)->with('success', 'Aplikasi ditambahkan.');
    }

    public function update(Request $request, Application $application): RedirectResponse
    {
        $data = $this->validated($request);

        // Pindah grup: letakkan di urutan paling akhir pada grup tujuan.
        if ((int) $data['app_group_id'] !== (int) $application->app_group_id) {
            $data['sort_order'] = Application::where('app_group_id', $data['app_group_id'])->max('sort_order') + 1;
        }

        $application->update($data);

        return $this->toList($request)->with('success', 'Aplikasi diperbarui.');
    }

    public function destroy(Request $request, Application $application): RedirectResponse
    {
        $application->delete();

        return $this->toList($request)->with('success', 'Aplikasi dihapus.');
    }

    public function toggle(Application $application): RedirectResponse
    {
        $application->toggleActive();

        return back()->with('success', 'Status aplikasi diperbarui.');
    }

    public function move(Application $application, string $direction): RedirectResponse
    {
        $application->move($direction);

        return back();
    }

    private function page(Request $request, ?Application $editing = null): View
    {
        $filter = $request->integer('group');

        $rows = Application::query()
            ->select('applications.*')
            ->with('group')
            ->join('app_groups', 'app_groups.id', '=', 'applications.app_group_id')
            ->when($filter, fn ($query) => $query->where('applications.app_group_id', $filter))
            ->orderBy('app_groups.sort_order')
            ->orderBy('app_groups.id')
            ->orderBy('applications.sort_order')
            ->orderBy('applications.id')
            ->get();

        return view('admin.apps.index', [
            'groups' => AppGroup::ordered()->get(),
            'rows' => $rows,
            'filter' => $filter,
            'editing' => $editing,
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'app_group_id' => ['required', 'exists:app_groups,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon' => ['required', Rule::in(Icon::names())],
            'color' => ['required', Rule::in(Icon::COLORS)],
        ]) + [
            'open_in_new_tab' => $request->boolean('open_in_new_tab'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * Kembali ke daftar dengan filter grup yang sedang dipakai.
     */
    private function toList(Request $request): RedirectResponse
    {
        return redirect()->route('admin.apps.index', array_filter(['group' => $request->integer('group')]));
    }
}
