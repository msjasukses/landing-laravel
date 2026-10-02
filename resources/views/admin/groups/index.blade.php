@extends('layouts.admin')

@section('title', 'Grup Aplikasi')

@section('content')
<div class="grid-side">
  <section class="card">
    <div class="card__head">
      <h2>Daftar grup / instansi</h2>
      <span class="muted">{{ $rows->count() }} grup</span>
    </div>

    @if ($rows->isEmpty())
      <p class="muted">Belum ada grup. Grup adalah judul besar (mis. nama sekolah) di atas daftar aplikasi.</p>
    @else
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th style="width:52px">Logo</th><th>Nama</th><th>Tagline</th><th>Aplikasi</th><th>Status</th><th class="ta-r">Aksi</th></tr>
        </thead>
        <tbody>
        @foreach ($rows as $row)
          <tr>
            <td>
              <span class="thumb">
                @if ($row->logo_url)<img src="{{ $row->logo_url }}" alt="">@else{{ $row->initials }}@endif
              </span>
            </td>
            <td><strong>{{ $row->name }}</strong></td>
            <td class="muted">{{ $row->tagline }}</td>
            <td><span class="pill">{{ $row->applications_count }}</span></td>
            <td>@include('admin.partials.status-toggle', ['prefix' => 'admin.groups'])</td>
            <td class="ta-r">
              @include('admin.partials.row-actions', [
                  'prefix' => 'admin.groups',
                  'confirm' => 'Hapus grup "'.$row->name.'" beserta '.$row->applications_count.' aplikasi di dalamnya?',
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
    <div class="card__head"><h2>{{ $editing ? 'Ubah grup' : 'Tambah grup' }}</h2></div>
    <form method="post" enctype="multipart/form-data" class="stack"
          action="{{ $editing ? route('admin.groups.update', $editing) : route('admin.groups.store') }}">
      @csrf
      @if ($editing) @method('PUT') @endif

      <label class="field">
        <span>Nama grup / instansi</span>
        <input type="text" name="name" required value="{{ old('name', $editing?->name) }}" placeholder="DEMO">
      </label>
      <label class="field">
        <span>Tagline</span>
        <input type="text" name="tagline" value="{{ old('tagline', $editing?->tagline) }}"
               placeholder="Sistem Informasi Sekolah Terintegrasi">
      </label>
      <div class="field">
        <span>Logo</span>
        @if ($editing?->logo_url)
          <div class="preview preview--sm"><img src="{{ $editing->logo_url }}" alt="Logo"></div>
          <label class="check"><input type="checkbox" name="remove_logo" value="1"> Hapus logo</label>
        @endif
        <input type="file" name="logo" accept="image/*">
        <small class="muted">Kosongkan untuk memakai inisial nama grup.</small>
      </div>
      <label class="check">
        <input type="checkbox" name="is_active" value="1" @checked(old('_token') ? old('is_active') : ($editing?->is_active ?? true))>
        Tampilkan di landing page
      </label>

      <div class="form-actions">
        <button class="btn btn--primary" type="submit"><x-icon name="check" size="16" /> Simpan</button>
        @if ($editing)
          <a class="btn" href="{{ route('admin.groups.index') }}">Batal</a>
        @endif
      </div>
    </form>
  </section>
</div>
@endsection
