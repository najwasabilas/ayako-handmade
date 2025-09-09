<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi Customer</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}" />
</head>
<body>
  <div class="register-container">
    <div class="register-card">
      <div class="left-side"></div>

      <div class="right-side">
        <h2>Form Registrasi</h2>
        <form action="#" method="POST" class="register-form" action="{{ url('/register') }}">
            @csrf
          <input type="text" name="name" placeholder="Nama Lengkap" required />
          <input type="email" name="email" placeholder="Masukkan Email" required />
          <input type="password" name="password" placeholder="Masukkan Password" required />
          <input type="password" name="password_confirmation" placeholder="Ulangi Password" required />

          <button type="submit" class="btn-daftar">Daftar</button>
        </form>

        <p class="login-text">
          Sudah punya akun? <a href="{{ route('login') }}" class="login-link">Login sekarang</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
