<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - WeldTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <i class="fas fa-fire"></i>
                    <span>Weld<span class="brand-accent">Track</span></span>
                </a>
                <p class="sidebar-caption">Panel admin yang dirapikan agar lebih mudah dipakai setiap hari.</p>
            </div>
            <nav class="sidebar-nav">
                <div class="sidebar-section-label">Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Pesanan
                </a>
                <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="fas fa-cogs"></i> Layanan
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i> Pesan Masuk
                </a>
                <div class="sidebar-divider"></div>
                <div class="sidebar-section-label">Akses Cepat</div>
                <a href="{{ route('home') }}" class="sidebar-link">
                    <i class="fas fa-globe"></i> Lihat Website
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link sidebar-link-button">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <main class="admin-main">
            <header class="admin-header">
                <div class="admin-header-left">
                    <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="admin-header-meta">
                        <small>Panel Admin</small>
                        <strong>@yield('title', 'Dashboard')</strong>
                    </div>
                </div>
                <div class="admin-header-right">
                    <span class="admin-user">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </span>
                </div>
            </header>

            @if(session('success'))
                <div class="admin-alert admin-alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const adminMain = document.querySelector('.admin-main');
        const mobileBreakpoint = window.matchMedia('(max-width: 960px)');

        function closeSidebarOnMobile() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        }

        function syncSidebarMode() {
            if (mobileBreakpoint.matches) {
                adminMain.classList.remove('expanded');
                sidebar.classList.remove('collapsed');
                closeSidebarOnMobile();
            } else {
                sidebarOverlay.classList.remove('show');
                sidebar.classList.remove('show');
            }
        }

        sidebarToggle?.addEventListener('click', () => {
            if (mobileBreakpoint.matches) {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                return;
            }

            sidebar.classList.toggle('collapsed');
            adminMain.classList.toggle('expanded');
        });

        sidebarOverlay?.addEventListener('click', closeSidebarOnMobile);
        mobileBreakpoint.addEventListener('change', syncSidebarMode);
        syncSidebarMode();
    </script>
    @yield('scripts')
</body>
</html>
