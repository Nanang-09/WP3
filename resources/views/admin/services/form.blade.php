@extends('layouts.admin')

@section('title', isset($service) ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2>
            <i class="fas fa-{{ isset($service) ? 'edit' : 'plus' }}"></i>
            {{ isset($service) ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
        </h2>
        <p class="page-subtitle">Form disederhanakan agar input data layanan lebih cepat dan tidak membingungkan.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="detail-card" style="max-width: 760px;">
    <form action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($service)) @method('PUT') @endif

        <div class="form-group">
            <label class="form-label">Nama Layanan *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $service->name ?? '') }}" required>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Singkat *</label>
            <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $service->short_description ?? '') }}" required>
            @error('short_description') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Lengkap *</label>
            <textarea name="description" class="form-control" required>{{ old('description', $service->description ?? '') }}</textarea>
            @error('description') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        {{-- Foto Layanan --}}
        <div class="form-group">
            <label class="form-label">Foto Layanan</label>

            @if(isset($service) && $service->image)
                <div style="margin-bottom: 12px;">
                    <img src="{{ asset($service->image) }}" alt="{{ $service->name }}"
                         style="width: 100%; max-width: 340px; height: 180px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="margin-top: 8px;">
                        <label class="form-check" style="color: var(--accent-red); font-size: 0.88rem;">
                            <input type="checkbox" name="remove_image" value="1">
                            Hapus foto ini
                        </label>
                    </div>
                </div>
            @endif

            <input type="file" name="image" id="image" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp">
            <p style="margin-top: 6px; font-size: 0.82rem; color: var(--text-muted);">
                Format: JPG, PNG, WebP. Maks. 5MB. Disarankan rasio 16:9.
            </p>

            {{-- Preview --}}
            <div id="imagePreviewWrapper" style="display: none; margin-top: 12px;">
                <img id="imagePreview" src="" alt="Preview"
                     style="width: 100%; max-width: 340px; height: 180px; object-fit: cover; border-radius: 10px; border: 1px solid var(--border-color);">
            </div>

            @error('image') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Harga Mulai (Rp) *</label>
                <input type="number" name="price_start" class="form-control" value="{{ old('price_start', $service->price_start ?? '') }}" required>
                @error('price_start') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Satuan Harga *</label>
                <input type="text" name="price_unit" class="form-control" placeholder="per m2" value="{{ old('price_unit', $service->price_unit ?? 'per proyek') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
        </div>

        <div class="form-row-inline">
            <label class="form-check">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $service->is_featured ?? false) ? 'checked' : '' }}>
                Tampilkan di Beranda (Featured)
            </label>
            <label class="form-check">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                Aktif
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ isset($service) ? 'Update Layanan' : 'Simpan Layanan' }}
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('image')?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewWrapper').style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
