<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Customer</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}" />
</head>
<body class="auth">
  <div class="register-container">
    <div class="register-card">
      <div class="left-side"></div>

      <div class="right-side">
        <h2>Form Login</h2>
        <form action="#" method="POST" class="register-form" action="{{ url('/login') }}">
            @csrf
          <input type="email" name="email" placeholder="Masukkan Email" required />
          <input type="password" name="password" placeholder="Masukkan Password" required />
          <p class="forgot-password"><a href="#">Lupa password?</a></p>

          <button type="submit" class="btn-daftar">Masuk</button>
        </form>

        <p class="login-text">
          Belum punya akun? <a href="{{ route('register') }}" class="login-link">Daftar sekarang</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
