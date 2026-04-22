@extends('layouts.admin')

@section('title', 'Kelola Layanan')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-cogs"></i> Kelola Layanan</h2>
        <p class="page-subtitle">Atur layanan yang tampil di website dengan tabel yang lebih sederhana dan mudah dipindai.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Layanan
        </a>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>Daftar layanan</h3>
            <p>Pastikan harga, status, dan layanan unggulan tetap rapi dan mudah dicek.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Nama Layanan</th>
                    <th>Harga Mulai</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td class="table-primary"><i class="{{ $service->icon }}" style="font-size: 1.1rem; color: var(--accent-blue);"></i></td>
                    <td class="table-primary">{{ $service->name }}</td>
                    <td>Rp {{ number_format($service->price_start, 0, ',', '.') }} {{ $service->price_unit }}</td>
                    <td>
                        @if($service->is_featured)
                            <span class="status-badge status-completed"><i class="fas fa-star"></i> Ya</span>
                        @else
                            <span class="table-muted">Tidak</span>
                        @endif
                    </td>
                    <td>
                        @if($service->is_active)
                            <span class="status-badge status-completed">Aktif</span>
                        @else
                            <span class="status-badge status-cancelled">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Yakin hapus layanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-cogs"></i>
                            <strong>Belum ada layanan</strong>
                            Tambahkan layanan baru agar tampil di website.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
