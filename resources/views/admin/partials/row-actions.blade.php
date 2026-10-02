{{--
  Tombol naik/turun, ubah, hapus.
  Parameter: $prefix (mis. 'admin.features'), $row, $confirm (teks konfirmasi hapus),
             $query (opsional, query string tambahan mis. ['group' => 2]).
--}}
@php($query = $query ?? [])
<div class="row-actions">
  @foreach (['up' => 'Naik', 'down' => 'Turun'] as $direction => $label)
    <form method="post" action="{{ route($prefix.'.move', [$row, $direction]) }}" class="inline">
      @csrf
      @method('PATCH')
      <button class="icon-btn" title="{{ $label }}"><x-icon :name="'arrow-'.$direction" size="15" /></button>
    </form>
  @endforeach
  <a class="icon-btn" title="Ubah" href="{{ route($prefix.'.edit', [$row, ...$query]) }}"><x-icon name="edit" size="15" /></a>
  <form method="post" action="{{ route($prefix.'.destroy', [$row, ...$query]) }}" class="inline" data-confirm="{{ $confirm }}">
    @csrf
    @method('DELETE')
    <button class="icon-btn icon-btn--danger" title="Hapus"><x-icon name="trash" size="15" /></button>
  </form>
</div>
