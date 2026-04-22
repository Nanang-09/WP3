@extends('layouts.app')

@section('title', 'Portofolio - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Portofolio Kami</h1>
        <p>Proyek-proyek terbaik yang telah kami selesaikan</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <span>Portofolio</span>
        </div>
    </div>
</section>

<section class="portfolio-section">
    <div class="container">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">Semua</button>
            @foreach($categories as $category)
            <button class="filter-btn" data-filter="{{ $category }}">{{ $category }}</button>
            @endforeach
        </div>
        <div class="portfolio-grid">
            @foreach($portfolios as $portfolio)
            <a href="{{ route('portfolios.show', $portfolio->slug) }}" class="portfolio-card fade-in" data-category="{{ $portfolio->category }}">
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
    </div>
</section>
@endsection
