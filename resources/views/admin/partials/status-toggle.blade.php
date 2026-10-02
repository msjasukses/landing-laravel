{{-- Tombol Aktif/Nonaktif. Parameter: $prefix (mis. 'admin.features'), $row --}}
<form method="post" action="{{ route($prefix.'.toggle', $row) }}" class="inline">
  @csrf
  @method('PATCH')
  <button type="submit" @class(['badge', 'badge--on' => $row->is_active, 'badge--off' => ! $row->is_active])>
    {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
  </button>
</form>
