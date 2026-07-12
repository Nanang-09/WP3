@extends('layouts.app')

@section('title', $portfolio->title . ' - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Detail Portofolio</h1>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('portfolios.index') }}">Portofolio</a>
            <span>/</span>
            <span>{{ $portfolio->title }}</span>
        </div>
    </div>
</section>

<section class="portfolio-detail">
    <div class="container">
        <div class="portfolio-detail-card fade-in">
            <div class="portfolio-detail-image">
                @if($portfolio->image_url)
                    <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}">
                @else
                    <i class="fas fa-building"></i>
                @endif
            </div>
            <div class="portfolio-detail-body">
                <span class="portfolio-category" style="position: static; margin-bottom: 16px; display: inline-block;">{{ $portfolio->category }}</span>
                <h2>{{ $portfolio->title }}</h2>
                <div class="portfolio-detail-meta">
                    <span><i class="fas fa-map-marker-alt"></i> {{ $portfolio->location }}</span>
                    <span><i class="fas fa-user"></i> {{ $portfolio->client_name }}</span>
                    <span><i class="fas fa-calendar"></i> {{ optional($portfolio->completion_date)->translatedFormat('d F Y') ?? '-' }}</span>
                </div>
                <p>{{ $portfolio->description }}</p>
                <div style="margin-top: 30px;">
                    <a href="{{ route('portfolios.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali ke Portofolio
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
