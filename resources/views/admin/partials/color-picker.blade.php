{{-- Pemilih warna ikon kartu aplikasi. Parameter: $name, $selected --}}
<div class="picker picker--color">
  @foreach (\App\Support\Icon::COLORS as $color)
    <label class="picker__opt" title="{{ $color }}">
      <input type="radio" name="{{ $name }}" value="{{ $color }}" @checked($color === $selected)>
      <span class="ic-{{ $color }}"><x-icon name="check" size="16" /></span>
    </label>
  @endforeach
</div>
