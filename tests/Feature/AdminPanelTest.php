<?php

namespace Tests\Feature;

use App\Models\AppGroup;
use App\Models\Application;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('username', 'admin')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_log_in_with_username(): void
    {
        $this->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'admin123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin());
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), ['username' => 'admin', 'password' => 'salah'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_every_admin_page_renders(): void
    {
        $this->actingAs($this->admin());
        $app = Application::first();

        foreach ([
            route('admin.dashboard'),
            route('admin.settings.edit'),
            route('admin.features.index'),
            route('admin.features.edit', Feature::first()),
            route('admin.groups.index'),
            route('admin.groups.edit', $app->group),
            route('admin.apps.index'),
            route('admin.apps.index', ['group' => $app->app_group_id]),
            route('admin.apps.edit', $app),
            route('admin.account.edit'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_settings_update_is_reflected_on_landing_page(): void
    {
        Storage::fake('uploads');

        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'site_name' => 'SMK Negeri Contoh',
                'hero_title' => 'Portal Aplikasi Sekolah',
                'hero_overlay' => 40,
                'section_label' => 'DAFTAR APLIKASI',
                'accent_color' => '#0ea5e9',
                'primary_color' => '#16a34a',
                'hero_image' => UploadedFile::fake()->image('hero.jpg', 1920, 600),
            ])
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHasNoErrors();

        $hero = setting('hero_image');
        Storage::disk('uploads')->assertExists($hero);

        $this->get('/')
            ->assertSee('Portal Aplikasi Sekolah')
            ->assertSee('DAFTAR APLIKASI')
            ->assertSee('--accent: #0ea5e9', false)
            ->assertSee('uploads/'.$hero, false)
            ->assertDontSee('id="appSearch"', false); // checkbox show_search tidak dikirim
    }

    public function test_settings_reject_invalid_color(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'site_name' => 'X',
                'hero_overlay' => 50,
                'accent_color' => 'merah',
                'primary_color' => '#4f46e5',
            ])
            ->assertSessionHasErrors('accent_color');
    }

    public function test_feature_crud_toggle_and_move(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.features.store'), [
            'title' => 'Mobile Friendly', 'subtitle' => 'Nyaman di HP', 'icon' => 'monitor', 'is_active' => 1,
        ])->assertRedirect(route('admin.features.index'));

        $feature = Feature::where('title', 'Mobile Friendly')->firstOrFail();
        $this->assertSame(4, $feature->sort_order);

        $this->patch(route('admin.features.move', [$feature, 'up']));
        $this->assertSame(
            ['Login Protect', 'Protected', 'Mobile Friendly', 'Data Integration'],
            Feature::ordered()->pluck('title')->all(),
        );

        $this->patch(route('admin.features.toggle', $feature));
        $this->assertFalse($feature->fresh()->is_active);

        $this->put(route('admin.features.update', $feature), ['title' => 'Responsif', 'icon' => 'star'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Responsif', $feature->fresh()->title);

        $this->delete(route('admin.features.destroy', $feature));
        $this->assertModelMissing($feature);
    }

    public function test_group_with_logo_and_cascade_delete(): void
    {
        Storage::fake('uploads');
        $this->actingAs($this->admin());

        $this->post(route('admin.groups.store'), [
            'name' => 'SMK Negeri 1',
            'tagline' => 'Sekolah contoh',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $group = AppGroup::where('name', 'SMK Negeri 1')->firstOrFail();
        Storage::disk('uploads')->assertExists($group->logo);

        $group->applications()->create(['name' => 'E-LEARNING', 'icon' => 'video', 'color' => 'red']);

        $this->delete(route('admin.groups.destroy', $group));

        $this->assertModelMissing($group);
        $this->assertDatabaseMissing('applications', ['name' => 'E-LEARNING']);
        Storage::disk('uploads')->assertMissing($group->logo);
    }

    public function test_application_crud_keeps_group_filter_and_order(): void
    {
        $this->actingAs($this->admin());
        [$demo, $layanan] = AppGroup::ordered()->get()->all();

        $this->post(route('admin.apps.store', ['group' => $demo->id]), [
            'app_group_id' => $demo->id,
            'name' => 'E-LEARNING',
            'url' => 'https://elearning.sekolah.sch.id',
            'icon' => 'video',
            'color' => 'red',
            'open_in_new_tab' => 1,
            'is_active' => 1,
        ])->assertRedirect(route('admin.apps.index', ['group' => $demo->id]));

        $app = Application::where('name', 'E-LEARNING')->firstOrFail();
        $this->assertSame(5, $app->sort_order);

        // Pindah grup: urutan diletakkan paling akhir pada grup tujuan.
        $this->put(route('admin.apps.update', $app), [
            'app_group_id' => $layanan->id, 'name' => 'E-LEARNING', 'icon' => 'video', 'color' => 'red',
        ])->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertSame($layanan->id, $app->app_group_id);
        $this->assertSame(6, $app->sort_order);
        $this->assertFalse($app->is_active);

        $this->post(route('admin.apps.store'), ['app_group_id' => $demo->id, 'name' => 'X', 'icon' => 'tidak-ada', 'color' => 'red'])
            ->assertSessionHasErrors('icon');
    }

    public function test_account_update_requires_current_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->put(route('admin.account.update'), [
            'name' => 'Operator', 'username' => 'operator', 'current_password' => 'salah',
        ])->assertSessionHasErrors('current_password');

        $this->put(route('admin.account.update'), [
            'name' => 'Operator',
            'username' => 'operator',
            'current_password' => 'admin123',
            'new_password' => 'rahasia123',
            'new_password_confirmation' => 'rahasia123',
        ])->assertSessionHasNoErrors();

        $admin->refresh();
        $this->assertSame('operator', $admin->username);
        $this->assertTrue(Hash::check('rahasia123', $admin->password));
    }
}
