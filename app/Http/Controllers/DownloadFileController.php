<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadFileController extends Controller
{
    /**
     * Mengirim file unduhan (atau mengarahkan ke tautan luar) dan mencatat jumlah unduhan.
     */
    public function __invoke(Download $download): StreamedResponse|RedirectResponse
    {
        abort_unless($download->is_active, 404);

        if ($download->hasStoredFile()) {
            abort_unless(Storage::disk('local')->exists($download->file_path), 404);
            $download->increment('download_count');

            return Storage::disk('local')->download(
                $download->file_path,
                $download->file_name,
                array_filter(['Content-Type' => $download->mimeType()]),
            );
        }

        abort_unless(filled($download->external_url), 404);
        $download->increment('download_count');

        return redirect()->away($download->external_url);
    }
}
