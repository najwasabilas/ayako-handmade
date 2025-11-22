<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Customer</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}" />
  <link rel="icon" href="{{ asset('assets/logo_ayako_icon.png') }}" type="image/png">
</head>
<body class="auth">
  <div class="register-container">
    <div class="register-card">
      <div class="left-side"></div>

      <div class="right-side">
        <h2>Form Login</h2>
        @if ($errors->any())
          <div class="alert-error">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" class="register-form" action="{{ url('/login') }}">
            @csrf
          <input type="email" name="email" placeholder="Masukkan Email" required />
          <input type="password" name="password" placeholder="Masukkan Password" required />
          <p class="forgot-password"><a href="{{ route('password.request') }}">Lupa password?</a></p>

          <button type="submit" class="btn-daftar">Masuk</button>
          <div class="divider">atau</div>
          <a href="{{ route('google.login') }}" class="btn-google">
            <img src="{{ asset('assets/google-icon.png') }}" alt="Google Logo" class="google-icon">
            Masuk dengan Google
          </a>

        </form>

        <p class="login-text">
          Belum punya akun? <a href="{{ route('register') }}" class="login-link">Daftar sekarang</a>
        </p>
      </div>
    </div>
  </div>
  <style>
    .btn-google {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: #ffffff;
      border: 1px solid #ccc;
      color: #444;
      padding: 10px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      margin-top: 10px;
      transition: 0.2s;
    }
    .btn-google:hover {
      background-color: #f5f5f5;
    }
    .google-icon {
      width: 20px;
      height: 20px;
    }
    .divider {
      text-align: center;
      color: #aaa;
      margin: 15px 0;
      font-size: 14px;
      position: relative;
    }
    .divider::before,
    .divider::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 40%;
      height: 1px;
      background: #ddd;
    }
    .divider::before { left: 0; }
    .divider::after { right: 0; }

  </style>
</body>
</html>
