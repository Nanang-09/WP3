@extends('layouts.app')

@section('title', 'Panel Mandor - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Panel Mandor</h1>
        <p>Update progres dan foto proyek langsung dari lapangan agar pemesan menerima laporan harian yang akurat.</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <span>Panel Mandor</span>
        </div>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <div class="foreman-stats">
            <div class="summary-card">
                <strong>{{ $stats['assigned_orders'] }}</strong>
                <span>Total order ditugaskan</span>
            </div>
            <div class="summary-card">
                <strong>{{ $stats['active_orders'] }}</strong>
                <span>Order aktif</span>
            </div>
            <div class="summary-card">
                <strong>{{ $stats['completed_orders'] }}</strong>
                <span>Order selesai</span>
            </div>
            <div class="summary-card">
                <strong>{{ $stats['updates_today'] }}</strong>
                <span>Update hari ini</span>
            </div>
        </div>

        <div class="customer-orders-shell fade-in">
            <div class="customer-orders-header">
                <div>
                    <h2><i class="fas fa-helmet-safety"></i> Tugas Lapangan</h2>
                    <p>Pilih proyek untuk mengirim progres harian dan foto lapangan.</p>
                </div>
            </div>

            <div class="customer-orders-list">
                @forelse($orders as $order)
                    <article class="customer-order-card">
                        <div class="customer-order-top">
                            <div>
                                <p class="customer-order-number">{{ $order->order_number }}</p>
                                <h3>{{ $order->service->name }}</h3>
                                <p class="customer-order-date">Pemesan: {{ $order->name }}</p>
                            </div>
                            <span class="customer-order-status" style="--status-color: {{ $order->status_color }};">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <div class="customer-order-grid">
                            <div class="customer-order-panel">
                                <span class="customer-order-label">Alamat Proyek</span>
                                <p>{{ $order->address }}</p>
                            </div>
                            <div class="customer-order-panel">
                                <span class="customer-order-label">Ringkasan Pekerjaan</span>
                                <p>{{ $order->description }}</p>
                            </div>
                        </div>
                        <div class="customer-order-meta">
                            <span><strong>Update terkirim:</strong> {{ $order->updates->count() }}</span>
                            <span><strong>Budget:</strong> {{ $order->budget_range ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="customer-order-actions">
                            <a href="{{ route('foreman.orders.show', $order) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-camera"></i> Kirim Update Lapangan
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="customer-orders-empty">
                        <i class="fas fa-clipboard-check"></i>
                        <h3>Belum ada order yang ditugaskan</h3>
                        <p>Admin perlu menugaskan order ke akun mandor ini terlebih dahulu.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
