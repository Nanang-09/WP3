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
                        <input type="file" name="photo" class="form-control" accept="image/*" capture="environment" required>
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
                                <img src="{{ $update->photo_url }}" alt="{{ $update->title }}" class="timeline-photo">
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
@endsection
