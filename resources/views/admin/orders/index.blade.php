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
        
        <div class="order-tabs" style="display: flex; gap: 15px; margin-bottom: 10px;">
            <a href="{{ route('admin.orders.index', ['tab' => 'new']) }}" class="btn {{ $activeTab === 'new' ? 'btn-primary' : 'btn-secondary' }}" style="position: relative;">
                Pesanan Baru
                @if($newOrdersCount > 0)
                    <span style="position: absolute; top: -5px; right: -10px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; font-weight: bold;">{{ $newOrdersCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.orders.index', ['tab' => 'all']) }}" class="btn {{ $activeTab === 'all' ? 'btn-primary' : 'btn-secondary' }}">
                Semua Pesanan
            </a>
        </div>

        <div class="search-bar">
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <input type="text" name="search" class="form-control" placeholder="Cari pesanan (nama/email/no)..." value="{{ request('search') }}">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status Aktif</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="queued" {{ request('status') == 'queued' ? 'selected' : '' }}>Dalam Antrean</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Konsultasi Dijadwalkan</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dikerjakan</option>
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
                    <th>Mandor</th>
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
                    <td class="table-muted">{{ $order->foreman?->name ?? 'Belum ada' }}</td>
                    <td class="table-muted">{{ $order->budget_range ?? '-' }}</td>
                    <td><span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span></td>
                    <td class="table-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm" title="Detail Pesanan">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            
                            @if($order->status === \App\Models\Order::STATUS_PENDING || $order->status === \App\Models\Order::STATUS_QUEUED)
                                <button type="button" class="btn btn-secondary btn-sm" style="color: var(--accent-red); border-color: #fecaca; background: #fff5f5;" title="Tolak Pesanan" onclick="rejectOrder('{{ route('admin.orders.reject', $order) }}')">
                                    <i class="fas fa-ban"></i> Tolak
                                </button>
                            @endif
                            
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini secara permanen?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--accent-red); border-color: #fecaca; background: #fff5f5; padding-inline: 10px;" title="Hapus Pesanan">
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

<form id="globalRejectForm" action="" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="rejection_reason" id="globalRejectionReason">
</form>

<script>
function rejectOrder(actionUrl) {
    const reason = prompt("Masukkan alasan penolakan pesanan:");
    if (reason === null) return; // user cancelled
    
    if (reason.trim() === "") {
        alert("Alasan penolakan wajib diisi!");
        return;
    }
    
    const form = document.getElementById('globalRejectForm');
    const input = document.getElementById('globalRejectionReason');
    
    form.action = actionUrl;
    input.value = reason;
    form.submit();
}
</script>
@endsection
