@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-clipboard-list"></i> Detail Pesanan</h2>
        <p class="page-subtitle">Halaman ini dirapikan supaya data pelanggan, proyek, dan update status terbaca lebih cepat.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="content-grid">
    <div>
        <div class="section-stack">
            <div class="detail-card">
                <h3><i class="fas fa-info-circle"></i> Informasi Pesanan</h3>
                <div class="detail-row">
                    <span class="detail-label">No. Pesanan</span>
                    <span class="detail-value table-primary">{{ $order->order_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value"><span class="status-badge status-{{ $order->status }}">{{ $order->status_label }}</span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Layanan</span>
                    <span class="detail-value">{{ $order->service->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pesanan</span>
                    <span class="detail-value">{{ $order->created_at->format('d F Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Budget Range</span>
                    <span class="detail-value">{{ $order->budget_range ?? 'Belum ditentukan' }}</span>
                </div>
                @if($order->status === \App\Models\Order::STATUS_QUEUED)
                <div class="detail-row">
                    <span class="detail-label">Posisi Antrean</span>
                    <span class="detail-value">#{{ $order->queue_position }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estimasi Tunggu</span>
                    <span class="detail-value">{{ $order->estimated_wait_label }}</span>
                </div>
                @endif
            </div>

            <div class="detail-card">
                <h3><i class="fas fa-user"></i> Data Pelanggan</h3>
                <div class="detail-row">
                    <span class="detail-label">Nama</span>
                    <span class="detail-value">{{ $order->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $order->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Telepon</span>
                    <span class="detail-value">{{ $order->phone }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Alamat Proyek</span>
                    <span class="detail-value">{{ $order->address }}</span>
                </div>
            </div>

            <div class="detail-card">
                <h3><i class="fas fa-file-alt"></i> Deskripsi Proyek</h3>
                <p>{{ $order->description }}</p>
                @if($order->notes)
                <div class="summary-note" style="margin-top: 18px;">
                    <strong>Catatan tambahan:</strong><br>
                    {{ $order->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div>
        <div class="summary-card">
            <h3 class="panel-title"><i class="fas fa-edit"></i> Update Status</h3>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Status Pesanan</label>
                    <select name="status" class="form-control">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="queued" {{ $order->status == 'queued' ? 'selected' : '' }}>Sedang Dalam Antrean</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Admin</label>
                    <textarea name="admin_notes" class="form-control" placeholder="Tambahkan catatan...">{{ $order->admin_notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
            <div class="summary-note" style="margin-top: 16px;">
                Perubahan status akan membantu tim memantau progres order tanpa harus membuka banyak halaman.
            </div>
        </div>
    </div>
</div>
@endsection
