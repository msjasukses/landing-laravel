{{-- Grid pemilih ikon. Parameter: $name, $selected --}}
<div class="picker picker--icon">
  @foreach (\App\Support\Icon::names() as $key)
    <label class="picker__opt" title="{{ $key }}">
      <input type="radio" name="{{ $name }}" value="{{ $key }}" @checked($key === $selected)>
      <span><x-icon :name="$key" size="18" /></span>
    </label>
  @endforeach
</div>
