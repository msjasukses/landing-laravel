@extends('layouts.admin')

@section('title', 'Fitur Header')

@section('content')
<div class="grid-side">
  <section class="card">
    <div class="card__head">
      <h2>Daftar fitur</h2>
      <span class="muted">{{ $rows->count() }} item</span>
    </div>

    @if ($rows->isEmpty())
      <p class="muted">Belum ada fitur. Tambahkan lewat form di samping.</p>
    @else
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th style="width:44px"></th><th>Judul</th><th>Keterangan</th><th>Status</th><th class="ta-r">Aksi</th></tr>
        </thead>
        <tbody>
        @foreach ($rows as $row)
          <tr>
            <td><span class="list__icon ic-slate"><x-icon :name="$row->icon" size="16" /></span></td>
            <td><strong>{{ $row->title }}</strong></td>
            <td class="muted">{{ $row->subtitle }}</td>
            <td>@include('admin.partials.status-toggle', ['prefix' => 'admin.features'])</td>
            <td class="ta-r">
              @include('admin.partials.row-actions', [
                  'prefix' => 'admin.features',
                  'confirm' => 'Hapus fitur "'.$row->title.'"?',
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
    <div class="card__head"><h2>{{ $editing ? 'Ubah fitur' : 'Tambah fitur' }}</h2></div>
    <form method="post" class="stack"
          action="{{ $editing ? route('admin.features.update', $editing) : route('admin.features.store') }}">
      @csrf
      @if ($editing) @method('PUT') @endif

      <label class="field">
        <span>Judul</span>
        <input type="text" name="title" required value="{{ old('title', $editing?->title) }}" placeholder="Login Protect">
      </label>
      <label class="field">
        <span>Keterangan</span>
        <input type="text" name="subtitle" value="{{ old('subtitle', $editing?->subtitle) }}" placeholder="Keamanan Login akun">
      </label>
      <div class="field">
        <span>Ikon</span>
        @include('admin.partials.icon-picker', ['name' => 'icon', 'selected' => old('icon', $editing?->icon ?? 'lock')])
      </div>
      <label class="check">
        <input type="checkbox" name="is_active" value="1" @checked(old('_token') ? old('is_active') : ($editing?->is_active ?? true))>
        Tampilkan di landing page
      </label>

      <div class="form-actions">
        <button class="btn btn--primary" type="submit"><x-icon name="check" size="16" /> Simpan</button>
        @if ($editing)
          <a class="btn" href="{{ route('admin.features.index') }}">Batal</a>
        @endif
      </div>
    </form>
  </section>
</div>
@endsection
