@extends('layouts.admin')

@section('title', 'Akun Admin')

@section('content')
<div class="grid-side">
  <section class="card">
    <div class="card__head"><h2><x-icon name="user" size="18" /> Data akun</h2></div>
    <form method="post" action="{{ route('admin.account.update') }}" class="stack">
      @csrf
      @method('PUT')

      <label class="field">
        <span>Nama tampilan</span>
        <input type="text" name="name" required value="{{ old('name', $user->name) }}">
      </label>
      <label class="field">
        <span>Username</span>
        <input type="text" name="username" required value="{{ old('username', $user->username) }}">
      </label>

      <hr class="sep">

      <label class="field">
        <span>Password saat ini <em>(wajib untuk menyimpan)</em></span>
        <input type="password" name="current_password" required autocomplete="current-password">
      </label>
      <div class="grid-2">
        <label class="field">
          <span>Password baru <em>(kosongkan bila tidak diganti)</em></span>
          <input type="password" name="new_password" autocomplete="new-password">
        </label>
        <label class="field">
          <span>Ulangi password baru</span>
          <input type="password" name="new_password_confirmation" autocomplete="new-password">
        </label>
      </div>

      <div class="form-actions">
        <button class="btn btn--primary" type="submit"><x-icon name="check" size="16" /> Simpan Perubahan</button>
      </div>
    </form>
  </section>

  <section class="card">
    <div class="card__head"><h2><x-icon name="shield" size="18" /> Catatan keamanan</h2></div>
    <ul class="notes">
      <li>Ganti password bawaan <code>admin123</code> segera setelah instalasi.</li>
      <li>Data tersimpan di database MySQL <code>{{ config('database.connections.mysql.database') }}</code> &mdash; cadangkan berkala.</li>
      <li>Gambar unggahan tersimpan di folder <code>public/uploads/</code>.</li>
      <li>Saat dipasang di hosting, set <code>APP_DEBUG=false</code> pada file <code>.env</code>.</li>
    </ul>
  </section>
</div>
@endsection
