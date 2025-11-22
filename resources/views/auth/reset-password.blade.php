<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Atur Ulang Password</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}" />
  <link rel="icon" href="{{ asset('assets/logo_ayako_icon.png') }}" type="image/png">
</head>
<body class="auth">
  <div class="register-container">
    <div class="register-card">
      <div class="left-side"></div>

      <div class="right-side">
        <h2>Atur Ulang Password</h2>

        @if ($errors->any())
          <div class="alert-error">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="register-form">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <input type="password" name="password" placeholder="Password Baru" required />
          <input type="password" name="password_confirmation" placeholder="Konfirmasi Password Baru" required />

          <button type="submit" class="btn-daftar">Ubah Password</button>
        </form>

        <p class="login-text">
          Kembali ke <a href="{{ route('login') }}" class="login-link">Login</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
