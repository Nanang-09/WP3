@extends('layouts.app')

@section('title', 'Layanan Kami - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Layanan Kami</h1>
        <p>Berbagai solusi konstruksi dan pengelasan profesional untuk kebutuhan Anda</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <span>Layanan</span>
        </div>
    </div>
</section>

<section class="services-section">
    <div class="container">
        <div class="services-grid">
            @foreach($services as $service)
            <div class="card fade-in">
                <div class="card-icon">
                    <i class="{{ $service->icon }}"></i>
                </div>
                <h3 class="card-title">{{ $service->name }}</h3>
                <p class="card-text">{{ $service->short_description }}</p>
                <p class="card-price">Mulai dari {{ $service->formatted_price }}</p>
                <div style="display: flex; gap: 12px; margin-top: 16px;">
                    <a href="{{ route('services.show', $service->slug) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-info-circle"></i> Detail
                    </a>
                    <a href="{{ route('order.create', $service->slug) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-shopping-cart"></i> Pesan
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="cta-section fade-in">
    <div class="container">
        <div class="cta-content">
            <h2>Butuh Layanan Khusus?</h2>
            <p>Hubungi kami untuk konsultasi gratis mengenai kebutuhan konstruksi Anda yang spesifik.</p>
            <div class="cta-buttons">
                <a href="{{ route('contact') }}" class="btn btn-gold">
                    <i class="fas fa-envelope"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
