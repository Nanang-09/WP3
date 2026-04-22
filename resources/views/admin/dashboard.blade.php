@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-chart-pie"></i> Dashboard</h2>
        <p class="page-subtitle">Ringkasan cepat untuk memantau pesanan, layanan, pelanggan, dan pesan masuk tanpa tampilan yang ramai.</p>
    </div>
    <div class="page-actions">
        <span class="muted-text">Login sebagai {{ auth()->user()->name }}</span>
    </div>
</div>

<div class="section-stack">
    <div class="stats-row stats-row-orders">
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['total_orders'] }}</div>
                    <div class="admin-stat-label">Total pesanan</div>
                    <div class="admin-stat-note">Seluruh order yang pernah masuk.</div>
                </div>
                <div class="admin-stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['pending_orders'] }}</div>
                    <div class="admin-stat-label">Menunggu konfirmasi</div>
                    <div class="admin-stat-note">Perlu ditinjau lebih dulu.</div>
                </div>
                <div class="admin-stat-icon gold"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['queued_orders'] }}</div>
                    <div class="admin-stat-label">Dalam antrean</div>
                    <div class="admin-stat-note">Pesanan baru yang menunggu giliran kerja.</div>
                </div>
                <div class="admin-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['in_progress'] }}</div>
                    <div class="admin-stat-label">Sedang dikerjakan</div>
                    <div class="admin-stat-note">Proyek yang masih berjalan.</div>
                </div>
                <div class="admin-stat-icon purple"><i class="fas fa-hammer"></i></div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['completed'] }}</div>
                    <div class="admin-stat-label">Selesai</div>
                    <div class="admin-stat-note">Pekerjaan yang sudah ditutup.</div>
                </div>
                <div class="admin-stat-icon green"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="stats-row stats-row-3">
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['total_services'] }}</div>
                    <div class="admin-stat-label">Layanan aktif</div>
                    <div class="admin-stat-note">Layanan yang tampil untuk pelanggan.</div>
                </div>
                <div class="admin-stat-icon blue"><i class="fas fa-cogs"></i></div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['total_customers'] }}</div>
                    <div class="admin-stat-label">Total pelanggan</div>
                    <div class="admin-stat-note">Jumlah kontak unik dari order.</div>
                </div>
                <div class="admin-stat-icon green"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-header">
                <div>
                    <div class="admin-stat-value">{{ $stats['unread_contacts'] }}</div>
                    <div class="admin-stat-label">Pesan belum dibaca</div>
                    <div class="admin-stat-note">Perlu respons atau tindak lanjut.</div>
                </div>
                <div class="admin-stat-icon red"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
    </div>

    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-title">
                <h3>Pesanan terbaru</h3>
                <p>Lihat order yang paling baru masuk tanpa harus membuka halaman lain lebih dulu.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Lihat semua pesanan</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="table-primary">{{ $order->order_number }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->service->name }}</td>
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
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <strong>Belum ada pesanan</strong>
                                Data order terbaru akan muncul di sini.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
