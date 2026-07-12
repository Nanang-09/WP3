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
            <div class="service-card fade-in">
                <div class="service-card-image">
                    @if($service->image)
                        <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" loading="lazy">
                    @else
                        <div class="service-card-no-image">
                            <i class="fas fa-image"></i>
                            <span>Foto segera hadir</span>
                        </div>
                    @endif
                </div>
                <div class="service-card-body">
                    <h3 class="service-card-title">{{ $service->name }}</h3>
                    <p class="service-card-text">{{ $service->short_description }}</p>
                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-info-circle"></i> Detail
                        </a>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <a href="{{ route('order.create', $service->slug) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-shopping-cart"></i> Pesan
                                </a>
                            @elseif(auth()->user()->isAdmin())
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-clipboard-list"></i> Kelola
                                </a>
                            @elseif(auth()->user()->isForeman())
                                <a href="{{ route('foreman.dashboard') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-helmet-safety"></i> Panel Mandor
                                </a>
                            @endif
                        @else
                            <a href="{{ route('order.create', $service->slug) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-shopping-cart"></i> Pesan
                            </a>
                        @endauth
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
