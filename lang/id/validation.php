<?php

/*
| Pesan validasi Bahasa Indonesia untuk aturan yang dipakai aplikasi ini.
| Aturan lain otomatis memakai pesan bawaan (Inggris) dari APP_FALLBACK_LOCALE.
*/

return [
    'alpha_dash' => ':Attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'between' => [
        'numeric' => ':Attribute harus bernilai antara :min sampai :max.',
    ],
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password saat ini salah.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa file.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'file' => 'Ukuran :attribute maksimal :max kilobyte.',
        'numeric' => ':Attribute maksimal bernilai :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'mimes' => ':Attribute harus berupa file: :values.',
    'min' => [
        'string' => ':Attribute minimal :min karakter.',
    ],
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah dipakai.',
    'uploaded' => ':Attribute gagal diunggah.',

    'attributes' => [
        'site_name' => 'nama situs',
        'site_tagline' => 'tagline',
        'meta_description' => 'deskripsi meta',
        'hero_title' => 'judul hero',
        'hero_subtitle' => 'sub judul',
        'hero_badge' => 'teks penegas',
        'hero_overlay' => 'kegelapan overlay',
        'hero_image' => 'gambar hero',
        'section_label' => 'label seksi',
        'accent_color' => 'warna label seksi',
        'primary_color' => 'warna utama',
        'footer_text' => 'teks footer',
        'contact_email' => 'email',
        'contact_phone' => 'telepon',
        'contact_address' => 'alamat',
        'favicon' => 'favicon',
        'title' => 'judul',
        'subtitle' => 'keterangan',
        'icon' => 'ikon',
        'color' => 'warna ikon',
        'name' => 'nama',
        'tagline' => 'tagline',
        'logo' => 'logo',
        'app_group_id' => 'grup',
        'description' => 'deskripsi',
        'url' => 'URL',
        'username' => 'username',
        'password' => 'password',
        'current_password' => 'password saat ini',
        'new_password' => 'password baru',
    ],
];
