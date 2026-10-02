@php
    $menu = [
        ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
        ['route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'label' => 'Pengaturan Umum', 'icon' => 'sliders'],
        ['route' => 'admin.features.index', 'active' => 'admin.features.*', 'label' => 'Fitur Header', 'icon' => 'shield'],
        ['route' => 'admin.groups.index', 'active' => 'admin.groups.*', 'label' => 'Grup Aplikasi', 'icon' => 'layers'],
        ['route' => 'admin.apps.index', 'active' => 'admin.apps.*', 'label' => 'Aplikasi', 'icon' => 'grid'],
        ['route' => 'admin.account.edit', 'active' => 'admin.account.*', 'label' => 'Akun Admin', 'icon' => 'user'],
    ];
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title') &middot; Panel Pengaturan</title>
<link rel="stylesheet" href="{{ asset_v('css/admin.css') }}">
</head>
<body>
<input type="checkbox" id="navToggle" class="nav-toggle" hidden>

<aside class="sidebar">
  <div class="sidebar__brand">
    <span class="sidebar__logo"><x-icon name="layers" size="20" /></span>
    <div>
      <strong>Panel Pengaturan</strong>
      <small>{{ setting('site_name', config('app.name')) }}</small>
    </div>
  </div>

  <nav class="sidebar__nav">
    @foreach ($menu as $item)
      <a href="{{ route($item['route']) }}" @class(['nav-item', 'is-active' => request()->routeIs($item['active'])])>
        <x-icon :name="$item['icon']" size="18" /><span>{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>

  <div class="sidebar__foot">
    <a href="{{ route('home') }}" target="_blank" class="nav-item">
      <x-icon name="external" size="18" /><span>Lihat Landing Page</span>
    </a>
    <form method="post" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="nav-item nav-item--danger">
        <x-icon name="logout" size="18" /><span>Keluar</span>
      </button>
    </form>
  </div>
</aside>

<div class="shell">
  <header class="topbar">
    <label for="navToggle" class="topbar__burger" aria-label="Menu"><x-icon name="menu" size="20" /></label>
    <h1 class="topbar__title">@yield('title')</h1>
    <div class="topbar__user">
      <span class="avatar">{{ initials($user->name) }}</span>
      <div>
        <strong>{{ $user->name }}</strong>
        <small>{{ '@'.$user->username }}</small>
      </div>
    </div>
  </header>

  <main class="content">
    @include('admin.partials.alerts')
    @yield('content')
  </main>
</div>

<label for="navToggle" class="nav-backdrop"></label>
<script src="{{ asset_v('js/admin.js') }}"></script>
</body>
</html>
