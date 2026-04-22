@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-clipboard-list"></i> Kelola Pesanan</h2>
        <p class="page-subtitle">Filter dan cek order pelanggan dari satu tabel yang lebih ringan dibaca.</p>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>Daftar pesanan</h3>
            <p>Gunakan pencarian atau filter status untuk menemukan order yang perlu ditindaklanjuti.</p>
        </div>
        <div class="search-bar">
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Cari pesanan..." value="{{ request('search') }}">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="queued" {{ request('status') == 'queued' ? 'selected' : '' }}>Dalam Antrean</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dikerjakan</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
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
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="table-primary">{{ $order->order_number }}</td>
                    <td>
                        <div class="table-inline">
                            <span class="table-primary">{{ $order->name }}</span>
                            <span class="table-muted">{{ $order->email }}</span>
                        </div>
                    </td>
                    <td>{{ $order->service->name }}</td>
                    <td class="table-muted">{{ $order->budget_range ?? '-' }}</td>
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
                            <i class="fas fa-inbox"></i>
                            <strong>Belum ada pesanan</strong>
                            Order pelanggan akan tampil di tabel ini.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="pagination-wrapper">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
