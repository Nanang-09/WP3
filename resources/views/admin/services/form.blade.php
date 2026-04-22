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
    <form action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST">
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

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Icon (Font Awesome class)</label>
                <input type="text" name="icon" class="form-control" placeholder="fas fa-home" value="{{ old('icon', $service->icon ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
            </div>
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
