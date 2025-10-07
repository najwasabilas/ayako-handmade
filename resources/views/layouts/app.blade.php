<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Ayako')</title>
  <link rel="stylesheet" href="{{ asset('style/style.css') }}">
</head>
<body class="@yield('body-class')">

  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-left">
      <img src="{{ asset('assets/logo_nav.jpg') }}" alt="Ayako Logo" class="logo">
    </div>

    <!-- Tombol hamburger (muncul di mobile) -->
    <button class="hamburger" id="hamburger">&#9776;</button>

    @php
      $current = request()->path(); // Ambil path URL sekarang
    @endphp

    <div class="nav-center" id="nav-menu">
      <a href="{{ url('/') }}" class="{{ $current === '/' ? 'active' : '' }}">HOME</a>
      <a href="{{ url('/katalog') }}" class="{{ $current === 'katalog' ? 'active' : '' }}">KATALOG</a>
      <a href="{{ url('/fabric') }}" class="{{ $current === 'fabric' ? 'active' : '' }}">FABRIC</a>
      <a href="{{ url('/profile-umkm') }}" class="{{ $current === 'profile-umkm' ? 'active' : '' }}">PROFIL</a>
      <a href="{{ url('/galeri') }}" class="{{ $current === 'galeri' ? 'active' : '' }}">GALERI</a>
    </div>

    <div class="nav-icons" id="nav-icons">
      <a href="#"><img src="{{ asset('assets/search.svg') }}" alt="Search"></a>
      <a href="#"><img src="{{ asset('assets/cart.svg') }}" alt="Cart"></a>
      <div class="dropdown">
        <img src="{{ asset('assets/user.svg') }}" alt="User" class="user-icon" id="userDropdownToggle">
        <div class="dropdown-menu" id="userDropdownMenu">
          @auth
            <a href="{{ route('customer.profile') }}">Profile</a>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit">Logout</button>
            </form>
          @else
            <a href="{{ route('login') }}">Login</a>
          @endauth
        </div>
      </div>
    </div>

  </nav>

  <!-- Main Content -->
  <main>
    <!-- Notifikasi Session -->
    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-error">
        {{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-error">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>

  <script>
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const navIcons = document.getElementById('nav-icons');

    hamburger.addEventListener('click', () => {
      navMenu.classList.toggle('show');
      navIcons.classList.toggle('show');
    });

    // Dropdown user
    const userToggle = document.getElementById('userDropdownToggle');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    userToggle.addEventListener('click', () => {
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!userToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.style.display = 'none';
      }
    });
  </script>

    <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <!-- Kolom 1: Logo -->
      <div class="footer-column footer-logo">
        <img src="{{ asset('assets/logo_footer.jpg') }}" alt="Ayako Logo" class="footer-logo-img">
        <p class="footer-brand">Handmade Bag Riau</p>
      </div>

      <!-- Kolom 2: Alamat dan Kontak -->
      <div class="footer-column footer-info">
        <h4>Alamat</h4>
        <p>Jl. Bandara SU, Sidomulyo Tim,<br>
        Kec. Marpoyan Damai, Kota Pekanbaru, Riau 28288</p>

        <h4>Kontak</h4>
        <p>0811-7680-059</p>

        <h4>Email</h4>
        <p>contohayako@gmail.com</p>
      </div>

      <!-- Kolom 3: Tentang Kami -->
      <div class="footer-column footer-links">
        <h4>Tentang Kami</h4>
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/katalog') }}">Katalog</a>
        <a href="{{ url('/profile-umkm') }}">Profil</a>
        <a href="{{ url('/galeri') }}">Galeri</a>
      </div>
    </div>
  </footer>

</body>
</html>
