<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Database\Factories\DownloadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

#[Fillable([
    'name', 'description', 'version', 'platform', 'file_path', 'file_name', 'file_size',
    'external_url', 'icon', 'color', 'sort_order', 'is_active',
])]
class Download extends Model
{
    /** @use HasFactory<DownloadFactory> */
    use HasFactory, Sortable;

    /**
     * Platform yang bisa dipilih admin.
     *
     * @var array<string, string>
     */
    public const PLATFORMS = [
        'android' => 'Android',
        'ios' => 'iOS',
        'windows' => 'Windows',
        'other' => 'Lainnya',
    ];

    /**
     * Ekstensi file yang boleh diunggah.
     *
     * @var list<string>
     */
    public const EXTENSIONS = ['apk', 'xapk', 'ipa', 'exe', 'msi', 'zip', 'rar', 'pdf'];

    /**
     * Batas ukuran file dari aplikasi (KB); batas efektif juga mengikuti php.ini.
     */
    public const MAX_KB = 1024 * 1024;

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'download_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Hanya entri yang benar-benar bisa diunduh: punya file atau tautan luar.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where(fn (Builder $query) => $query->whereNotNull('file_path')->orWhereNotNull('external_url'));
    }

    public function hasStoredFile(): bool
    {
        return $this->file_path !== null;
    }

    /**
     * Content-Type khusus agar HP Android langsung mengenali file APK.
     */
    public function mimeType(): ?string
    {
        return $this->extension === 'APK' ? 'application/vnd.android.package-archive' : null;
    }

    protected function platformLabel(): Attribute
    {
        return Attribute::get(fn () => self::PLATFORMS[$this->platform] ?? self::PLATFORMS['other']);
    }

    /**
     * Versi siap tampil: "2.1" => "v2.1", "Beta" tetap "Beta".
     */
    protected function versionLabel(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            blank($this->version) => null,
            ctype_digit($this->version[0]) => 'v'.$this->version,
            default => $this->version,
        });
    }

    protected function extension(): Attribute
    {
        return Attribute::get(fn () => $this->file_name ? strtoupper(pathinfo($this->file_name, PATHINFO_EXTENSION)) : null);
    }

    protected function fileSizeLabel(): Attribute
    {
        return Attribute::get(fn () => $this->file_size ? Number::fileSize($this->file_size, maxPrecision: 1) : null);
    }
}
