@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="stat-grid">
  <div class="stat">
    <span class="stat__icon ic-indigo"><x-icon name="grid" size="20" /></span>
    <div>
      <p class="stat__num">{{ $stats['apps'] }}</p>
      <p class="stat__label">Aplikasi terdaftar</p>
      <p class="stat__note">{{ $stats['apps_active'] }} tampil di landing page</p>
    </div>
  </div>
  <div class="stat">
    <span class="stat__icon ic-violet"><x-icon name="layers" size="20" /></span>
    <div>
      <p class="stat__num">{{ $stats['groups'] }}</p>
      <p class="stat__label">Grup / instansi</p>
      <p class="stat__note">{{ $stats['groups_active'] }} aktif</p>
    </div>
  </div>
  <div class="stat">
    <span class="stat__icon ic-green"><x-icon name="shield" size="20" /></span>
    <div>
      <p class="stat__num">{{ $stats['features'] }}</p>
      <p class="stat__label">Fitur header aktif</p>
      <p class="stat__note">Tampil pada bar putih di bawah hero</p>
    </div>
  </div>
</div>

<div class="grid-2">
  <section class="card">
    <div class="card__head"><h2>Aplikasi terbaru</h2>
      <a class="btn btn--sm" href="{{ route('admin.apps.index') }}"><x-icon name="plus" size="15" /> Kelola</a>
    </div>
    @if ($recent->isEmpty())
      <p class="muted">Belum ada aplikasi.</p>
    @else
      <ul class="list">
        @foreach ($recent as $app)
          <li>
            <span class="list__icon ic-{{ $app->color }}"><x-icon :name="$app->icon" size="16" /></span>
            <div class="list__body">
              <strong>{{ $app->name }}</strong>
              <small>{{ $app->group->name }}@if ($app->description) &middot; {{ $app->description }}@endif</small>
            </div>
            <span @class(['badge', 'badge--on' => $app->is_active, 'badge--off' => ! $app->is_active])>
              {{ $app->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </li>
        @endforeach
      </ul>
    @endif
  </section>

  <section class="card">
    <div class="card__head"><h2>Pintasan</h2></div>
    <div class="shortcuts">
      <a class="shortcut" href="{{ route('admin.settings.edit') }}">
        <span class="ic-indigo"><x-icon name="sliders" size="18" /></span>
        <div><strong>Pengaturan Umum</strong><small>Judul, hero, warna, kontak</small></div>
      </a>
      <a class="shortcut" href="{{ route('admin.apps.index') }}">
        <span class="ic-sky"><x-icon name="grid" size="18" /></span>
        <div><strong>Aplikasi</strong><small>Tambah / ubah menu aplikasi</small></div>
      </a>
      <a class="shortcut" href="{{ route('admin.groups.index') }}">
        <span class="ic-violet"><x-icon name="layers" size="18" /></span>
        <div><strong>Grup Aplikasi</strong><small>Instansi &amp; logo</small></div>
      </a>
      <a class="shortcut" href="{{ route('home') }}" target="_blank">
        <span class="ic-green"><x-icon name="external" size="18" /></span>
        <div><strong>Lihat Landing Page</strong><small>Buka tampilan publik</small></div>
      </a>
    </div>
  </section>
</div>
@endsection
