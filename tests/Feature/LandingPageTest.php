<?php

namespace Tests\Feature;

use App\Models\AppGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_landing_page_shows_seeded_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Digital Learning Management System')
            ->assertSee('Login Protect')
            ->assertSee('AVAILABLE APPS')
            ->assertSeeInOrder(['DEMO', 'DATA CENTER', 'PRESENSI', 'JURNAL', 'CBT', 'LAYANAN SEKOLAH', 'KESISWAAN'])
            ->assertSee('id="appSearch"', false);
    }

    public function test_inactive_apps_and_groups_are_hidden(): void
    {
        $this->get('/')->assertDontSee('PPDB ONLINE');

        AppGroup::where('name', 'LAYANAN SEKOLAH')->update(['is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('DATA CENTER')
            ->assertDontSee('KESISWAAN');
    }
}
