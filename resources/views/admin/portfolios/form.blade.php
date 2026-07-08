@extends('layouts.admin')

@section('title', isset($portfolio) ? 'Edit Portofolio' : 'Tambah Portofolio')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2>
            <i class="fas fa-{{ isset($portfolio) ? 'edit' : 'plus' }}"></i>
            {{ isset($portfolio) ? 'Edit Portofolio' : 'Tambah Portofolio Baru' }}
        </h2>
        <p class="page-subtitle">Form ini mendukung edit data proyek sekaligus upload atau ganti gambar portofolio.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.portfolios.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="content-grid">
    <div class="detail-card">
        <form action="{{ isset($portfolio) ? route('admin.portfolios.update', $portfolio) : route('admin.portfolios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($portfolio)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Judul Proyek *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $portfolio->title ?? '') }}" required>
                @error('title') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Proyek *</label>
                <textarea name="description" class="form-control" required>{{ old('description', $portfolio->description ?? '') }}</textarea>
                @error('description') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Kategori *</label>
                    <input type="text" name="category" class="form-control" placeholder="Contoh: Renovasi Bangunan" value="{{ old('category', $portfolio->category ?? '') }}" required>
                    @error('category') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai *</label>
                    <input type="date" name="completion_date" class="form-control" value="{{ old('completion_date', isset($portfolio) && $portfolio->completion_date ? $portfolio->completion_date->format('Y-m-d') : '') }}" required>
                    @error('completion_date') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Lokasi Proyek *</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $portfolio->location ?? '') }}" required>
                    @error('location') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Klien *</label>
                    <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $portfolio->client_name ?? '') }}" required>
                    @error('client_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Gambar Portofolio</label>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="muted-text" style="margin-top: 8px; font-size: 0.85rem;">Format: JPG, PNG, atau WEBP. Maksimal 5 MB.</p>
                @error('image') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-row-inline">
                <label class="form-check">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $portfolio->is_featured ?? false) ? 'checked' : '' }}>
                    Tampilkan di Beranda (Featured)
                </label>
                @if(isset($portfolio) && $portfolio->image_url)
                    <label class="form-check">
                        <input type="checkbox" name="remove_image" value="1" {{ old('remove_image') ? 'checked' : '' }}>
                        Hapus gambar saat ini
                    </label>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ isset($portfolio) ? 'Update Portofolio' : 'Simpan Portofolio' }}
            </button>
        </form>
    </div>

    <div class="summary-card">
        <h3><i class="fas fa-image"></i> Preview Gambar</h3>
        @if(isset($portfolio) && $portfolio->image_url)
            <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="admin-preview-image" style="margin-bottom: 16px;">
            <p class="summary-note">Upload gambar baru untuk mengganti gambar lama. Jika centang hapus gambar, portofolio akan kembali memakai tampilan placeholder.</p>
        @else
            <div class="admin-preview-image admin-thumb-placeholder" style="margin-bottom: 16px;">
                <i class="fas fa-image"></i>
            </div>
            <p class="summary-note">Belum ada gambar. Setelah upload, gambar ini akan langsung dipakai di halaman beranda, daftar portofolio, dan detail portofolio.</p>
        @endif
    </div>
</div>
@endsection
