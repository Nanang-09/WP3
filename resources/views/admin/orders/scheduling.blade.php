@extends('layouts.admin')

@section('title', 'Penjadwalan Proyek')

@section('content')
<div class="page-title" style="margin-bottom: 30px;">
    <div class="page-heading">
        <h2><i class="fas fa-calendar-check" style="color: var(--accent-blue);"></i> Penjadwalan Proyek</h2>
        <p class="page-subtitle">Form ini digunakan untuk mengaktifkan pengerjaan proyek dan memulai monitoring kemajuan lapangan.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.orders.consultation', $order) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Konsultasi
        </a>
    </div>
</div>

<div class="content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
    <!-- Main Scheduling Form -->
    <div class="detail-card" style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow-md);">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 24px; color: var(--text-primary); border-bottom: 1.5px dashed var(--border-color); padding-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-handshake" style="color: var(--accent-blue);"></i> Form Kesepakatan & Jadwal Pengerjaan
        </h3>

        @if($errors->any())
        <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 24px; font-size: 0.88rem;">
            <strong style="display: block; margin-bottom: 4px;"><i class="fas fa-exclamation-circle"></i> Periksa kembali isian Anda:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.orders.saveScheduling', $order) }}" method="POST">
            @csrf
            
            <!-- Foreman Selection -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary); margin-bottom: 8px; display: block;">Tugaskan Mandor Lapangan *</label>
                <select name="foreman_id" class="form-control" required style="width: 100%; border-radius: 8px; border-color: var(--border-color); padding: 10px; font-size: 0.9rem;">
                    <option value="">Pilih Mandor...</option>
                    @foreach($foremen as $foreman)
                        <option value="{{ $foreman->id }}" {{ (string) old('foreman_id', $order->foreman_id) === (string) $foreman->id ? 'selected' : '' }}>
                            {{ $foreman->name }}{{ $foreman->phone ? ' - ' . $foreman->phone : '' }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">Mandor yang dipilih akan bertanggung jawab mengirimkan laporan progres foto di lapangan.</small>
            </div>

            <!-- Date Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <!-- Start Date -->
                <div class="form-group">
                    <label class="form-label" style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary); margin-bottom: 8px; display: block;">Tanggal Mulai Proyek *</label>
                    <input type="date" name="project_start_date" class="form-control" value="{{ old('project_start_date', optional($order->project_start_date ?: now()->addDays(2))->format('Y-m-d')) }}" required style="width: 100%; border-radius: 8px; border-color: var(--border-color); padding: 10px;">
                </div>
                <!-- End Date -->
                <div class="form-group">
                    <label class="form-label" style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary); margin-bottom: 8px; display: block;">Tanggal Selesai Proyek *</label>
                    <input type="date" name="project_end_date" class="form-control" value="{{ old('project_end_date', optional($order->project_end_date ?: now()->addDays(12))->format('Y-m-d')) }}" required style="width: 100%; border-radius: 8px; border-color: var(--border-color); padding: 10px;">
                </div>
            </div>

            <!-- Notes -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary); margin-bottom: 8px; display: block;">Catatan Kesepakatan</label>
                <textarea name="agreement_notes" class="form-control" rows="5" placeholder="Masukkan detail spesifikasi material besi, ukuran ketebalan, tipe pengerjaan, atau catatan khusus proyek lainnya yang disepakati..." style="width: 100%; border-radius: 8px; border-color: var(--border-color);">{{ old('agreement_notes', $order->agreement_notes) }}</textarea>
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">Catatan ini akan menjadi panduan bagi Mandor Lapangan dalam mengerjakan proyek.</small>
            </div>

            <!-- Save Submit -->
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 700; background: #3b82f6; border-color: #3b82f6; color: white; padding: 12px 24px; border-radius: 8px; width: 100%;">
                <i class="fas fa-save"></i> Simpan Penjadwalan & Aktifkan Proyek
            </button>
        </form>
    </div>

    <!-- Sidebar Summary -->
    <div class="detail-card" style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <i class="fas fa-info-circle" style="color: var(--accent-blue);"></i> Info Pesanan
        </h3>
        <div class="detail-row" style="flex-direction: column; align-items: start; gap: 2px; margin-bottom: 12px;">
            <span class="detail-label" style="font-size: 0.72rem; text-transform: uppercase;">No. Pesanan</span>
            <span class="detail-value" style="font-weight: 700; color: var(--accent-blue);">{{ $order->order_number }}</span>
        </div>
        <div class="detail-row" style="flex-direction: column; align-items: start; gap: 2px; margin-bottom: 12px;">
            <span class="detail-label" style="font-size: 0.72rem; text-transform: uppercase;">Layanan</span>
            <span class="detail-value" style="font-weight: 600;">{{ $order->service->name }}</span>
        </div>
        <div class="detail-row" style="flex-direction: column; align-items: start; gap: 2px; margin-bottom: 12px;">
            <span class="detail-label" style="font-size: 0.72rem; text-transform: uppercase;">Pelanggan</span>
            <span class="detail-value" style="font-weight: 600;">{{ $order->name }}</span>
        </div>
        <div class="detail-row" style="flex-direction: column; align-items: start; gap: 2px;">
            <span class="detail-label" style="font-size: 0.72rem; text-transform: uppercase;">Alamat Lokasi</span>
            <span class="detail-value" style="font-size: 0.88rem; line-height: 1.4;">{{ $order->address }}</span>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 12px; margin-top: 15px; background: rgba(59, 130, 246, 0.02); border-radius: 8px; padding: 12px;">
            <strong style="font-size: 0.82rem; color: #1e3a8a; display: block; margin-bottom: 4px;"><i class="fas fa-shield-alt"></i> Info Alur:</strong>
            <p style="font-size: 0.78rem; color: #1e3a8a; line-height: 1.4; margin: 0;">
                Setelah form di sisi kiri disimpan, status akan berubah menjadi **Sedang Dikerjakan**. Pelanggan akan melihat status pengerjaan proyek serta timeline milestones di halaman tracking mereka.
            </p>
        </div>
    </div>
</div>
@endsection
