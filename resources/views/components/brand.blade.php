{{-- Logo + nama situs (navbar & footer landing page). Tanpa logo: inisial nama situs. --}}
@php
    $siteName = setting('site_name', config('app.name'));
    $logo = upload_url(setting('site_logo'));
    $showName = ! $logo || setting('show_site_name', '1') === '1';
@endphp
<a href="#top" {{ $attributes->merge(['class' => 'brand']) }}>
  @if ($logo)
    <span class="brand__logo"><img src="{{ $logo }}" alt="{{ $showName ? '' : $siteName }}"></span>
  @else
    <span class="brand__mark">{{ initials($siteName) }}</span>
  @endif
  @if ($showName)
    <span class="brand__name">{{ $siteName }}</span>
  @endif
</a>
