@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-history"></i> Riwayat Pesanan</h2>
        <p class="page-subtitle">Catatan semua pesanan yang telah selesai atau dibatalkan. Gunakan pencarian untuk menemukan pelanggan tertentu.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Pesanan Aktif
        </a>
    </div>
</div>

{{-- Stats Banner --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">

    {{-- Total di database --}}
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 1px solid #334155; border-radius: 14px; padding: 20px 22px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #3b82f6; border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-database" style="color: white; font-size: 1.2rem;"></i>
        </div>
        <div>
            <p style="margin: 0 0 2px; font-size: 0.78rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px;">Total di Database</p>
            <h3 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #f1f5f9;">{{ number_format($totalHistoryCount) }}</h3>
        </div>
    </div>

    {{-- Selesai --}}
    <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 1px solid #6ee7b7; border-radius: 14px; padding: 20px 22px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #10b981; border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-check-double" style="color: white; font-size: 1.2rem;"></i>
        </div>
        <div>
            <p style="margin: 0 0 2px; font-size: 0.78rem; color: #047857; text-transform: uppercase; letter-spacing: 0.8px;">Proyek Selesai</p>
            <h3 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #065f46;">
                {{ \App\Models\Order::where('status', \App\Models\Order::STATUS_COMPLETED)->count() }}
            </h3>
        </div>
    </div>

    {{-- Dibatalkan --}}
    <div style="background: linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%); border: 1px solid #fca5a5; border-radius: 14px; padding: 20px 22px; display: flex; align-items: center; gap: 16px;">
        <div style="background: #ef4444; border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="fas fa-times-circle" style="color: white; font-size: 1.2rem;"></i>
        </div>
        <div>
            <p style="margin: 0 0 2px; font-size: 0.78rem; color: #b91c1c; text-transform: uppercase; letter-spacing: 0.8px;">Dibatalkan</p>
            <h3 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #7f1d1d;">
                {{ \App\Models\Order::where('status', \App\Models\Order::STATUS_CANCELLED)->count() }}
            </h3>
        </div>
    </div>

</div>

{{-- Showcase Notice (hanya saat tidak ada pencarian dan total > 10) --}}
@if($showcaseMode && $totalHistoryCount > 10)
<div style="background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border: 1.5px solid #fde047; border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
    <i class="fas fa-info-circle" style="color: #ca8a04; font-size: 1.1rem; margin-top: 1px; flex-shrink: 0;"></i>
    <div>
        <p style="margin: 0; font-size: 0.88rem; color: #713f12; font-weight: 600;">
            Menampilkan 10 riwayat terbaru dari total <strong>{{ number_format($totalHistoryCount) }}</strong> yang tersimpan di database.
        </p>
        <p style="margin: 4px 0 0; font-size: 0.82rem; color: #a16207;">
            Gunakan kolom pencarian di bawah untuk menemukan pesanan pelanggan tertentu — semua data tetap bisa ditemukan.
        </p>
    </div>
</div>
@endif

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>
                @if(request()->filled('search'))
                    Hasil Pencarian: "{{ request('search') }}"
                @else
                    10 Riwayat Terbaru
                @endif
            </h3>
            <p>Cari berdasarkan nama pelanggan, email, nomor pesanan, atau nomor telepon.</p>
        </div>
        <div class="search-bar" style="margin-bottom: 0;">
            <form action="{{ route('admin.orders.history') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama / email / no. pesanan / telp..."
                       value="{{ request('search') }}"
                       style="min-width: 280px;">
                <button type="submit" class="btn btn-primary btn-sm" style="white-space: nowrap;">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.orders.history') }}" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
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
                    <th>Status</th>
                    <th>Tgl. Dibuat</th>
                    <th>Tgl. Update</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historyOrders as $order)
                <tr>
                    <td class="table-primary">{{ $order->order_number }}</td>
                    <td>
                        <div class="table-inline">
                            <span class="table-primary">{{ $order->name }}</span>
                            <span class="table-muted">{{ $order->email }}</span>
                            @if($order->phone)
                                <span class="table-muted" style="font-size: 0.78rem;">{{ $order->phone }}</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $order->service->name }}</td>
                    <td class="table-muted">{{ $order->foreman?->name ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span>
                    </td>
                    <td class="table-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="table-muted">{{ $order->updated_at->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="btn btn-secondary btn-sm" title="Lihat Detail">
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
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            @if(request()->filled('search'))
                                <strong>Tidak ada hasil untuk "{{ request('search') }}"</strong>
                                Coba kata kunci lain atau <a href="{{ route('admin.orders.history') }}">reset pencarian</a>.
                            @else
                                <strong>Belum ada riwayat pesanan</strong>
                                Pesanan yang sudah selesai atau dibatalkan akan muncul di sini.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($historyOrders->hasPages())
    <div class="pagination-wrapper">
        {{ $historyOrders->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
