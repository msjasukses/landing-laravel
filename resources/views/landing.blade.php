@php
    $siteName = setting('site_name', config('app.name'));
    $heroImg = upload_url(setting('hero_image'));
    $favicon = upload_url(setting('favicon') ?: setting('site_logo'));
    $overlay = max(0, min(90, (int) setting('hero_overlay', '65'))) / 100;
    $showSearch = setting('show_search', '1') === '1' && $totalApps > 3;
    $showChips = $groups->count() > 1;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $siteName }}</title>
<meta name="description" content="{{ setting('meta_description') }}">
<meta name="theme-color" content="#0b1020">
@if ($favicon)<link rel="icon" href="{{ $favicon }}">@endif
<link rel="preload" href="{{ asset('fonts/plus-jakarta-sans/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ asset_v('css/site.css') }}">
<style>
  :root {
    --accent: {{ setting('accent_color', '#e11d48') }};
    --primary: {{ setting('primary_color', '#4f46e5') }};
    --hero-overlay: {{ $overlay }};
  }
</style>
<script>document.documentElement.classList.add('js');</script>
</head>
<body>

<header class="nav" data-nav>
  <div class="container nav__inner">
    <x-brand />
    <nav class="nav__links" aria-label="Navigasi utama">
      <a href="#aplikasi" class="nav__link">Aplikasi</a>
      @if ($downloads->isNotEmpty())
        <a href="#unduhan" class="nav__link">Unduhan</a>
      @endif
      <a href="#kontak" class="nav__link">Kontak</a>
      <a href="{{ route('siswa.dashboard') }}" class="nav__link">Portal Siswa</a>
      <a href="{{ route('admin.dashboard') }}" class="btn btn--glass btn--sm">
        <x-icon name="lock" size="15" /><span>Masuk</span>
      </a>
    </nav>
  </div>
</header>

<section id="top" @class(['hero', 'hero--image' => $heroImg])
         @if ($heroImg) style="background-image:url('{{ $heroImg }}')" @endif>
  <div class="hero__bg" aria-hidden="true">
    @if ($heroImg)
      <div class="hero__scrim"></div>
    @else
      <span class="blob blob--1"></span>
      <span class="blob blob--2"></span>
      <span class="blob blob--3"></span>
    @endif
    <div class="hero__grid"></div>
  </div>

  <div @class(['container', 'hero__inner', 'hero__inner--solo' => $showcaseApps->isEmpty()])>
    <div class="hero__copy">
      @if (setting('hero_badge'))
        <p class="hero__badge"><span class="pulse"></span>{{ setting('hero_badge') }}</p>
      @endif
      <h1 class="hero__title">{!! nl2br(e(setting('hero_title', $siteName))) !!}</h1>
      @if (setting('hero_subtitle'))
        <p class="hero__subtitle">{{ setting('hero_subtitle') }}</p>
      @endif

      <div class="hero__actions">
        <a href="#aplikasi" class="btn btn--primary">
          Jelajahi Aplikasi <x-icon name="arrow-down" size="17" />
        </a>
        @if (setting('contact_email'))
          <a href="mailto:{{ setting('contact_email') }}" class="btn btn--ghost">
            <x-icon name="mail" size="17" /> Hubungi Kami
          </a>
        @endif
      </div>

      @if ($totalApps > 0)
        <dl class="hero__stats">
          <div><dt>Aplikasi</dt><dd>{{ $totalApps }}</dd></div>
          <div><dt>Grup layanan</dt><dd>{{ $groups->count() }}</dd></div>
          @if ($features->isNotEmpty())
            <div><dt>Fitur unggulan</dt><dd>{{ $features->count() }}</dd></div>
          @endif
        </dl>
      @endif
    </div>

    @if ($showcaseApps->isNotEmpty())
      <div class="hero__visual" aria-hidden="true">
        <div class="showcase">
          <div class="showcase__bar">
            <i></i><i></i><i></i>
            <span>{{ $siteName }}</span>
          </div>
          <div class="showcase__grid">
            @foreach ($showcaseApps as $app)
              <div class="showcase__tile tone-{{ $app->color }}" style="--i: {{ $loop->index }}">
                <span class="showcase__icon"><x-icon :name="$app->icon" size="20" /></span>
                <span class="showcase__name">{{ $app->name }}</span>
              </div>
            @endforeach
          </div>
        </div>

        @if ($features->isNotEmpty())
          <div class="float-chip float-chip--a">
            <span class="float-chip__icon"><x-icon :name="$features->first()->icon" size="16" /></span>
            <span><strong>{{ $features->first()->title }}</strong><small>{{ $features->first()->subtitle }}</small></span>
          </div>
        @endif
        <div class="float-chip float-chip--b">
          <span class="float-chip__icon float-chip__icon--ok"><x-icon name="check" size="16" /></span>
          <span><strong>{{ $totalApps }} aplikasi</strong><small>siap digunakan</small></span>
        </div>
      </div>
    @endif
  </div>
</section>

@if ($features->isNotEmpty())
<section class="features container" aria-label="Keunggulan">
  <div class="features__card">
    @foreach ($features as $feature)
      <div class="feature" data-reveal style="--d: {{ $loop->index }}">
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

<main id="aplikasi" class="main">
  <div class="container">
    <header class="section-head" data-reveal>
      <p class="section-label">{{ setting('section_label', 'AVAILABLE APPS') }}</p>
      @if (setting('section_title'))
        <h2 class="section-title">{{ setting('section_title') }}</h2>
      @endif
      @if (setting('section_subtitle'))
        <p class="section-sub">{{ setting('section_subtitle') }}</p>
      @endif
    </header>

    @if ($showSearch || $showChips)
      <div class="toolbar" data-reveal>
        @if ($showSearch)
          <label class="search">
            <x-icon name="search" size="19" />
            <input type="search" id="appSearch" placeholder="Cari aplikasi&hellip;" autocomplete="off"
                   aria-label="Cari aplikasi">
            <kbd class="search__key" title="Tekan / untuk mencari">/</kbd>
          </label>
        @endif

        @if ($showChips)
          <div class="chips" role="group" aria-label="Filter grup">
            <button type="button" class="chip is-active" data-filter="" aria-pressed="true">
              Semua <span class="chip__count">{{ $totalApps }}</span>
            </button>
            @foreach ($groups as $group)
              <button type="button" class="chip" data-filter="{{ $group->id }}" aria-pressed="false">
                {{ $group->name }} <span class="chip__count">{{ $group->applications->count() }}</span>
              </button>
            @endforeach
          </div>
        @endif

        <p class="toolbar__count" data-count aria-live="polite"></p>
      </div>
    @endif

    @if ($groups->isEmpty())
      <div class="empty">
        <span class="empty__icon"><x-icon name="grid" size="26" /></span>
        <p>Belum ada aplikasi yang ditampilkan. Tambahkan lewat
          <a href="{{ route('admin.dashboard') }}">panel pengaturan</a>.</p>
      </div>
    @endif

    @foreach ($groups as $group)
      <section class="group" id="grup-{{ $group->id }}" data-group="{{ $group->id }}">
        <div class="group__head" data-reveal>
          <div class="group__logo">
            @if ($group->logo_url)
              <img src="{{ $group->logo_url }}" alt="Logo {{ $group->name }}">
            @else
              <span>{{ $group->initials }}</span>
            @endif
          </div>
          <div class="group__meta">
            <h3 class="group__name">{{ $group->name }}</h3>
            @if ($group->tagline)
              <p class="group__tagline">{{ $group->tagline }}</p>
            @endif
          </div>
          <span class="group__count">{{ $group->applications->count() }} aplikasi</span>
        </div>

        @if ($group->applications->isEmpty())
          <p class="group__empty">Belum ada aplikasi pada grup ini.</p>
        @else
          <div class="apps">
            @foreach ($group->applications as $app)
              <a class="app-card tone-{{ $app->color }}" href="{{ $app->url ?: '#' }}"
                 @if ($app->open_in_new_tab) target="_blank" rel="noopener" @endif
                 data-name="{{ mb_strtolower($app->name.' '.$app->description) }}"
                 data-reveal style="--d: {{ $loop->index }}">
                <span class="app-card__top">
                  <span class="app-card__icon"><x-icon :name="$app->icon" size="22" /></span>
                  @if ($app->open_in_new_tab)
                    <span class="app-card__ext" title="Dibuka di tab baru"><x-icon name="external" size="15" /></span>
                  @endif
                </span>
                <span class="app-card__name">{{ $app->name }}</span>
                @if ($app->description)
                  <span class="app-card__desc">{{ $app->description }}</span>
                @endif
                <span class="app-card__cta">Buka aplikasi <x-icon name="arrow-right" size="15" /></span>
              </a>
            @endforeach
          </div>
        @endif
      </section>
    @endforeach

    <div class="empty" data-no-results hidden>
      <span class="empty__icon"><x-icon name="search" size="26" /></span>
      <p><strong>Aplikasi tidak ditemukan.</strong><br>Coba kata kunci lain atau pilih grup lainnya.</p>
      <button type="button" class="btn btn--soft btn--sm" data-reset>Tampilkan semua aplikasi</button>
    </div>
  </div>
</main>

@if ($downloads->isNotEmpty())
<section id="unduhan" class="downloads">
  <div class="container">
    <header class="section-head" data-reveal>
      @if (setting('downloads_label'))
        <p class="section-label">{{ setting('downloads_label') }}</p>
      @endif
      @if (setting('downloads_title'))
        <h2 class="section-title">{{ setting('downloads_title') }}</h2>
      @endif
      @if (setting('downloads_subtitle'))
        <p class="section-sub">{{ setting('downloads_subtitle') }}</p>
      @endif
    </header>

    <div class="dl-grid">
      @foreach ($downloads as $download)
        <article class="dl-card tone-{{ $download->color }}" data-reveal style="--d: {{ $loop->index }}">
          <div class="dl-card__head">
            <span class="dl-card__icon"><x-icon :name="$download->icon" size="24" /></span>
            <div class="dl-card__title">
              <h3 class="dl-card__name">{{ $download->name }}</h3>
              <p class="dl-card__tags">
                <span class="dl-tag">{{ $download->platform_label }}</span>
                @if ($download->version_label)<span class="dl-tag dl-tag--soft">{{ $download->version_label }}</span>@endif
              </p>
            </div>
          </div>

          @if ($download->description)
            <p class="dl-card__desc">{{ $download->description }}</p>
          @endif

          <div class="dl-card__foot">
            <p class="dl-card__info">
              @if ($download->hasStoredFile())
                {{ $download->extension }} &middot; {{ $download->file_size_label }}
              @else
                Tautan resmi
              @endif
              @if ($download->download_count > 0)
                <br>{{ number_format($download->download_count, 0, ',', '.') }}&times; diunduh
              @endif
            </p>
            @if ($download->hasStoredFile())
              <a class="btn btn--sm dl-card__btn" href="{{ route('downloads.file', $download) }}" download>
                <x-icon name="download" size="16" /> Unduh
              </a>
            @else
              <a class="btn btn--sm dl-card__btn" href="{{ route('downloads.file', $download) }}" target="_blank" rel="noopener">
                <x-icon name="external" size="16" /> Buka
              </a>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif

<footer id="kontak" class="footer">
  <div class="container footer__grid">
    <div class="footer__brand">
      <x-brand class="brand--light" />
      @if (setting('meta_description'))
        <p class="footer__about">{{ setting('meta_description') }}</p>
      @endif
    </div>

    @if (setting('contact_email') || setting('contact_phone') || setting('contact_address'))
      <div>
        <h4 class="footer__title">Kontak</h4>
        <ul class="footer__list">
          @if (setting('contact_email'))
            <li><x-icon name="mail" size="16" /><a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></li>
          @endif
          @if (setting('contact_phone'))
            <li><x-icon name="phone" size="16" /><span>{{ setting('contact_phone') }}</span></li>
          @endif
          @if (setting('contact_address'))
            <li><x-icon name="map-pin" size="16" /><span>{{ setting('contact_address') }}</span></li>
          @endif
        </ul>
      </div>
    @endif

    <div>
      <h4 class="footer__title">Tautan</h4>
      <ul class="footer__list">
        @foreach ($groups as $group)
          <li><x-icon name="layers" size="16" /><a href="#grup-{{ $group->id }}">{{ $group->name }}</a></li>
        @endforeach
        @if ($downloads->isNotEmpty())
          <li><x-icon name="download" size="16" /><a href="#unduhan">Unduhan</a></li>
        @endif
        <li><x-icon name="settings" size="16" /><a href="{{ route('admin.dashboard') }}">Pengaturan</a></li>
      </ul>
    </div>
  </div>

  <div class="container footer__bottom">
    <p>{{ setting('footer_text') }}</p>
  </div>
</footer>

<button type="button" class="to-top" data-to-top aria-label="Kembali ke atas" hidden>
  <x-icon name="arrow-up" size="18" />
</button>

<script src="{{ asset_v('js/site.js') }}"></script>
</body>
</html>
