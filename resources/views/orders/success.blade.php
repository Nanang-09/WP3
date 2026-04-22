@extends('layouts.app')

@section('title', 'Pesanan Berhasil - WeldTrack')

@section('content')
<section class="success-section">
    <div class="container">
        <div class="fade-in">
            <div class="success-icon {{ $order->status === \App\Models\Order::STATUS_QUEUED ? 'success-icon-queued' : '' }}">
                <i class="fas {{ $order->status === \App\Models\Order::STATUS_QUEUED ? 'fa-hourglass-half' : 'fa-check' }}"></i>
            </div>
            <h1>Pesanan Berhasil Dibuat!</h1>
            @if($order->status === \App\Models\Order::STATUS_QUEUED)
            <p>Pesanan Anda sudah tercatat dan saat ini masuk ke dalam antrean pengerjaan.</p>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Tim kami tetap akan menghubungi Anda untuk konfirmasi, lalu pesanan diproses sesuai urutan antrean.</p>
            @else
            <p>Terima kasih telah mempercayakan proyek Anda kepada WeldTrack.</p>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Tim kami akan segera menghubungi Anda untuk konfirmasi dan survei lokasi.</p>
            @endif

            <div class="order-detail-box">
                <div class="order-detail-row">
                    <span class="order-detail-label">No. Pesanan</span>
                    <span class="order-detail-value" style="color: var(--accent-blue);">{{ $order->order_number }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Layanan</span>
                    <span class="order-detail-value">{{ $order->service->name }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Nama</span>
                    <span class="order-detail-value">{{ $order->name }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Email</span>
                    <span class="order-detail-value">{{ $order->email }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Telepon</span>
                    <span class="order-detail-value">{{ $order->phone }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Status</span>
                    <span class="order-detail-value" style="color: {{ $order->status_color }};">{{ $order->status_label }}</span>
                </div>
                @if($order->status === \App\Models\Order::STATUS_QUEUED)
                <div class="order-detail-row">
                    <span class="order-detail-label">Posisi Antrean</span>
                    <span class="order-detail-value">#{{ $order->queue_position }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Pesanan di Depan</span>
                    <span class="order-detail-value">{{ $order->orders_ahead_count }} pesanan</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Estimasi Tunggu</span>
                    <span class="order-detail-value">{{ $order->estimated_wait_label }}</span>
                </div>
                @endif
            </div>

            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
                <a href="{{ route('home') }}" class="btn btn-outline">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Layanan Lain
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
