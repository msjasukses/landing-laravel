@extends('layouts.admin')

@section('title', 'Pengaturan Umum')

@php
    $heroImg = upload_url(setting('hero_image'));
    $favicon = upload_url(setting('favicon'));
    $val = fn (string $key, string $default = '') => old($key, setting($key, $default));
@endphp

@section('content')
<form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="stack">
  @csrf
  @method('PUT')

  <section class="card">
    <div class="card__head"><h2><x-icon name="globe" size="18" /> Identitas Situs</h2></div>
    <div class="grid-2">
      <label class="field">
        <span>Nama situs <em>(judul tab browser)</em></span>
        <input type="text" name="site_name" value="{{ $val('site_name') }}" required>
      </label>
      <label class="field">
        <span>Tagline</span>
        <input type="text" name="site_tagline" value="{{ $val('site_tagline') }}">
      </label>
    </div>
    <label class="field">
      <span>Deskripsi meta <em>(SEO)</em></span>
      <textarea name="meta_description" rows="2">{{ $val('meta_description') }}</textarea>
    </label>
    <div class="grid-2">
      <div class="field">
        <span>Favicon</span>
        @if ($favicon)
          <div class="preview preview--sm"><img src="{{ $favicon }}" alt="Favicon"></div>
          <label class="check"><input type="checkbox" name="remove_favicon" value="1"> Hapus favicon</label>
        @endif
        <input type="file" name="favicon" accept="image/*">
      </div>
    </div>
  </section>

  <section class="card">
    <div class="card__head"><h2><x-icon name="image" size="18" /> Hero / Banner Atas</h2></div>
    <label class="field">
      <span>Judul hero <em>(tekan Enter untuk pindah baris)</em></span>
      <textarea name="hero_title" rows="2">{{ $val('hero_title') }}</textarea>
    </label>
    <label class="field">
      <span>Sub judul</span>
      <textarea name="hero_subtitle" rows="2">{{ $val('hero_subtitle') }}</textarea>
    </label>
    <div class="grid-2">
      <label class="field">
        <span>Teks penegas (bold kecil)</span>
        <input type="text" name="hero_badge" value="{{ $val('hero_badge') }}">
      </label>
      <label class="field">
        <span>Kegelapan overlay: <output id="overlayOut">{{ $val('hero_overlay', '65') }}</output>%</span>
        <input type="range" name="hero_overlay" min="0" max="90" step="5"
               value="{{ $val('hero_overlay', '65') }}" oninput="overlayOut.value = this.value">
      </label>
    </div>
    <div class="field">
      <span>Gambar latar hero <em>(disarankan 1920&times;600 px)</em></span>
      @if ($heroImg)
        <div class="preview"><img src="{{ $heroImg }}" alt="Hero"></div>
        <label class="check"><input type="checkbox" name="remove_hero" value="1"> Hapus gambar hero</label>
      @else
        <p class="muted">Belum ada gambar &mdash; hero memakai gradasi bawaan.</p>
      @endif
      <input type="file" name="hero_image" accept="image/*">
    </div>
  </section>

  <section class="card">
    <div class="card__head"><h2><x-icon name="sliders" size="18" /> Tampilan Daftar Aplikasi</h2></div>
    <div class="grid-2">
      <label class="field">
        <span>Label seksi <em>(teks kecil berwarna)</em></span>
        <input type="text" name="section_label" value="{{ $val('section_label') }}">
      </label>
      <label class="field">
        <span>Judul seksi <em>(kosongkan untuk menyembunyikan)</em></span>
        <input type="text" name="section_title" value="{{ $val('section_title') }}">
      </label>
    </div>
    <label class="field">
      <span>Keterangan seksi</span>
      <textarea name="section_subtitle" rows="2">{{ $val('section_subtitle') }}</textarea>
    </label>
    <div class="grid-2">
      <label class="field">
        <span>Warna label seksi</span>
        <input type="color" name="accent_color" value="{{ $val('accent_color', '#e11d48') }}">
      </label>
      <label class="field">
        <span>Warna utama <em>(aksen tautan &amp; inisial logo)</em></span>
        <input type="color" name="primary_color" value="{{ $val('primary_color', '#4f46e5') }}">
      </label>
      <div class="field">
        <span>Kotak pencarian</span>
        <label class="check">
          <input type="checkbox" name="show_search" value="1" @checked(setting('show_search', '1') === '1')>
          Tampilkan pencarian bila aplikasi lebih dari 3
        </label>
      </div>
    </div>
  </section>

  <section class="card">
    <div class="card__head"><h2><x-icon name="mail" size="18" /> Footer &amp; Kontak</h2></div>
    <label class="field">
      <span>Teks footer</span>
      <input type="text" name="footer_text" value="{{ $val('footer_text') }}">
    </label>
    <div class="grid-2">
      <label class="field">
        <span>Email</span>
        <input type="email" name="contact_email" value="{{ $val('contact_email') }}">
      </label>
      <label class="field">
        <span>Telepon</span>
        <input type="text" name="contact_phone" value="{{ $val('contact_phone') }}">
      </label>
    </div>
    <label class="field">
      <span>Alamat</span>
      <input type="text" name="contact_address" value="{{ $val('contact_address') }}">
    </label>
  </section>

  <div class="form-actions form-actions--sticky">
    <button class="btn btn--primary" type="submit"><x-icon name="check" size="16" /> Simpan Pengaturan</button>
    <a class="btn" href="{{ route('home') }}" target="_blank"><x-icon name="external" size="16" /> Pratinjau</a>
  </div>
</form>
@endsection
