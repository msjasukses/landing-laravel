<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Support\Icon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DownloadController extends Controller
{
    /**
     * Disk privat (storage/app/private): file hanya bisa diambil lewat route unduhan.
     */
    private const DISK = 'local';

    public function index(): View
    {
        return $this->page();
    }

    public function edit(Download $download): View
    {
        return $this->page($download);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('file')) {
            $data += $this->storeFile($request->file('file'));
        }

        Download::create($data);

        return redirect()->route('admin.downloads.index')->with('success', 'File unduhan ditambahkan.');
    }

    public function update(Request $request, Download $download): RedirectResponse
    {
        $data = $this->validated($request, $download);

        if ($request->hasFile('file') || $request->boolean('remove_file')) {
            $this->deleteFile($download);
            $data += $request->hasFile('file')
                ? $this->storeFile($request->file('file'))
                : ['file_path' => null, 'file_name' => null, 'file_size' => null];
        }

        $download->update($data);

        return redirect()->route('admin.downloads.index')->with('success', 'File unduhan diperbarui.');
    }

    public function destroy(Download $download): RedirectResponse
    {
        $this->deleteFile($download);
        $download->delete();

        return redirect()->route('admin.downloads.index')->with('success', 'File unduhan dihapus.');
    }

    public function toggle(Download $download): RedirectResponse
    {
        $download->toggleActive();

        return back()->with('success', 'Status file unduhan diperbarui.');
    }

    public function move(Download $download, string $direction): RedirectResponse
    {
        $download->move($direction);

        return back();
    }

    private function page(?Download $editing = null): View
    {
        return view('admin.downloads.index', [
            'rows' => Download::ordered()->get(),
            'editing' => $editing,
            'maxBytes' => min(UploadedFile::getMaxFilesize(), Download::MAX_KB * 1024),
        ]);
    }

    /**
     * File wajib ada kecuali diisi tautan luar (atau saat ubah, file lama dipertahankan).
     *
     * Ekstensi dicek dengan aturan `extensions`, bukan `mimes`: APK adalah arsip ZIP
     * sehingga deteksi isi file mengenalinya sebagai zip/jar. File disimpan di disk privat
     * dan selalu dikirim sebagai unduhan (attachment), tidak pernah dijalankan server.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Download $download = null): array
    {
        $keepsFile = $download?->hasStoredFile() && ! $request->boolean('remove_file');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'version' => ['nullable', 'string', 'max:30'],
            'platform' => ['required', Rule::in(array_keys(Download::PLATFORMS))],
            'description' => ['nullable', 'string', 'max:200'],
            'file' => [
                Rule::requiredIf(! $keepsFile && ! $request->filled('external_url')),
                'nullable',
                'file',
                'extensions:'.implode(',', Download::EXTENSIONS),
                'max:'.Download::MAX_KB,
            ],
            'external_url' => ['nullable', 'url:http,https', 'max:255'],
            'icon' => ['required', Rule::in(Icon::names())],
            'color' => ['required', Rule::in(Icon::COLORS)],
        ], [
            'file.required' => 'Unggah file atau isi tautan unduhan luar.',
        ]);

        unset($data['file']);

        return $data + ['is_active' => $request->boolean('is_active')];
    }

    /**
     * @return array{file_path: string, file_name: string, file_size: int}
     */
    private function storeFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = trim(preg_replace('/[^\w.\- ]+/u', '-', $baseName), '-. ') ?: 'unduhan';

        return [
            'file_path' => $file->storeAs('downloads', Str::random(40).'.'.$extension, self::DISK),
            'file_name' => Str::limit($safeName, 120, '').'.'.$extension,
            'file_size' => $file->getSize(),
        ];
    }

    private function deleteFile(Download $download): void
    {
        if ($download->hasStoredFile()) {
            Storage::disk(self::DISK)->delete($download->file_path);
        }
    }
}
