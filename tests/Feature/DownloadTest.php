<?php

namespace Tests\Feature;

use App\Models\Download;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Isian wajib form unduhan, ditambah field yang sedang diuji.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'EXAMBRO',
            'platform' => 'android',
            'icon' => 'smartphone',
            'color' => 'green',
            'is_active' => 1,
        ];
    }

    public function test_guest_cannot_add_a_download(): void
    {
        $this->post(route('admin.downloads.store'), $this->payload())
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('downloads', 0);
    }

    public function test_admin_uploads_apk_to_private_storage(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create())
            ->post(route('admin.downloads.store'), $this->payload([
                'version' => '2.1',
                'file' => UploadedFile::fake()->create('Exambro v2.1.apk', 2048),
            ]))
            ->assertRedirect(route('admin.downloads.index'))
            ->assertSessionHasNoErrors();

        $download = Download::sole();
        $this->assertSame('Exambro v2.1.apk', $download->file_name);
        $this->assertSame(2048 * 1024, $download->file_size);
        $this->assertStringEndsWith('.apk', $download->file_path);
        Storage::disk('local')->assertExists($download->file_path);
    }

    public function test_download_needs_a_file_or_an_external_link(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.downloads.store'), $this->payload())
            ->assertSessionHasErrors(['file' => 'Unggah file atau isi tautan unduhan luar.']);

        $this->assertDatabaseCount('downloads', 0);
    }

    public function test_file_type_outside_the_allowed_list_is_rejected(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create())
            ->post(route('admin.downloads.store'), $this->payload([
                'file' => UploadedFile::fake()->create('script.php', 1),
            ]))
            ->assertSessionHasErrors(['file' => 'File harus berformat: apk, xapk, ipa, exe, msi, zip, rar, pdf.']);

        $this->assertDatabaseCount('downloads', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_external_link_is_saved_without_a_file(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.downloads.store'), $this->payload([
                'external_url' => 'https://play.google.com/store/apps/details?id=id.sch.contoh',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('downloads', [
            'name' => 'EXAMBRO',
            'file_path' => null,
            'external_url' => 'https://play.google.com/store/apps/details?id=id.sch.contoh',
        ]);
    }

    public function test_uploading_a_new_file_replaces_the_old_one(): void
    {
        Storage::fake('local');
        $download = Download::factory()->create();
        Storage::disk('local')->put($download->file_path, 'versi-lama');
        $oldPath = $download->file_path;

        $this->actingAs(User::factory()->create())
            ->put(route('admin.downloads.update', $download), $this->payload([
                'file' => UploadedFile::fake()->create('exambro-baru.apk', 10),
            ]))
            ->assertSessionHasNoErrors();

        $download->refresh();
        $this->assertSame('exambro-baru.apk', $download->file_name);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($download->file_path);
    }

    public function test_file_can_be_removed_in_favour_of_an_external_link(): void
    {
        Storage::fake('local');
        $download = Download::factory()->create();
        Storage::disk('local')->put($download->file_path, 'apk');
        $oldPath = $download->file_path;

        $this->actingAs(User::factory()->create())
            ->put(route('admin.downloads.update', $download), $this->payload([
                'remove_file' => 1,
                'external_url' => 'https://example.com/exambro',
            ]))
            ->assertSessionHasNoErrors();

        $download->refresh();
        $this->assertNull($download->file_path);
        $this->assertNull($download->file_size);
        Storage::disk('local')->assertMissing($oldPath);
    }

    public function test_deleting_a_download_deletes_its_file(): void
    {
        Storage::fake('local');
        $download = Download::factory()->create();
        Storage::disk('local')->put($download->file_path, 'apk');

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.downloads.destroy', $download))
            ->assertRedirect(route('admin.downloads.index'));

        $this->assertModelMissing($download);
        Storage::disk('local')->assertMissing($download->file_path);
    }

    public function test_visitor_downloads_apk_and_the_counter_increases(): void
    {
        Storage::fake('local');
        $download = Download::factory()->create(['file_name' => 'exambro.apk']);
        Storage::disk('local')->put($download->file_path, 'isi-apk');

        $this->get(route('downloads.file', $download))
            ->assertDownload('exambro.apk')
            ->assertHeader('Content-Type', 'application/vnd.android.package-archive');

        $this->assertSame(1, $download->fresh()->download_count);
    }

    public function test_external_download_redirects_and_is_counted(): void
    {
        $download = Download::factory()->externalLink()->create();

        $this->get(route('downloads.file', $download))
            ->assertRedirect('https://play.google.com/store/apps/details?id=id.sch.contoh');

        $this->assertSame(1, $download->fresh()->download_count);
    }

    public function test_inactive_download_is_hidden_and_cannot_be_downloaded(): void
    {
        Storage::fake('local');
        $download = Download::factory()->inactive()->create(['name' => 'APLIKASI LAMA']);
        Storage::disk('local')->put($download->file_path, 'apk');

        $this->get(route('downloads.file', $download))->assertNotFound();
        $this->get('/')->assertDontSee('APLIKASI LAMA');

        $this->assertSame(0, $download->fresh()->download_count);
    }

    public function test_landing_page_lists_active_downloads(): void
    {
        $this->get('/')->assertDontSee('id="unduhan"', false);

        $download = Download::factory()->create(['name' => 'EXAMBRO', 'version' => '2.1', 'file_size' => 13107200]);

        $this->get('/')
            ->assertSee('id="unduhan"', false)
            ->assertSee('Unduh aplikasi pendukung')
            ->assertSeeInOrder(['EXAMBRO', 'Android', 'v2.1', 'APK', '12,5 MB'])
            ->assertSee(route('downloads.file', $download), false);
    }
}
