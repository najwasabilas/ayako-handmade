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
      <a href="{{ url('/profil') }}" class="{{ $current === 'profil' ? 'active' : '' }}">PROFIL</a>
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

</body>
</html>
