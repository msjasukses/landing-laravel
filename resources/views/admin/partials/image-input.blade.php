{{--
  Input unggah gambar + pratinjau (gambar baru langsung tampil sebelum disimpan).
  Parameter: $name, $label, $current (URL gambar saat ini atau null), $remove (nama checkbox hapus),
             $hint (opsional, keterangan di label), $empty (opsional, teks bila belum ada gambar),
             $size (opsional: 'sm' | 'logo'; default lebar penuh).
--}}
@php($previewId = 'preview-'.$name)
<div class="field">
  <span>{{ $label }} @isset($hint)<em>({{ $hint }})</em>@endisset</span>

  <div @class(['preview', 'preview--'.($size ?? '') => isset($size)]) id="{{ $previewId }}" @if (! $current) hidden @endif>
    <img @if ($current) src="{{ $current }}" @endif alt="Pratinjau {{ strtolower($label) }}">
  </div>

  @if ($current)
    <label class="check"><input type="checkbox" name="{{ $remove }}" value="1"> Hapus gambar ini</label>
  @elseif (isset($empty))
    <p class="muted" data-preview-empty>{{ $empty }}</p>
  @endif

  <input type="file" name="{{ $name }}" accept="image/*" data-preview="#{{ $previewId }}">
</div>
