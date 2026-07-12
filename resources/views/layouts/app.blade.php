<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WeldTrack - Solusi terpercaya untuk jasa konstruksi bangunan, pengelasan, dan renovasi profesional.">
    <title>@yield('title', 'WeldTrack - Jasa Konstruksi & Pengelasan Profesional')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>
    {{-- Navigation --}}
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="{{ route('home') }}" class="nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="WeldTrack Logo" class="brand-logo" style="height: 40px; width: auto; object-fit: contain;">
                <span class="brand-text">
                    <span class="brand-name">Weld<span class="brand-accent">Track</span></span>
                    <span class="brand-tagline">jasa las pulau lombok</span>
                </span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">Layanan</a></li>
                <li><a href="{{ route('portfolios.index') }}" class="nav-link {{ request()->routeIs('portfolios.*') ? 'active' : '' }}">Portofolio</a></li>
                <li><a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Kontak</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Panel Admin</a></li>
                    @elseif(auth()->user()->isForeman())
                        <li><a href="{{ route('foreman.dashboard') }}" class="nav-link {{ request()->routeIs('foreman.*') ? 'active' : '' }}">Panel Mandor</a></li>
                    @else
                        <li><a href="{{ route('order.index') }}" class="nav-link {{ request()->routeIs('order.*') ? 'active' : '' }}">Pesanan & Progres</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">Pengaturan Akun</a></li>
                    @endif
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;" id="logoutFormNav">
                            @csrf
                            <button type="button" class="nav-link btn-link" onclick="confirmLogout('logoutFormNav')">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success" id="flashAlert">
            <div class="container">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" id="flashAlertError" style="background: rgba(239, 68, 68, 0.12); border-bottom: 1px solid rgba(239, 68, 68, 0.25); color: #fecaca;">
            <div class="container">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="footer-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="WeldTrack Logo" class="brand-logo" style="height: 40px; width: auto; object-fit: contain;">
                        <span>Weld<span class="brand-accent">Track</span></span>
                    </a>
                    <p>Solusi terpercaya untuk jasa konstruksi bangunan, pengelasan, dan renovasi profesional dengan standar kualitas terbaik.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="{{ route('services.index') }}">Pembangunan Rumah</a></li>
                        <li><a href="{{ route('services.index') }}">Renovasi Bangunan</a></li>
                        <li><a href="{{ route('services.index') }}">Konstruksi Baja & Las</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Navigasi</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('services.index') }}">Layanan</a></li>
                        <li><a href="{{ route('portfolios.index') }}">Portofolio</a></li>
                        <li><a href="{{ route('contact') }}">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Hubungi Kami</h4>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Jl. Ahmad Yani, Gerimax Indah, Kec. Narmada, Kabupaten Lombok Barat, Nusa Tenggara Barat</li>
                        <li><i class="fas fa-phone"></i> +62 878-6541-0555</li>
                        <li><i class="fas fa-envelope"></i> rifaifarid@gmail.com</li>
                        <li><i class="fas fa-clock"></i> Sen - Sab: 08.00 - 17.00</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} WeldTrack. Semua Hak Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function confirmLogout(formId) {
            if (confirm('Apakah Anda yakin ingin keluar dari akun ini?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
