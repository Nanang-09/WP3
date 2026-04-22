@extends('layouts.app')

@section('title', 'WeldTrack - Jasa Konstruksi & Pengelasan Profesional')

@section('content')
<section class="hero" id="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-hard-hat"></i> Jasa konstruksi dan pengelasan
            </div>
            <h1 class="hero-title">
                Tampilan sederhana, proses jelas, hasil kerja tetap profesional.
            </h1>
            <p class="hero-description">
                WeldTrack membantu Anda memesan layanan konstruksi dengan alur yang mudah dipahami. Pilih layanan, lihat portofolio, lalu hubungi tim kami untuk mulai proyek.
            </p>
            <div class="hero-buttons">
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Layanan
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline">
                    <i class="fas fa-phone"></i> Konsultasi
                </a>
            </div>
            <div class="hero-summary">
                <div class="summary-card">
                    <strong>500+</strong>
                    <span>proyek selesai</span>
                </div>
                <div class="summary-card">
                    <strong>Harga jelas</strong>
                    <span>tanpa penjelasan berbelit</span>
                </div>
                <div class="summary-card">
                    <strong>Respon cepat</strong>
                    <span>tim follow up maksimal 1x24 jam</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-section fade-in" id="tentang">
    <div class="container">
        <div class="about-grid">
            <div class="about-content">
                <div class="section-badge">
                    <i class="fas fa-info-circle"></i> Tentang Kami
                </div>
                <h2>Informasi penting ditampilkan seperlunya.</h2>
                <p>Website ini dirapikan agar calon klien bisa langsung fokus ke layanan, contoh proyek, dan cara menghubungi tim kami tanpa terganggu elemen visual yang berlebihan.</p>
                <p>Kami tetap menangani proyek rumah, renovasi, fabrikasi, dan pengelasan dengan standar kerja yang sama: jelas, rapi, dan mudah diikuti sejak awal.</p>
            </div>
            <div class="about-panel fade-in">
                <h3>Alur singkat</h3>
                <div class="about-features">
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i> Pilih layanan yang dibutuhkan
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i> Isi detail proyek
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i> Tim meninjau kebutuhan Anda
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check-circle"></i> Pekerjaan dimulai setelah konfirmasi
                    </div>
                </div>
                <p class="about-panel-note">Cocok untuk pengunjung yang ingin cepat paham tanpa harus membaca terlalu banyak di awal.</p>
                <a href="{{ route('contact') }}" class="btn btn-secondary">Hubungi Tim</a>
            </div>
        </div>
    </div>
</section>

<section class="services-section fade-in" id="layanan">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-cogs"></i> Layanan Kami
            </div>
            <h2 class="section-title">Layanan yang paling sering dicari</h2>
            <p class="section-subtitle">Setiap layanan ditampilkan ringkas agar pengunjung bisa membandingkan pilihan dengan cepat.</p>
        </div>
        <div class="services-grid">
            @foreach($services as $service)
            <div class="card fade-in">
                <div class="card-icon">
                    <i class="{{ $service->icon }}"></i>
                </div>
                <h3 class="card-title">{{ $service->name }}</h3>
                <p class="card-text">{{ $service->short_description }}</p>
                <p class="card-price">Mulai dari {{ $service->formatted_price }}</p>
                <a href="{{ route('services.show', $service->slug) }}" class="card-link">
                    Selengkapnya <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="portfolio-section fade-in" id="portofolio">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-images"></i> Portofolio
            </div>
            <h2 class="section-title">Contoh pekerjaan kami</h2>
            <p class="section-subtitle">Portofolio dibuat lebih bersih agar pengunjung fokus ke jenis proyek, lokasi, dan hasil akhirnya.</p>
        </div>
        <div class="portfolio-grid">
            @foreach($portfolios as $portfolio)
            <a href="{{ route('portfolios.show', $portfolio->slug) }}" class="portfolio-card fade-in">
                <div class="portfolio-image">
                    <i class="fas fa-building"></i>
                    <span class="portfolio-category">{{ $portfolio->category }}</span>
                </div>
                <div class="portfolio-info">
                    <h3>{{ $portfolio->title }}</h3>
                    <div class="portfolio-meta">
                        <span><i class="fas fa-map-marker-alt"></i> {{ $portfolio->location }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $portfolio->completion_date->format('M Y') }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('portfolios.index') }}" class="btn btn-outline">
                Lihat Semua Portofolio <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="testimonials-section fade-in" id="testimoni">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-quote-left"></i> Testimoni
            </div>
            <h2 class="section-title">Ulasan dari klien</h2>
            <p class="section-subtitle">Ditampilkan singkat supaya tetap informatif tanpa membuat halaman terasa penuh.</p>
        </div>
        <div class="testimonials-grid">
            @foreach($testimonials as $testimonial)
            <div class="testimonial-card fade-in">
                <div class="testimonial-stars">
                    @for($i = 0; $i < $testimonial->rating; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                </div>
                <p class="testimonial-content">"{{ $testimonial->content }}"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">
                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="testimonial-name">{{ $testimonial->name }}</div>
                        <div class="testimonial-role">{{ $testimonial->role }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="cta-section fade-in">
    <div class="container">
        <div class="cta-content">
            <h2>Sudah tahu layanan yang dibutuhkan?</h2>
            <p>Lanjutkan ke pemesanan atau hubungi kami untuk konsultasi singkat. Tujuannya tetap sama: proses mudah dan cepat dipahami.</p>
            <div class="cta-buttons">
                <a href="{{ route('services.index') }}" class="btn btn-gold">
                    <i class="fas fa-rocket"></i> Mulai Sekarang
                </a>
                <a href="https://wa.me/6281234567890" class="btn btn-outline" target="_blank">
                    <i class="fab fa-whatsapp"></i> Chat WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
