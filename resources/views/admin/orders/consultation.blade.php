@extends('layouts.admin')

@section('title', 'Proses Konsultasi Proyek')

@section('content')
<div class="page-title" style="margin-bottom: 30px;">
    <div class="page-heading">
        <h2><i class="fas fa-comments-alt" style="color: var(--accent-blue);"></i> Proses Konsultasi Proyek</h2>
        <p class="page-subtitle">Pelanggan and Admin saat ini berada dalam tahap diskusi konsultasi dan survei fisik lapangan.</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 10px;">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<div class="content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
    <!-- Main Left Area -->
    <div class="section-stack" style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Status Card -->
        <div class="detail-card" style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden;">
            <div class="pulse-decor" style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--accent-blue);"></div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 16px; color: var(--text-primary); display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-clock" style="color: var(--accent-blue); margin-right: 6px;"></i> Agenda Konsultasi & Survei Fisik</span>
                <span class="status-badge" style="background: rgba(59, 130, 246, 0.1); border: 1.5px solid rgba(59, 130, 246, 0.3); color: var(--accent-blue); padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; animation: border-pulse 2s infinite;">
                    KONSULTASI AKTIF
                </span>
            </h3>

            <div style="background: rgba(59, 130, 246, 0.03); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Tanggal Pertemuan</span>
                        <p style="font-weight: 700; font-size: 1rem; color: var(--text-primary); margin: 4px 0 0 0;">
                            <i class="far fa-calendar-alt" style="color: var(--accent-blue);"></i> {{ $order->consultation_date?->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Waktu Pertemuan</span>
                        <p style="font-weight: 700; font-size: 1rem; color: var(--text-primary); margin: 4px 0 0 0;">
                            <i class="far fa-clock" style="color: var(--accent-blue);"></i> {{ $order->consultation_time }}
                        </p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Lokasi / Tempat</span>
                        <p style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary); margin: 4px 0 0 0;">
                            <i class="fas fa-map-marker-alt" style="color: var(--accent-red);"></i> {{ $order->consultation_place ?: 'Lokasi Proyek' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Catatan Admin & Update Form -->
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="scheduled">
                <input type="hidden" name="is_consultation_confirmed" value="1">
                
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label" style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 8px; display: block;">Catatan Diskusi / Admin</label>
                    <textarea name="admin_notes" class="form-control" rows="4" placeholder="Tulis catatan penyesuaian, hasil diskusi awal, spesifikasi request model, dll. Catatan ini juga dapat dilihat pelanggan di halaman mereka..." style="width: 100%; border-radius: 8px; border-color: var(--border-color);">{{ $order->admin_notes }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-sync-alt"></i> Simpan Catatan
                </button>
            </form>
        </div>

        <!-- Order details summary -->
        <div class="detail-card">
            <h3><i class="fas fa-file-invoice"></i> Ringkasan Pemesanan</h3>
            <div class="detail-row">
                <span class="detail-label">No. Pesanan</span>
                <span class="detail-value table-primary">{{ $order->order_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Layanan</span>
                <span class="detail-value">{{ $order->service->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Budget Pelanggan</span>
                <span class="detail-value">
                    @if($order->budget_range)
                        <span style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #10b981; font-weight: 700; padding: 2px 10px; border-radius: 12px; font-size: 0.85rem;">
                            {{ $order->budget_range }}
                        </span>
                    @else
                        <span style="color: var(--text-muted); font-style: italic;">Belum diisi pelanggan</span>
                    @endif
                </span>
            </div>
            <div style="border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: 15px;">
                <span class="detail-label" style="font-weight: 700; display: block; margin-bottom: 8px;">Deskripsi Kebutuhan Pelanggan:</span>
                <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.9rem;">{{ $order->description }}</p>
            </div>
            @if($order->notes)
            <div style="margin-top: 15px; background: rgba(30, 41, 59, 0.02); border-radius: 8px; padding: 12px; border-left: 3.5px solid #64748b;">
                <strong>Catatan Tambahan Pelanggan:</strong><br>
                <span style="font-size: 0.88rem; color: var(--text-secondary);">{{ $order->notes }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar Right Area (Actions & Client Info) -->
    <div class="section-stack" style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Process Complete Card -->
        <div class="summary-card" style="border: 2px solid var(--accent-blue); background: linear-gradient(180deg, var(--surface) 0%, rgba(59, 130, 246, 0.02) 100%); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow-md);">
            <h3 class="panel-title" style="color: var(--accent-blue); font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 12px;">
                <i class="fas fa-handshake"></i> Penyelesaian Konsultasi
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 20px;">
                Jika diskusi selesai dan kedua pihak telah sepakat dengan spesifikasi dan jadwal pengerjaan, selesaikan konsultasi ini untuk masuk ke tahap **Penjadwalan Proyek**.
            </p>
            
            <a href="{{ route('admin.orders.scheduling', $order) }}" class="btn btn-primary" style="width: 100%; text-align: center; justify-content: center; font-weight: 700; background: #3b82f6; border-color: #3b82f6; color: white; display: inline-flex; align-items: center; gap: 8px; padding: 12px; border-radius: 10px; text-decoration: none; transition: background 0.2s;">
                <i class="fas fa-calendar-alt"></i> Selesaikan & Jadwalkan Proyek
            </a>
        </div>

        <!-- Client Info Card -->
        <div class="detail-card">
            <h3><i class="fas fa-user-circle"></i> Kontak Pelanggan</h3>
            <div class="detail-row" style="flex-direction: column; align-items: start; gap: 4px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px; margin-bottom: 12px;">
                <span class="detail-label" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Nama</span>
                <span class="detail-value" style="font-weight: 600;">{{ $order->name }}</span>
            </div>
            <div class="detail-row" style="flex-direction: column; align-items: start; gap: 4px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px; margin-bottom: 12px;">
                <span class="detail-label" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Telepon</span>
                <span class="detail-value" style="font-weight: 600;">{{ $order->phone }}</span>
            </div>
            <div class="detail-row" style="flex-direction: column; align-items: start; gap: 4px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px; margin-bottom: 12px;">
                <span class="detail-label" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Email</span>
                <span class="detail-value">{{ $order->email }}</span>
            </div>
            <div class="detail-row" style="flex-direction: column; align-items: start; gap: 4px;">
                <span class="detail-label" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Alamat Proyek</span>
                <span class="detail-value" style="line-height: 1.4; font-size: 0.88rem;">{{ $order->address }}</span>
            </div>

            <!-- WhatsApp Link -->
            @php
                $cleanPhone = preg_replace('/\D+/', '', $order->phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }
                $waText = "Halo " . $order->name . " 👋\n\n"
                    . "Kami dari tim *WeldTrack* mengucapkan terima kasih telah mempercayakan proyek pengelasan Anda kepada kami. 🙏\n\n"
                    . "Dengan senang hati kami informasikan bahwa pesanan dan jadwal konsultasi Anda telah *DITERIMA* dan dikonfirmasi oleh pihak admin kami.\n\n"
                    . "📋 *Detail Pesanan:*\n"
                    . "• No. Pesanan: *" . $order->order_number . "*\n"
                    . "• Layanan: *" . $order->service->name . "*\n"
                    . ($order->consultation_date ? "• Jadwal Pertemuan: *" . $order->consultation_date->translatedFormat('d F Y') . "* pukul *" . $order->consultation_time . "*\n" : "")
                    . ($order->consultation_place ? "• Lokasi: *" . $order->consultation_place . "*\n" : "")
                    . "\nMohon berkenan untuk menunggu kehadiran tim kami pada waktu yang telah disepakati. Tim kami akan hadir tepat waktu untuk melakukan survei dan konsultasi langsung di lokasi Anda.\n\n"
                    . "Jika ada pertanyaan atau perlu koordinasi lebih lanjut, jangan ragu untuk menghubungi kami kembali.\n\n"
                    . "Terima kasih & salam hangat,\n"
                    . "*Tim WeldTrack* 🔧";
                $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . rawurlencode($waText);
            @endphp
            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; margin-top: 15px; background: #25d366; border-color: #25d366; color: white;">
                <i class="fab fa-whatsapp"></i> Chat Pelanggan (WhatsApp)
            </a>
        </div>

        <!-- Rejection card (just in case they don't reach agreement) -->
        <div class="summary-card" style="border-color: #fecaca; background: #fff8f8; padding: 20px;">
            <h3 class="panel-title" style="color: var(--accent-red); margin-bottom: 8px;"><i class="fas fa-ban"></i> Batalkan / Tolak Pesanan</h3>
            <p style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 12px;">
                Jika diskusi selesai namun kesepakatan harga/spesifikasi tidak tercapai, Anda dapat membatalkan pesanan.
            </p>
            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                @csrf
                <div class="form-group" style="margin-bottom: 12px;">
                    <textarea name="rejection_reason" class="form-control" placeholder="Tulis alasan penolakan..." required style="font-size: 0.82rem; height: 70px;"></textarea>
                </div>
                <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; background: var(--accent-red); color: white; border: none;">
                    <i class="fas fa-times-circle"></i> Batalkan Pesanan
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes border-pulse {
        0%, 100% {
            border-color: rgba(59, 130, 246, 0.3);
        }
        50% {
            border-color: rgba(59, 130, 246, 0.8);
        }
    }
</style>
@endsection
