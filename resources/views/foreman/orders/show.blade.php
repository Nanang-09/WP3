@extends('layouts.app')

@section('title', 'Update Lapangan - ' . $order->order_number)

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Update Lapangan</h1>
        <p>{{ $order->order_number }} - {{ $order->service->name }} untuk {{ $order->name }}</p>
        <div class="breadcrumb">
            <a href="{{ route('foreman.dashboard') }}">Panel Mandor</a>
            <span>/</span>
            <span>Update Lapangan</span>
        </div>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <div class="order-grid">
            <div class="order-form-card fade-in">
                <h3 style="font-size: 1.3rem; margin-bottom: 28px;"><i class="fas fa-camera" style="color: var(--accent-blue);"></i> Kirim Progres Hari Ini</h3>
                <form action="{{ route('foreman.orders.updates.store', $order) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Judul Update *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Pemasangan rangka atap selesai" required>
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Update *</label>
                        <input type="date" name="update_date" class="form-control" value="{{ old('update_date', now()->toDateString()) }}" required>
                        @error('update_date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    @php
                        $submittedPercents = $order->updates->pluck('progress_percent')->toArray();
                    @endphp
                    <div class="form-group">
                        <label class="form-label">Milestone Progres Proyek *</label>
                        <select name="progress_percent" class="form-control" required style="font-weight: 600;">
                            <option value="">-- Pilih Milestone Progres --</option>
                            <option value="25" {{ in_array(25, $submittedPercents) ? 'disabled' : '' }} {{ old('progress_percent') == 25 ? 'selected' : '' }}>25% - Pekerjaan Awal {{ in_array(25, $submittedPercents) ? '(Sudah Dikirim)' : '' }}</option>
                            <option value="50" {{ in_array(50, $submittedPercents) ? 'disabled' : '' }} {{ old('progress_percent') == 50 ? 'selected' : '' }}>50% - Pekerjaan Menengah {{ in_array(50, $submittedPercents) ? '(Sudah Dikirim)' : '' }}</option>
                            <option value="75" {{ in_array(75, $submittedPercents) ? 'disabled' : '' }} {{ old('progress_percent') == 75 ? 'selected' : '' }}>75% - Pekerjaan Akhir / Finishing {{ in_array(75, $submittedPercents) ? '(Sudah Dikirim)' : '' }}</option>
                            <option value="100" {{ in_array(100, $submittedPercents) ? 'disabled' : '' }} {{ old('progress_percent') == 100 ? 'selected' : '' }}>100% - Proyek Selesai {{ in_array(100, $submittedPercents) ? '(Sudah Dikirim)' : '' }}</option>
                        </select>
                        @error('progress_percent') <p class="form-error">{{ $message }}</p> @enderror
                        <small style="display: block; margin-top: 6px; color: var(--text-muted); line-height: 1.4;">
                            * Pilih milestone yang belum terisi. Sistem akan otomatis memperbarui status proyek ke "Sedang Dikerjakan" (jika memilih 25/50/75%) atau "Selesai" (jika memilih 100%).
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Pekerjaan *</label>
                        <textarea name="description" class="form-control" placeholder="Jelaskan pekerjaan yang selesai hari ini, material yang terpasang, atau kendala lapangan." required>{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Proyek Hari Ini *</label>
                        
                        <!-- Hidden real file input -->
                        <input type="file" id="photo-input" name="photo" style="display: none;" accept="image/*" required>
                        
                        <!-- Two action buttons -->
                        <div style="display: flex; gap: 10px; margin-bottom: 8px;">
                            <button type="button" id="btn-gallery" class="btn btn-secondary" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.9rem; padding: 10px; font-weight: 600; cursor: pointer; border-radius: var(--radius); border: 1.5px solid var(--border-color); background: var(--surface);">
                                <i class="fas fa-images" style="color: var(--accent-blue);"></i> Galeri
                            </button>
                            <button type="button" id="btn-camera" class="btn btn-secondary" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.9rem; padding: 10px; font-weight: 600; cursor: pointer; border-radius: var(--radius); border: 1.5px solid #d97706; background: #d97706; color: white;">
                                <i class="fas fa-camera"></i> Kamera
                            </button>
                        </div>
                        
                        <!-- File Preview Card -->
                        <div id="file-preview-container" style="display: none; align-items: center; gap: 12px; padding: 10px; border: 1.5px dashed var(--border-color); border-radius: var(--radius); background: rgba(0,0,0,0.01); margin-bottom: 8px;">
                            <img id="file-preview-img" src="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                            <div style="flex: 1; overflow: hidden; line-height: 1.4;">
                                <span id="file-preview-name" style="display: block; font-size: 0.82rem; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; color: var(--text-primary);"></span>
                                <span id="file-preview-size" style="display: block; font-size: 0.75rem; color: var(--text-muted);"></span>
                            </div>
                            <button type="button" id="btn-remove-file" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 8px; font-size: 1rem;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        
                        @error('photo') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_visible_to_customer" value="1" {{ old('is_visible_to_customer', '1') ? 'checked' : '' }}>
                            <span>Tampilkan update ini ke pemesan secara langsung</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Kirim Update ke Sistem
                    </button>
                </form>
            </div>

            <div class="order-summary fade-in">
                <h3><i class="fas fa-briefcase" style="color: var(--accent-blue);"></i> Ringkasan Proyek</h3>
                <div class="customer-order-panel" style="margin-bottom: 16px;">
                    <span class="customer-order-label">Pemesan</span>
                    <p>{{ $order->name }} - {{ $order->phone }}</p>
                </div>
                <div class="customer-order-panel" style="margin-bottom: 16px;">
                    <span class="customer-order-label">Alamat</span>
                    <p>{{ $order->address }}</p>
                </div>
                <div class="customer-order-panel" style="margin-bottom: 16px;">
                    <span class="customer-order-label">Yang Dikerjakan</span>
                    <p>{{ $order->description }}</p>
                </div>
                <div class="customer-order-meta">
                    <span><strong>Status:</strong> {{ $order->status_label }}</span>
                    <span><strong>Total update:</strong> {{ $order->updates->count() }}</span>
                </div>

                @if($order->materials->count() > 0)
                <div style="margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 12px; font-weight: 700;">
                        <i class="fas fa-boxes" style="color: var(--accent-blue);"></i> Spesifikasi Bahan & Kebutuhan
                    </h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Berikut adalah daftar bahan baku yang dibutuhkan. Harap gunakan sesuai takaran agar efisien dan tidak mubazir.</p>
                    <div class="table-responsive">
                        <table class="table table-sm" style="font-size: 0.85rem; width: 100%; border: 1px solid var(--border-color);">
                            <thead>
                                <tr style="background: rgba(0,0,0,0.02);">
                                    <th style="padding: 8px;">Bahan</th>
                                    <th style="padding: 8px;">Panjang</th>
                                    <th style="padding: 8px;">Bentuk/Spesifikasi</th>
                                    <th style="padding: 8px; text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->materials as $mat)
                                <tr>
                                    <td style="padding: 8px; font-weight: 600;">{{ $mat->material_name }}</td>
                                    <td style="padding: 8px; color: var(--text-secondary);">{{ $mat->length ?? '-' }}</td>
                                    <td style="padding: 8px; color: var(--text-secondary);">{{ $mat->shape ?? '-' }}</td>
                                    <td style="padding: 8px; text-align: center; font-weight: 700;">{{ $mat->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="milestones-tracker" style="margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 15px; font-weight: 700;">
                        <i class="fas fa-route" style="color: var(--accent-blue);"></i> Track Milestone Progres
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach([25, 50, 75, 100] as $percent)
                            @php
                                $milestoneUpdate = $order->updates->where('progress_percent', $percent)->first();
                            @endphp
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: {{ $milestoneUpdate ? 'rgba(16, 185, 129, 0.04)' : 'rgba(30, 41, 59, 0.01)' }}; border-color: {{ $milestoneUpdate ? 'var(--accent-green)' : 'var(--border-color)' }};">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-family: var(--font-heading); font-weight: 800; font-size: 1.1rem; color: {{ $milestoneUpdate ? 'var(--accent-green)' : 'var(--text-muted)' }};">{{ $percent }}%</span>
                                    <div>
                                        <small style="display: block; font-weight: 600; color: var(--text-primary);">
                                            {{ $milestoneUpdate ? $milestoneUpdate->title : 'Belum Terisi' }}
                                        </small>
                                        @if($milestoneUpdate)
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">Dikirim: {{ $milestoneUpdate->update_date->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($milestoneUpdate)
                                    <button class="portfolio-zoom-trigger" data-lightbox-src="{{ $milestoneUpdate->photo_url }}" data-lightbox-caption="Progres {{ $percent }}% - {{ $milestoneUpdate->title }}" style="width: 40px; height: 30px; border-radius: 4px; overflow: hidden; border: 1px solid var(--border-color); padding: 0; cursor: zoom-in;">
                                        <img src="{{ $milestoneUpdate->photo_url }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                    </button>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.9rem;"><i class="fas fa-lock"></i></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="progress-timeline" style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                    <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 15px; font-weight: 700;"><i class="fas fa-history"></i> Riwayat Laporan</h4>
                    @forelse($order->updates as $update)
                        <div class="timeline-item">
                            <div class="timeline-item-top">
                                <div>
                                    <strong>{{ $update->title }}</strong>
                                    <p class="muted-text">{{ $update->update_date->translatedFormat('d F Y') }}</p>
                                </div>
                                @if($update->progress_percent !== null)
                                    <span class="progress-pill">{{ $update->progress_percent }}%</span>
                                @endif
                            </div>
                            <p>{{ $update->description }}</p>
                            @if($update->photo_url)
                                <img src="{{ $update->photo_url }}" alt="{{ $update->title }}" class="timeline-photo" 
                                     onclick="openProgressLightbox('{{ $update->photo_url }}', '{{ addslashes($update->title) }}')"
                                     style="cursor: zoom-in; max-width: 100%; border-radius: 8px; margin-top: 10px; transition: transform 0.2s;"
                                     onmouseover="this.style.transform='scale(1.02)'"
                                     onmouseout="this.style.transform='scale(1)'">
                            @endif
                        </div>
                    @empty
                        <div class="empty-state" style="padding-inline: 0;">
                            <i class="fas fa-camera"></i>
                            <strong>Belum ada update lapangan</strong>
                            Kirim update pertama setelah pekerjaan hari ini selesai.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Progress Lightbox Modal --}}
<div id="progress-lightbox" onclick="closeProgressLightbox()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center; flex-direction: column; gap: 12px; padding: 20px;">
    <button onclick="closeProgressLightbox(); event.stopPropagation()" style="position: absolute; top: 16px; right: 20px; background: rgba(255,255,255,0.15); border: none; color: white; border-radius: 50%; width: 36px; height: 36px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-times"></i>
    </button>
    <img id="progress-lightbox-img" src="" alt="" style="max-width: 90vw; max-height: 80vh; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); object-fit: contain;">
    <p id="progress-lightbox-caption" style="color: #e2e8f0; font-size: 0.9rem; text-align: center; max-width: 600px;"></p>
</div>
@endsection

@section('scripts')
<script>
function openProgressLightbox(src, caption) {
    const lb = document.getElementById('progress-lightbox');
    if (!lb) return;
    document.getElementById('progress-lightbox-img').src = src;
    document.getElementById('progress-lightbox-caption').textContent = caption;
    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeProgressLightbox() {
    const lb = document.getElementById('progress-lightbox');
    if (lb) lb.style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    // Attach to milestone zoom triggers
    document.querySelectorAll('.portfolio-zoom-trigger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const src = this.getAttribute('data-lightbox-src');
            const caption = this.getAttribute('data-lightbox-caption');
            openProgressLightbox(src, caption);
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeProgressLightbox();
    });

    const photoInput = document.getElementById('photo-input');
    const btnGallery = document.getElementById('btn-gallery');
    const btnCamera = document.getElementById('btn-camera');
    const previewContainer = document.getElementById('file-preview-container');
    const previewImg = document.getElementById('file-preview-img');
    const previewName = document.getElementById('file-preview-name');
    const previewSize = document.getElementById('file-preview-size');
    const btnRemove = document.getElementById('btn-remove-file');

    if (btnGallery && btnCamera && photoInput) {
        btnGallery.addEventListener('click', function() {
            photoInput.removeAttribute('capture');
            photoInput.click();
        });

        btnCamera.addEventListener('click', function() {
            photoInput.setAttribute('capture', 'environment');
            photoInput.click();
        });

        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Read and show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);

                previewName.textContent = file.name;
                // Format size
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                previewSize.textContent = sizeInMB + ' MB';
                
                previewContainer.style.display = 'flex';
            } else {
                clearPreview();
            }
        });
    }

    if (btnRemove) {
        btnRemove.addEventListener('click', function() {
            clearPreview();
        });
    }

    function clearPreview() {
        if (photoInput) photoInput.value = '';
        if (previewImg) previewImg.src = '';
        if (previewName) previewName.textContent = '';
        if (previewSize) previewSize.textContent = '';
        if (previewContainer) previewContainer.style.display = 'none';
    }
});
</script>
@endsection
