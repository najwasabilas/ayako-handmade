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

    <div class="nav-center" id="nav-menu">
      <a href="{{ url('/') }}">HOME</a>
      <a href="#">KATALOG</a>
      <a href="#">FABRIC</a>
      <a href="#">PROFIL</a>
      <a href="#">GALERI</a>
    </div>

    <div class="nav-icons" id="nav-icons">
      <a href=""><img src="{{ asset('assets/search.svg') }}" alt="Search"></a>
      <a href=""><img src="{{ asset('assets/cart.svg') }}" alt="Cart"></a>
      <a href=""><img src="{{ asset('assets/user.svg') }}" alt="User"></a>
    </div>
  </nav>

  <!-- Main Content -->
  <main>
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
  </script>
</body>
</html>
