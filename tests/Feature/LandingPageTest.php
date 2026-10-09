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
            ->assertSee('Semua layanan sekolah dalam satu portal')
            ->assertSeeInOrder(['DEMO', 'DATA CENTER', 'PRESENSI', 'JURNAL', 'CBT', 'LAYANAN SEKOLAH', 'KESISWAAN'])
            ->assertSee('id="appSearch"', false);
    }

    public function test_group_filter_appears_only_when_more_than_one_group_is_active(): void
    {
        $groups = AppGroup::ordered()->get();

        $this->get('/')
            ->assertSee('data-filter="'.$groups[0]->id.'"', false)
            ->assertSee('data-filter="'.$groups[1]->id.'"', false);

        $groups[1]->update(['is_active' => false]);

        $this->get('/')->assertDontSee('data-filter=', false);
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
