@extends('layouts.admin')

@section('title', 'Aplikasi')

@section('content')
@if ($groups->isEmpty())
  <div class="alert alert--error">
    <x-icon name="x" size="16" />
    <span>Belum ada grup aplikasi. Buat grup dulu di menu <a href="{{ route('admin.groups.index') }}">Grup Aplikasi</a>.</span>
  </div>
@else
@php($query = array_filter(['group' => $filter]))
<div class="grid-side">
  <section class="card">
    <div class="card__head">
      <h2>Daftar aplikasi</h2>
      <form method="get" action="{{ route('admin.apps.index') }}" class="inline">
        <select name="group" onchange="this.form.submit()" class="select select--sm">
          <option value="0">Semua grup</option>
          @foreach ($groups as $g)
            <option value="{{ $g->id }}" @selected($filter === $g->id)>{{ $g->name }}</option>
          @endforeach
        </select>
      </form>
    </div>

    @if ($rows->isEmpty())
      <p class="muted">Belum ada aplikasi pada tampilan ini.</p>
    @else
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th style="width:44px"></th><th>Aplikasi</th><th>Grup</th><th>URL</th><th>Status</th><th class="ta-r">Aksi</th></tr>
        </thead>
        <tbody>
        @foreach ($rows as $row)
          <tr>
            <td><span class="list__icon ic-{{ $row->color }}"><x-icon :name="$row->icon" size="16" /></span></td>
            <td>
              <strong>{{ $row->name }}</strong>
              @if ($row->description)<small class="muted db">{{ $row->description }}</small>@endif
            </td>
            <td class="muted">{{ $row->group->name }}</td>
            <td class="muted trunc">{{ $row->url ?: '#' }}</td>
            <td>@include('admin.partials.status-toggle', ['prefix' => 'admin.apps'])</td>
            <td class="ta-r">
              @include('admin.partials.row-actions', [
                  'prefix' => 'admin.apps',
                  'confirm' => 'Hapus aplikasi "'.$row->name.'"?',
                  'query' => $query,
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
    <div class="card__head"><h2>{{ $editing ? 'Ubah aplikasi' : 'Tambah aplikasi' }}</h2></div>
    <form method="post" class="stack"
          action="{{ $editing ? route('admin.apps.update', [$editing, ...$query]) : route('admin.apps.store', $query) }}">
      @csrf
      @if ($editing) @method('PUT') @endif

      <label class="field">
        <span>Grup</span>
        <select name="app_group_id" class="select" required>
          @foreach ($groups as $g)
            <option value="{{ $g->id }}" @selected((int) old('app_group_id', $editing?->app_group_id ?? $filter) === $g->id)>
              {{ $g->name }}
            </option>
          @endforeach
        </select>
      </label>
      <label class="field">
        <span>Nama aplikasi</span>
        <input type="text" name="name" required value="{{ old('name', $editing?->name) }}" placeholder="DATA CENTER">
      </label>
      <label class="field">
        <span>Deskripsi singkat</span>
        <input type="text" name="description" value="{{ old('description', $editing?->description) }}" placeholder="Pusat Data Sekolah">
      </label>
      <label class="field">
        <span>URL tujuan</span>
        <input type="text" name="url" value="{{ old('url', $editing?->url) }}" placeholder="https://datacenter.sekolah.sch.id">
      </label>
      <div class="field">
        <span>Ikon</span>
        @include('admin.partials.icon-picker', ['name' => 'icon', 'selected' => old('icon', $editing?->icon ?? 'grid')])
      </div>
      <div class="field">
        <span>Warna ikon</span>
        @include('admin.partials.color-picker', ['name' => 'color', 'selected' => old('color', $editing?->color ?? 'indigo')])
      </div>
      <label class="check">
        <input type="checkbox" name="open_in_new_tab" value="1" @checked(old('_token') ? old('open_in_new_tab') : ($editing?->open_in_new_tab ?? true))>
        Buka di tab baru
      </label>
      <label class="check">
        <input type="checkbox" name="is_active" value="1" @checked(old('_token') ? old('is_active') : ($editing?->is_active ?? true))>
        Tampilkan di landing page
      </label>

      <div class="form-actions">
        <button class="btn btn--primary" type="submit"><x-icon name="check" size="16" /> Simpan</button>
        @if ($editing)
          <a class="btn" href="{{ route('admin.apps.index', $query) }}">Batal</a>
        @endif
      </div>
    </form>
  </section>
</div>
@endif
@endsection
