@extends('layouts.admin')

@section('title', 'Unduhan')

@use('App\Models\Download')
@use('Illuminate\Support\Number')

@php
    $platform = old('platform', $editing?->platform ?? 'android');
    $accept = collect(Download::EXTENSIONS)->map(fn ($ext) => '.'.$ext)->implode(',');
@endphp

@section('content')
<div class="grid-side">
  <section class="card">
    <div class="card__head">
      <h2>Daftar file unduhan</h2>
      <span class="muted">{{ $rows->count() }} file &middot; {{ $rows->sum('download_count') }}&times; diunduh</span>
    </div>

    @if ($rows->isEmpty())
      <p class="muted">Belum ada file unduhan. Tambahkan APK atau installer lewat form di samping;
        seksi <strong>Unduhan</strong> otomatis muncul di landing page.</p>
    @else
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th style="width:44px"></th><th>Nama</th><th>Platform</th><th>File</th><th>Diunduh</th><th>Status</th><th class="ta-r">Aksi</th></tr>
        </thead>
        <tbody>
        @foreach ($rows as $row)
          <tr>
            <td><span class="list__icon ic-{{ $row->color }}"><x-icon :name="$row->icon" size="16" /></span></td>
            <td>
              <strong>{{ $row->name }}</strong>
              @if ($row->version_label)<span class="pill">{{ $row->version_label }}</span>@endif
              @if ($row->description)<small class="muted db">{{ $row->description }}</small>@endif
            </td>
            <td class="muted">{{ $row->platform_label }}</td>
            <td class="muted">
              @if ($row->hasStoredFile())
                <span class="db trunc" title="{{ $row->file_name }}">{{ $row->file_name }}</span>
                <small>{{ $row->file_size_label }}</small>
              @elseif ($row->external_url)
                <span class="db trunc" title="{{ $row->external_url }}"><x-icon name="external" size="13" /> Tautan luar</span>
              @else
                <span class="badge badge--off">Belum ada file</span>
              @endif
            </td>
            <td><span class="pill">{{ $row->download_count }}</span></td>
            <td>@include('admin.partials.status-toggle', ['prefix' => 'admin.downloads'])</td>
            <td class="ta-r">
              @include('admin.partials.row-actions', [
                  'prefix' => 'admin.downloads',
                  'confirm' => 'Hapus "'.$row->name.'" beserta file-nya?',
              ])
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </section>

  <section class="card">
    <div class="card__head"><h2>{{ $editing ? 'Ubah file unduhan' : 'Tambah file unduhan' }}</h2></div>
    <form method="post" enctype="multipart/form-data" class="stack" data-uploading
          action="{{ $editing ? route('admin.downloads.update', $editing) : route('admin.downloads.store') }}">
      @csrf
      @if ($editing) @method('PUT') @endif

      <label class="field">
        <span>Nama aplikasi / file</span>
        <input type="text" name="name" required value="{{ old('name', $editing?->name) }}" placeholder="EXAMBRO">
      </label>
      <div class="grid-2">
        <label class="field">
          <span>Platform</span>
          <select name="platform" class="select" required>
            @foreach (Download::PLATFORMS as $key => $label)
              <option value="{{ $key }}" @selected($platform === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </label>
        <label class="field">
          <span>Versi <em>(opsional)</em></span>
          <input type="text" name="version" value="{{ old('version', $editing?->version) }}" placeholder="2.1.0">
        </label>
      </div>
      <label class="field">
        <span>Deskripsi singkat</span>
        <input type="text" name="description" value="{{ old('description', $editing?->description) }}"
               placeholder="Browser ujian untuk HP Android">
      </label>

      <div class="field">
        <span>File <em>({{ strtoupper(implode(', ', Download::EXTENSIONS)) }}; maks. {{ Number::fileSize($maxBytes) }})</em></span>
        @if ($editing?->hasStoredFile())
          <div class="file-current">
            <x-icon name="file-text" size="18" />
            <div><strong>{{ $editing->file_name }}</strong><small>{{ $editing->file_size_label }} &middot; {{ $editing->download_count }}&times; diunduh</small></div>
          </div>
          <label class="check"><input type="checkbox" name="remove_file" value="1"> Hapus file ini</label>
          <small class="muted">Pilih file baru untuk mengganti file di atas.</small>
        @endif
        <input type="file" name="file" accept="{{ $accept }}" data-max-bytes="{{ $maxBytes }}"
               data-max-label="{{ Number::fileSize($maxBytes) }}">
      </div>

      <label class="field">
        <span>Atau tautan unduhan luar <em>(mis. Play Store; dipakai bila tidak ada file)</em></span>
        <input type="url" name="external_url" value="{{ old('external_url', $editing?->external_url) }}"
               placeholder="https://play.google.com/store/apps/details?id=...">
      </label>

      <div class="field">
        <span>Ikon</span>
        @include('admin.partials.icon-picker', ['name' => 'icon', 'selected' => old('icon', $editing?->icon ?? 'smartphone')])
      </div>
      <div class="field">
        <span>Warna ikon</span>
        @include('admin.partials.color-picker', ['name' => 'color', 'selected' => old('color', $editing?->color ?? 'green')])
      </div>
      <label class="check">
        <input type="checkbox" name="is_active" value="1" @checked(old('_token') ? old('is_active') : ($editing?->is_active ?? true))>
        Tampilkan di landing page
      </label>

      <div class="form-actions">
        <button class="btn btn--primary" type="submit"><x-icon name="check" size="16" /> <span>Simpan</span></button>
        @if ($editing)
          <a class="btn" href="{{ route('admin.downloads.index') }}">Batal</a>
        @endif
      </div>
    </form>
  </section>
</div>
@endsection
