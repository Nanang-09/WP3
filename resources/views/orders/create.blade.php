@extends('layouts.app')

@section('title', 'Pesan ' . $service->name . ' - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Form Pemesanan</h1>
        <p>Isi detail proyek Anda untuk memesan layanan {{ $service->name }}</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('services.index') }}">Layanan</a>
            <span>/</span>
            <span>Pemesanan</span>
        </div>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <div class="order-grid">
            <div class="order-form-card fade-in">
                <h3 style="font-size: 1.3rem; margin-bottom: 28px;"><i class="fas fa-clipboard-list" style="color: var(--accent-blue);"></i> Detail Pemesanan</h3>
                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="phone">No. Telepon *</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="08xxx" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="budget_range">Estimasi Budget</label>
                            <select id="budget_range" name="budget_range" class="form-control">
                                <option value="">Pilih range budget</option>
                                <option value="< Rp 50 juta" {{ old('budget_range') == '< Rp 50 juta' ? 'selected' : '' }}>< Rp 50 Juta</option>
                                <option value="Rp 50 - 100 juta" {{ old('budget_range') == 'Rp 50 - 100 juta' ? 'selected' : '' }}>Rp 50 - 100 Juta</option>
                                <option value="Rp 100 - 300 juta" {{ old('budget_range') == 'Rp 100 - 300 juta' ? 'selected' : '' }}>Rp 100 - 300 Juta</option>
                                <option value="Rp 300 - 500 juta" {{ old('budget_range') == 'Rp 300 - 500 juta' ? 'selected' : '' }}>Rp 300 - 500 Juta</option>
                                <option value="> Rp 500 juta" {{ old('budget_range') == '> Rp 500 juta' ? 'selected' : '' }}>> Rp 500 Juta</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Alamat Proyek *</label>
                        <textarea id="address" name="address" class="form-control" placeholder="Masukkan alamat lengkap lokasi proyek" style="min-height: 80px;" required>{{ old('address') }}</textarea>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Deskripsi Proyek *</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Jelaskan detail proyek yang Anda inginkan (luas bangunan, jumlah lantai, spesifikasi khusus, dll)" required>{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">Catatan Tambahan</label>
                        <textarea id="notes" name="notes" class="form-control" placeholder="Informasi atau permintaan tambahan (opsional)" style="min-height: 80px;">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                        <i class="fas fa-paper-plane"></i> Kirim Pesanan
                    </button>
                </form>
            </div>

            <div class="order-summary fade-in">
                <h3><i class="fas fa-receipt" style="color: var(--accent-blue);"></i> Ringkasan</h3>
                <div class="order-service-info">
                    <div class="order-service-icon">
                        <i class="{{ $service->icon }}"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1rem;">{{ $service->name }}</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $service->price_unit }}</p>
                    </div>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 20px;">{{ $service->short_description }}</p>
                <div style="padding: 16px; background: rgba(0, 212, 255, 0.05); border-radius: var(--radius); border: 1px solid rgba(0, 212, 255, 0.1);">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Harga mulai dari</p>
                    <p style="font-size: 1.4rem; font-weight: 800; color: var(--accent-gold);">Rp {{ number_format($service->price_start, 0, ',', '.') }}</p>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">{{ $service->price_unit }}</p>
                </div>
                @if($queuePreview)
                <div class="queue-preview-card">
                    <div class="queue-preview-header">
                        <i class="fas fa-hourglass-half"></i>
                        <strong>Pesanan baru akan masuk antrean</strong>
                    </div>
                    <p>Saat ini ada {{ $queuePreview['orders_ahead'] }} pesanan aktif/antrean sebelum pesanan baru diproses.</p>
                    <div class="queue-preview-stats">
                        <span>Posisi antrean baru: #{{ $queuePreview['position'] }}</span>
                        <span>Estimasi tunggu: {{ $queuePreview['estimated_wait_days'] }} hari kerja</span>
                    </div>
                </div>
                @endif
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-glass);">
                    <p style="font-size: 0.8rem; color: var(--text-muted);"><i class="fas fa-info-circle" style="color: var(--accent-blue);"></i> Tim kami akan menghubungi Anda dalam 1x24 jam untuk konfirmasi dan survei lokasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
