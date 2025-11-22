<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi Customer</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}" />
  <link rel="icon" href="{{ asset('assets/logo_ayako_icon.png') }}" type="image/png">
</head>
<body class="auth">
  <div class="register-container">
    <div class="register-card">
      <div class="left-side"></div>

      <div class="right-side">
        <h2>Form Registrasi</h2>

        {{-- Tampilkan semua error jika ada --}}
        @if ($errors->any())
          <div class="error-alert">
            <ul style="margin: 0; padding-left: 20px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" class="register-form" action="{{ url('/register') }}">
            @csrf
          <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required />
          <input type="email" name="email" placeholder="Masukkan Email" value="{{ old('email') }}" required />
          <input type="password" name="password" placeholder="Masukkan Password" required />
          <input type="password" name="password_confirmation" placeholder="Ulangi Password" required />

          <button type="submit" class="btn-daftar">Daftar</button>
          <div class="divider">atau</div>
            <a href="{{ route('google.login') }}" class="btn-google">
              <img src="{{ asset('assets/google-icon.png') }}" alt="Google Logo" class="google-icon">
              Daftar dengan Google
            </a>

        </form>

        <p class="login-text">
          Sudah punya akun? <a href="{{ route('login') }}" class="login-link">Login sekarang</a>
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
