<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk &middot; Panel Pengaturan</title>
<link rel="stylesheet" href="{{ asset_v('css/admin.css') }}">
</head>
<body class="auth">
<form class="auth__card" method="post" action="{{ route('admin.login.store') }}">
  @csrf
  <span class="auth__logo"><x-icon name="lock" size="22" /></span>
  <h1>Panel Pengaturan</h1>
  <p class="auth__sub">{{ setting('site_name', config('app.name')) }}</p>

  @include('admin.partials.alerts')

  <label class="field">
    <span>Username</span>
    <input type="text" name="username" required autofocus value="{{ old('username') }}" autocomplete="username">
  </label>

  <label class="field">
    <span>Password</span>
    <input type="password" name="password" required autocomplete="current-password">
  </label>

  <label class="check">
    <input type="checkbox" name="remember" value="1" @checked(old('remember'))> Ingat saya
  </label>

  <button class="btn btn--primary btn--block" type="submit">Masuk</button>
  <p class="auth__back"><a href="{{ route('home') }}">&larr; Kembali ke landing page</a></p>
</form>
</body>
</html>
