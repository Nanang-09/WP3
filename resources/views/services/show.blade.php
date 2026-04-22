@extends('layouts.app')

@section('title', $service->name . ' - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $service->name }}</h1>
        <p>{{ $service->short_description }}</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('services.index') }}">Layanan</a>
            <span>/</span>
            <span>{{ $service->name }}</span>
        </div>
    </div>
</section>

<section class="service-detail">
    <div class="container">
        <div class="service-detail-grid">
            <div class="service-detail-content fade-in">
                <div class="card-icon" style="margin-bottom: 24px;">
                    <i class="{{ $service->icon }}"></i>
                </div>
                <h2>{{ $service->name }}</h2>
                <p>{{ $service->description }}</p>

                <h3 style="margin-top: 30px; margin-bottom: 16px; font-size: 1.2rem;">Keunggulan Layanan</h3>
                <div class="about-features" style="margin-top: 0;">
                    <div class="about-feature"><i class="fas fa-check-circle"></i> Material Berkualitas</div>
                    <div class="about-feature"><i class="fas fa-check-circle"></i> Tim Berpengalaman</div>
                    <div class="about-feature"><i class="fas fa-check-circle"></i> Garansi Pekerjaan</div>
                    <div class="about-feature"><i class="fas fa-check-circle"></i> Tepat Waktu</div>
                    <div class="about-feature"><i class="fas fa-check-circle"></i> Konsultasi Gratis</div>
                    <div class="about-feature"><i class="fas fa-check-circle"></i> Harga Transparan</div>
                </div>
            </div>
            <div class="service-detail-sidebar fade-in">
                <div class="service-price-card">
                    <div class="card-icon" style="margin: 0 auto 16px;">
                        <i class="{{ $service->icon }}"></i>
                    </div>
                    <h3>{{ $service->name }}</h3>
                    <div class="price">Rp {{ number_format($service->price_start, 0, ',', '.') }}</div>
                    <div class="price-unit">{{ $service->price_unit }}</div>
                    <a href="{{ route('order.create', $service->slug) }}" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                    </a>
                    <a href="https://wa.me/6281234567890?text=Halo, saya tertarik dengan layanan {{ $service->name }}" class="btn btn-outline" style="width: 100%; margin-top: 12px;" target="_blank">
                        <i class="fab fa-whatsapp"></i> Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
