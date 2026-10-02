@php
    $heroImg = upload_url(setting('hero_image'));
    $favicon = upload_url(setting('favicon'));
    $overlay = max(0, min(90, (int) setting('hero_overlay', '65'))) / 100;
    $showSearch = setting('show_search', '1') === '1';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ setting('site_name', config('app.name')) }}</title>
<meta name="description" content="{{ setting('meta_description') }}">
@if ($favicon)<link rel="icon" href="{{ $favicon }}">@endif
<link rel="stylesheet" href="{{ asset_v('css/site.css') }}">
<style>
  :root {
    --accent: {{ setting('accent_color', '#e11d48') }};
    --primary: {{ setting('primary_color', '#4f46e5') }};
    --hero-overlay: {{ $overlay }};
  }
</style>
</head>
<body>

<header @class(['hero', 'hero--plain' => ! $heroImg])
        @if ($heroImg) style="background-image:url('{{ $heroImg }}')" @endif>
  <div class="hero__scrim"></div>
  <div class="hero__inner container">
    <h1 class="hero__title">{!! nl2br(e(setting('hero_title', setting('site_name')))) !!}</h1>
    @if (setting('hero_subtitle'))
      <p class="hero__subtitle">{{ setting('hero_subtitle') }}</p>
    @endif
    @if (setting('hero_badge'))
      <p class="hero__badge">{{ setting('hero_badge') }}</p>
    @endif
  </div>
</header>

@if ($features->isNotEmpty())
<section class="features container" aria-label="Keunggulan">
  <div class="features__card">
    @foreach ($features as $feature)
      <div class="feature">
        <span class="feature__icon"><x-icon :name="$feature->icon" size="20" /></span>
        <div>
          <p class="feature__title">{{ $feature->title }}</p>
          @if ($feature->subtitle)<p class="feature__sub">{{ $feature->subtitle }}</p>@endif
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

<main class="container main">
  <p class="section-label">{{ setting('section_label', 'AVAILABLE APPS') }}</p>

  @if ($showSearch && $totalApps > 3)
    <div class="search">
      <x-icon name="search" size="18" />
      <input type="search" id="appSearch" placeholder="Cari aplikasi&hellip;" autocomplete="off"
             aria-label="Cari aplikasi">
    </div>
  @endif

  @if ($groups->isEmpty())
    <p class="empty">Belum ada aplikasi yang ditampilkan. Tambahkan lewat
      <a href="{{ route('admin.dashboard') }}">panel pengaturan</a>.</p>
  @endif

  @foreach ($groups as $group)
    <section class="group" data-group>
      <div class="group__head">
        <div class="group__logo">
          @if ($group->logo_url)
            <img src="{{ $group->logo_url }}" alt="Logo {{ $group->name }}">
          @else
            <span class="group__logo-text">{{ $group->initials }}</span>
          @endif
        </div>
        <h2 class="group__name">{{ $group->name }}</h2>
        @if ($group->tagline)
          <p class="group__tagline">{{ $group->tagline }}</p>
        @endif
      </div>

      @if ($group->applications->isEmpty())
        <p class="empty empty--sm">Belum ada aplikasi pada grup ini.</p>
      @else
      <div class="apps">
        @foreach ($group->applications as $app)
          <a class="app-card" href="{{ $app->url ?: '#' }}"
             @if ($app->open_in_new_tab) target="_blank" rel="noopener" @endif
             data-name="{{ mb_strtolower($app->name.' '.$app->description) }}">
            <span class="app-card__icon ic-{{ $app->color }}"><x-icon :name="$app->icon" size="20" /></span>
            <span class="app-card__name">{{ $app->name }}</span>
            @if ($app->description)
              <span class="app-card__desc">{{ $app->description }}</span>
            @endif
          </a>
        @endforeach
      </div>
      @endif
      <p class="apps__none" hidden>Aplikasi tidak ditemukan.</p>
    </section>
  @endforeach
</main>

<footer class="footer">
  <div class="container footer__inner">
    <p class="footer__text">{{ setting('footer_text') }}</p>
    <ul class="footer__meta">
      @if (setting('contact_email'))
        <li><x-icon name="mail" size="15" /><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
      @endif
      @if (setting('contact_phone'))
        <li><x-icon name="phone" size="15" /><span>{{ setting('contact_phone') }}</span></li>
      @endif
      @if (setting('contact_address'))
        <li><x-icon name="map-pin" size="15" /><span>{{ setting('contact_address') }}</span></li>
      @endif
      <li><x-icon name="settings" size="15" /><a href="{{ route('admin.dashboard') }}">Pengaturan</a></li>
    </ul>
  </div>
</footer>

<script src="{{ asset_v('js/site.js') }}"></script>
</body>
</html>
