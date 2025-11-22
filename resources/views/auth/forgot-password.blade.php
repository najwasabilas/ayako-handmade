<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lupa Password</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}" />
  <link rel="icon" href="{{ asset('assets/logo_ayako_icon.png') }}" type="image/png">
</head>
<body class="auth">
  <div class="register-container">
    <div class="register-card">
      <div class="left-side"></div>

      <div class="right-side">
        <h2>Lupa Password</h2>

        @if (session('status'))
          <div class="alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert-error">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="register-form">
          @csrf
          <input type="email" name="email" placeholder="Masukkan Email Anda" required />
          <button type="submit" class="btn-daftar">Kirim Link Reset</button>
        </form>

        <p class="login-text">
          Kembali ke <a href="{{ route('login') }}" class="login-link">Halaman Login</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
