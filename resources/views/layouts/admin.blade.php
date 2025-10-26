<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('style/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/logo_ayako_icon.png') }}" type="image/png">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="admin-profile">
                <img src="{{ asset('assets/admin.jpg') }}" alt="Admin" class="profile-icon">
                <h3>ADMIN</h3>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i> Laporan Penjualan
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-cart-shopping"></i> Pesanan
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-bag-shopping"></i> Produk
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-shirt"></i> Fabric
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="logout">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
