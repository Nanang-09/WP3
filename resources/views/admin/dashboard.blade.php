@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        <p class="page-subtitle">Pantau pesanan aktif dan tindak lanjuti yang perlu perhatian segera.</p>
    </div>
    <div class="page-actions">
        <span class="muted-text">Login sebagai {{ auth()->user()->name }}</span>
    </div>
</div>

{{-- ===== NOTIFIKASI PRIORITAS PESANAN BARU ===== --}}
@if($pendingCount > 0)
<div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 20px 24px; border-radius: 14px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 6px 20px rgba(239,68,68,0.3);">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; animation: pulse-ring 2s infinite;">
            <i class="fas fa-bell" style="font-size: 1.5rem;"></i>
        </div>
        <div>
            <h3 style="margin: 0 0 4px; font-size: 1.2rem; font-weight: 700; color: white;">
                🔔 {{ $pendingCount }} Pesanan Baru Menunggu!
            </h3>
            <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">
                Segera jadwalkan konsultasi agar pelanggan tidak menunggu terlalu lama.
            </p>
        </div>
    </div>
    <a href="{{ route('admin.orders.index', ['tab' => 'new']) }}"
       style="background: white; color: #dc2626; padding: 10px 22px; border-radius: 10px; font-weight: 700; text-decoration: none; white-space: nowrap; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.2s;">
        Proses Sekarang <i class="fas fa-arrow-right"></i>
    </a>
</div>
<style>
    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
        70%  { box-shadow: 0 0 0 12px rgba(255,255,255,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
    }
</style>
@endif

{{-- ===== RINGKASAN STATUS AKTIF ===== --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px;">
    {{-- Pesanan Baru --}}
    <a href="{{ route('admin.orders.index', ['tab' => 'new']) }}" style="text-decoration: none;">
        <div style="background: #fff; border: 1.5px solid {{ $pendingCount > 0 ? '#fca5a5' : '#e5e7eb' }}; border-radius: 14px; padding: 18px 20px; display: flex; flex-direction: column; gap: 6px; transition: box-shadow 0.2s; box-shadow: {{ $pendingCount > 0 ? '0 4px 16px rgba(239,68,68,0.12)' : 'none' }};">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 2rem; font-weight: 800; color: {{ $pendingCount > 0 ? '#dc2626' : '#374151' }};">{{ $pendingCount }}</span>
                <div style="background: {{ $pendingCount > 0 ? '#fee2e2' : '#f3f4f6' }}; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-inbox" style="color: {{ $pendingCount > 0 ? '#dc2626' : '#9ca3af' }}; font-size: 0.9rem;"></i>
                </div>
            </div>
            <span style="font-size: 0.8rem; font-weight: 600; color: #6b7280;">Pesanan Baru</span>
        </div>
    </a>

    {{-- Dijadwalkan --}}
    <a href="{{ route('admin.orders.index', ['status' => 'scheduled']) }}" style="text-decoration: none;">
        <div style="background: #fff; border: 1.5px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; flex-direction: column; gap: 6px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 2rem; font-weight: 800; color: #374151;">{{ $scheduledCount }}</span>
                <div style="background: #fffbeb; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-check" style="color: #d97706; font-size: 0.9rem;"></i>
                </div>
            </div>
            <span style="font-size: 0.8rem; font-weight: 600; color: #6b7280;">Dijadwalkan</span>
        </div>
    </a>

    {{-- Dikonfirmasi --}}
    <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" style="text-decoration: none;">
        <div style="background: #fff; border: 1.5px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; flex-direction: column; gap: 6px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 2rem; font-weight: 800; color: #374151;">{{ $confirmedCount }}</span>
                <div style="background: #eff6ff; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-check-double" style="color: #2563eb; font-size: 0.9rem;"></i>
                </div>
            </div>
            <span style="font-size: 0.8rem; font-weight: 600; color: #6b7280;">Dikonfirmasi</span>
        </div>
    </a>

    {{-- Sedang Dikerjakan --}}
    <a href="{{ route('admin.orders.index', ['status' => 'in_progress']) }}" style="text-decoration: none;">
        <div style="background: #fff; border: 1.5px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; flex-direction: column; gap: 6px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 2rem; font-weight: 800; color: #374151;">{{ $inProgressCount }}</span>
                <div style="background: #f5f3ff; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                     <i class="fas fa-hammer" style="color: #7c3aed; font-size: 0.9rem;"></i>
                </div>
            </div>
            <span style="font-size: 0.8rem; font-weight: 600; color: #6b7280;">Dikerjakan</span>
        </div>
    </a>

    {{-- Selesai / Kerja yang sudah --}}
    <a href="{{ route('admin.orders.history') }}" style="text-decoration: none;">
        <div style="background: #fff; border: 1.5px solid #e5e7eb; border-radius: 14px; padding: 18px 20px; display: flex; flex-direction: column; gap: 6px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 2rem; font-weight: 800; color: #10b981;">{{ $completedCount }}</span>
                <div style="background: #d1fae5; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-check-circle" style="color: #10b981; font-size: 0.9rem;"></i>
                </div>
            </div>
            <span style="font-size: 0.8rem; font-weight: 600; color: #6b7280;">Kerjaan Selesai (Riwayat)</span>
        </div>
    </a>
</div>

{{-- ===== DAFTAR PESANAN AKTIF ===== --}}
<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>Pesanan Aktif</h3>
            <p>Semua pesanan yang sedang dalam proses — dari pesanan baru hingga pengerjaan. Pesanan selesai ada di <a href="{{ route('admin.orders.completed') }}" style="color: var(--accent-blue); font-weight: 600;">halaman Selesai</a>.</p>
        </div>
        <a href="{{ route('admin.orders.completed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-check-circle"></i> Lihat Selesai
        </a>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Mandor</th>
                    <th>Status</th>
                    <th>Masuk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeOrders as $order)
                <tr style="{{ $order->status === \App\Models\Order::STATUS_PENDING ? 'background: #fffbfb;' : '' }}">
                    <td class="table-primary">
                        {{ $order->order_number }}
                        @if($order->status === \App\Models\Order::STATUS_PENDING)
                            <span style="display: inline-block; background: #dc2626; color: white; font-size: 0.6rem; padding: 1px 5px; border-radius: 6px; margin-left: 4px; vertical-align: middle; font-weight: 700;">BARU</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-inline">
                            <span class="table-primary">{{ $order->name }}</span>
                            <span class="table-muted">{{ $order->email }}</span>
                        </div>
                    </td>
                    <td>{{ $order->service->name }}</td>
                    <td class="table-muted">{{ $order->foreman?->name ?? 'Belum ditugaskan' }}</td>
                    <td><span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                    <td class="table-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
                            <strong>Tidak ada pesanan aktif</strong>
                            Semua pesanan sudah ditangani. Cek <a href="{{ route('admin.orders.completed') }}">halaman Selesai</a>.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
