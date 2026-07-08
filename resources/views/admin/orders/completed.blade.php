@extends('layouts.admin')

@section('title', 'Pesanan Selesai')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-check-circle" style="color: #10b981;"></i> Pesanan Selesai</h2>
        <p class="page-subtitle">Arsip semua proyek yang telah berhasil dikerjakan dan ditutup.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

{{-- Summary --}}
<div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 1px solid #6ee7b7; border-radius: 14px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px;">
    <div style="background: #10b981; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i class="fas fa-trophy" style="color: white; font-size: 1.2rem;"></i>
    </div>
    <div>
        <h4 style="margin: 0 0 2px; font-size: 1.1rem; font-weight: 700; color: #065f46;">
            {{ $completedOrders->total() }} proyek berhasil diselesaikan
        </h4>
        <p style="margin: 0; font-size: 0.85rem; color: #047857;">
            Semua pesanan di halaman ini sudah ditutup dan tidak perlu tindak lanjut.
        </p>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>Arsip Pesanan Selesai</h3>
            <p>Gunakan pencarian untuk menemukan proyek tertentu.</p>
        </div>
        <div class="search-bar" style="margin-bottom: 0;">
            <form action="{{ route('admin.orders.completed') }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Cari pesanan (nama/email/no)..." value="{{ request('search') }}">
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Mandor</th>
                    <th>Budget</th>
                    <th>Tanggal Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completedOrders as $order)
                <tr>
                    <td class="table-primary">{{ $order->order_number }}</td>
                    <td>
                        <div class="table-inline">
                            <span class="table-primary">{{ $order->name }}</span>
                            <span class="table-muted">{{ $order->email }}</span>
                        </div>
                    </td>
                    <td>{{ $order->service->name }}</td>
                    <td class="table-muted">{{ $order->foreman?->name ?? '-' }}</td>
                    <td class="table-muted">{{ $order->budget_range ?? '-' }}</td>
                    <td class="table-muted">{{ $order->updated_at->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm" title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                  onsubmit="return confirm('Hapus pesanan {{ $order->order_number }} secara permanen?')"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm"
                                        style="color: var(--accent-red); border-color: #fecaca; background: #fff5f5; padding-inline: 10px;"
                                        title="Hapus Permanen">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <strong>Belum ada proyek yang selesai</strong>
                            Pesanan yang sudah selesai akan muncul di sini.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($completedOrders->hasPages())
    <div class="pagination-wrapper">
        {{ $completedOrders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
