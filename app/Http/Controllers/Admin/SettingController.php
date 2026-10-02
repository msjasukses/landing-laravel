<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Format gambar yang boleh diunggah (hero & favicon).
     */
    public const IMAGE_RULES = ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg,ico', 'max:4096'];

    public function edit(): View
    {
        return view('admin.settings');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:150'],
            'site_tagline' => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'hero_title' => ['nullable', 'string', 'max:200'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
            'hero_badge' => ['nullable', 'string', 'max:150'],
            'hero_overlay' => ['required', 'integer', 'between:0,90'],
            'section_label' => ['nullable', 'string', 'max:100'],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'footer_text' => ['nullable', 'string', 'max:200'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:200'],
            'hero_image' => self::IMAGE_RULES,
            'favicon' => self::IMAGE_RULES,
        ]);

        $data['show_search'] = $request->boolean('show_search') ? '1' : '0';

        foreach (['hero_image' => 'remove_hero', 'favicon' => 'remove_favicon'] as $field => $remove) {
            $current = setting($field);

            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('', 'uploads');
            } elseif ($request->boolean($remove)) {
                $data[$field] = null;
            } else {
                unset($data[$field]);

                continue;
            }

            if ($current !== '') {
                Storage::disk('uploads')->delete($current);
            }
        }

        Setting::put($data);

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
