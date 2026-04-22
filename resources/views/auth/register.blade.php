<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - WeldTrack</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @php
        $googleConfigured = filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
        $facebookConfigured = filled(config('services.facebook.client_id')) && filled(config('services.facebook.client_secret'));
        $socialAuthConfigured = $googleConfigured || $facebookConfigured;
    @endphp
    <section class="auth-section" style="padding-top: 40px; padding-bottom: 40px;">
        <div class="auth-card">
            <div class="auth-header">
                <a href="{{ route('home') }}" class="nav-brand">
                    <i class="fas fa-fire"></i>
                    <span>Weld<span class="brand-accent">Track</span></span>
                </a>
                <h2>Buat Akun</h2>
                <p>Daftar untuk melakukan dan melacak pesanan Anda</p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius); padding: 14px; margin-bottom: 20px; color: var(--accent-red); font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Buat password (min. 8 karakter)" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>

            <div style="margin-top: 24px; text-align: center; position: relative;">
                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 15px 0;">
                <span style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: white; padding: 0 10px; color: var(--text-muted); font-size: 0.85rem;">Atau daftar dengan</span>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                @if($googleConfigured)
                    <a href="{{ route('social.redirect', 'google') }}" class="btn" style="flex: 1; border: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--text-color); box-shadow: none;">
                        <i class="fa-brands fa-google" style="color: #ea4335;"></i> Google
                    </a>
                @else
                    <button type="button" class="btn" disabled style="flex: 1; border: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--text-muted); box-shadow: none; opacity: 0.6; cursor: not-allowed;" title="Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env untuk mengaktifkan login Google.">
                        <i class="fa-brands fa-google" style="color: #ea4335;"></i> Google
                    </button>
                @endif

                @if($facebookConfigured)
                    <a href="{{ route('social.redirect', 'facebook') }}" class="btn" style="flex: 1; border: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--text-color); box-shadow: none;">
                        <i class="fa-brands fa-facebook" style="color: #1877f2;"></i> Facebook
                    </a>
                @else
                    <button type="button" class="btn" disabled style="flex: 1; border: 1px solid var(--border-color); display: flex; justify-content: center; align-items: center; gap: 8px; color: var(--text-muted); box-shadow: none; opacity: 0.6; cursor: not-allowed;" title="Isi FACEBOOK_CLIENT_ID dan FACEBOOK_CLIENT_SECRET di file .env untuk mengaktifkan login Facebook.">
                        <i class="fa-brands fa-facebook" style="color: #1877f2;"></i> Facebook
                    </button>
                @endif
            </div>

            @if(!$socialAuthConfigured)
                <p style="margin-top: 12px; font-size: 0.85rem; color: var(--text-muted); text-align: center;">
                    Login sosial belum aktif di environment ini. Isi kredensial OAuth di file .env lalu jalankan php artisan config:clear.
                </p>
            @endif

            <p style="text-align: center; margin-top: 24px; font-size: 0.9rem;">
                Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--accent-blue); font-weight: 600;">Masuk di sini</a>
            </p>

            <p style="text-align: center; margin-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
                <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </p>
        </div>
    </section>
</body>
</html>
