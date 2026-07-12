<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - WeldTrack</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
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
                    <img src="{{ asset('images/logo.png') }}" alt="WeldTrack Logo" class="brand-logo" style="height: 40px; width: auto; object-fit: contain;">
                    <span>Weld<span class="brand-accent">Track</span></span>
                </a>
                <p class="sidebar-caption">Panel admin yang dirapikan agar lebih mudah dipakai setiap hari.</p>
            </div>
            <nav class="sidebar-nav">
                <div class="sidebar-section-label">Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Pesanan Aktif
                </a>
                <a href="{{ route('admin.orders.completed') }}" class="sidebar-link {{ request()->routeIs('admin.orders.completed') ? 'active' : '' }}" style="padding-left: 2.5rem; font-size: 0.88rem;">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i> Pesanan Selesai
                </a>
                <a href="{{ route('admin.orders.history') }}" class="sidebar-link {{ request()->routeIs('admin.orders.history') ? 'active' : '' }}" style="padding-left: 2.5rem; font-size: 0.88rem;">
                    <i class="fas fa-history" style="color: #f59e0b;"></i> Riwayat Pesanan
                </a>
                <a href="{{ route('admin.foremen.index') }}" class="sidebar-link {{ request()->routeIs('admin.foremen.*') ? 'active' : '' }}">
                    <i class="fas fa-hard-hat"></i> Mandor
                </a>
                <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="fas fa-cogs"></i> Layanan
                </a>
                <a href="{{ route('admin.portfolios.index') }}" class="sidebar-link {{ request()->routeIs('admin.portfolios.*') ? 'active' : '' }}">
                    <i class="fas fa-images"></i> Portofolio
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i> Pesan Masuk
                </a>
                <div class="sidebar-divider"></div>
                <div class="sidebar-section-label">Akses Cepat</div>
                <a href="{{ route('home') }}" class="sidebar-link">
                    <i class="fas fa-globe"></i> Lihat Website
                </a>
                <form action="{{ route('logout') }}" method="POST" id="logoutFormAdmin">
                    @csrf
                    <button type="button" class="sidebar-link sidebar-link-button" onclick="confirmLogout('logoutFormAdmin')">
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

            @if(session('error'))
                <div class="admin-alert" style="background: #fff1f2; border: 1px solid #fecaca; color: #b91c1c;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
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
    <script>
        function confirmLogout(formId) {
            if (confirm('Apakah Anda yakin ingin keluar dari akun ini?')) {
                document.getElementById(formId).submit();
            }
        }

        // ---- REALTIME WEB NOTIFICATION & AUDIO BELL FOR ADMIN ----
        document.addEventListener('DOMContentLoaded', () => {
            // Minta permission browser notifications
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            let lastPendingId = null;
            const checkUrl = @json(route('admin.orders.checkNew'));

            // Fungsi membuat suara bell chime sintetis yang bersih
            function playChime() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Nada 1
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                    gain1.gain.setValueAtTime(0.12, audioCtx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start();
                    osc1.stop(audioCtx.currentTime + 0.35);
                    
                    // Nada 2 (Chime harmonis)
                    setTimeout(() => {
                        const osc2 = audioCtx.createOscillator();
                        const gain2 = audioCtx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime); // A5
                        gain2.gain.setValueAtTime(0.15, audioCtx.currentTime);
                        gain2.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
                        osc2.connect(gain2);
                        gain2.connect(audioCtx.destination);
                        osc2.start();
                        osc2.stop(audioCtx.currentTime + 0.5);
                    }, 140);
                } catch (e) {
                    console.log("Web Audio blocked atau tidak didukung:", e);
                }
            }

            // Polling check order baru
            async function checkForNewOrders() {
                try {
                    const url = lastPendingId 
                        ? `${checkUrl}?last_checked_id=${lastPendingId}` 
                        : checkUrl;

                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    
                    if (lastPendingId === null) {
                        // Inisialisasi awal saat load halaman pertama kali agar tidak men-spam order lama
                        lastPendingId = data.latest_pending_id || 0;
                        return;
                    }

                    if (data.new_orders && data.new_orders.length > 0) {
                        // Bunyikan bell chime
                        playChime();

                        // Kirim notifikasi sistem browser
                        data.new_orders.forEach(order => {
                            if ('Notification' in window && Notification.permission === 'granted') {
                                const notification = new Notification("🔔 Pesanan Baru WeldTrack!", {
                                    body: `${order.name} memesan ${order.service_name} (${order.created_at})`,
                                    icon: "{{ asset('images/logo.png') }}",
                                    tag: `order-${order.id}`
                                });

                                notification.onclick = function() {
                                    window.focus();
                                    window.location.href = order.show_url;
                                };
                            }
                        });

                        // Perbarui ID terakhir
                        lastPendingId = data.latest_pending_id;
                    }
                } catch (err) {
                    console.error("Gagal memeriksa pesanan baru:", err);
                }
            }

            // Jalankan polling setiap 10 detik
            setInterval(checkForNewOrders, 10000);
            // Jalankan sekali saat load
            checkForNewOrders();
        });
    </script>
    @yield('scripts')
</body>
</html>
