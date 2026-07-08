@extends('layouts.admin')

@section('title', 'Kelola Portofolio')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-images"></i> Kelola Portofolio</h2>
        <p class="page-subtitle">Admin bisa menambah, mengedit, dan mengganti gambar portofolio langsung dari panel ini.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.portfolios.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Portofolio
        </a>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>Daftar portofolio</h3>
            <p>Pastikan judul, kategori, dan gambar proyek selalu rapi agar tampil baik di website.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Judul Proyek</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Selesai</th>
                    <th>Featured</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($portfolios as $portfolio)
                <tr>
                    <td>
                        @if($portfolio->image_url)
                            <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="admin-thumb">
                        @else
                            <div class="admin-thumb admin-thumb-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="table-inline">
                            <span class="table-primary">{{ $portfolio->title }}</span>
                            <span class="table-muted">{{ $portfolio->client_name }}</span>
                        </div>
                    </td>
                    <td>{{ $portfolio->category }}</td>
                    <td>{{ $portfolio->location }}</td>
                    <td>{{ optional($portfolio->completion_date)->translatedFormat('d M Y') ?? '-' }}</td>
                    <td>
                        @if($portfolio->is_featured)
                            <span class="status-badge status-completed"><i class="fas fa-star"></i> Ya</span>
                        @else
                            <span class="table-muted">Tidak</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.portfolios.edit', $portfolio) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.portfolios.destroy', $portfolio) }}" method="POST" onsubmit="return confirm('Yakin hapus portofolio ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <strong>Belum ada portofolio</strong>
                            Tambahkan portofolio baru agar admin bisa menampilkan contoh proyek di website.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
