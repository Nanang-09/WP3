@extends('layouts.app')

@section('title', 'Proses Konsultasi Proyek - WeldTrack')

@section('content')
<section class="success-section" style="padding: 60px 0; background: linear-gradient(180deg, rgba(30, 41, 59, 0.02) 0%, rgba(30, 41, 59, 0) 100%);">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
        <div class="fade-in">
            <!-- Header Section -->
            <div class="success-header" style="text-align: center; margin-bottom: 40px;">
                <div class="pulse-container" style="position: relative; display: inline-block; margin-bottom: 20px;">
                    <div class="success-icon" style="width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="fas fa-comments-alt" style="color: var(--accent-blue); font-size: 2.2rem; animation: float 3s ease-in-out infinite;"></i>
                    </div>
                    <span class="pulsing-dot" style="position: absolute; bottom: 4px; right: 4px; width: 16px; height: 16px; background: #3b82f6; border-radius: 50%; border: 3px solid var(--surface); box-shadow: 0 0 10px rgba(59, 130, 246, 0.6); animation: pulse 2s infinite;"></span>
                </div>
                <h1 style="font-family: var(--font-heading); font-size: 2rem; font-weight: 800; color: var(--text-primary); margin-bottom: 10px;">Proses Konsultasi Sedang Berlangsung</h1>
                <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1rem; line-height: 1.6;">
                    Tim ahli WeldTrack sedang berkoordinasi dan melakukan survei/pertemuan fisik untuk mendiskusikan kebutuhan pengelasan Anda secara langsung.
                </p>
            </div>

            <!-- Interactive Progress/Milestone Stepper -->
            <div class="stepper-card" style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-md); margin-bottom: 30px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-route" style="color: var(--accent-blue);"></i> Status Alur Transaksi
                </h3>
                <div class="stepper-wrapper" style="display: flex; justify-content: space-between; position: relative; flex-wrap: wrap; gap: 20px;">
                    <!-- Line connector for desktop -->
                    <div class="stepper-line" style="position: absolute; top: 22px; left: 40px; right: 40px; height: 3px; background: #e2e8f0; z-index: 1;"></div>
                    
                    <!-- Step 1 -->
                    <div class="step-item" style="flex: 1; text-align: center; z-index: 2; min-width: 120px;">
                        <div class="step-circle" style="width: 44px; height: 44px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid var(--surface); box-shadow: 0 0 0 2px #10b981;">
                            <i class="fas fa-check" style="font-size: 0.9rem;"></i>
                        </div>
                        <span style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); display: block;">Pengajuan Pesanan</span>
                        <small style="color: var(--text-muted); font-size: 0.72rem;">Sukses dikirim</small>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-item" style="flex: 1; text-align: center; z-index: 2; min-width: 120px;">
                        <div class="step-circle" style="width: 44px; height: 44px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid var(--surface); box-shadow: 0 0 0 2px #10b981;">
                            <i class="fas fa-check" style="font-size: 0.9rem;"></i>
                        </div>
                        <span style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); display: block;">Jadwal Disepakati</span>
                        <small style="color: var(--text-muted); font-size: 0.72rem;">Waktu survei deal</small>
                    </div>

                    <!-- Step 3 (Active) -->
                    <div class="step-item" style="flex: 1; text-align: center; z-index: 2; min-width: 120px;">
                        <div class="step-circle" style="width: 44px; height: 44px; border-radius: 50%; background: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid var(--surface); box-shadow: 0 0 0 2px #3b82f6; animation: pulse-shadow 2.5s infinite;">
                            3
                        </div>
                        <span style="font-weight: 700; font-size: 0.85rem; color: var(--accent-blue); display: block;">Proses Konsultasi</span>
                        <small style="color: var(--accent-blue); font-weight: 600; font-size: 0.72rem;">Sedang Berlangsung</small>
                    </div>

                    <!-- Step 4 -->
                    <div class="step-item" style="flex: 1; text-align: center; z-index: 2; min-width: 120px;">
                        <div class="step-circle" style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; color: var(--text-muted); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid var(--surface); box-shadow: 0 0 0 2px #e2e8f0;">
                            4
                        </div>
                        <span style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted); display: block;">Monitoring Proyek</span>
                        <small style="color: var(--text-muted); font-size: 0.72rem;">Langkah Berikutnya</small>
                    </div>
                </div>
            </div>

            <!-- Meeting Info Card -->
            <div class="order-detail-box" style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px; position: relative;">
                <h3 style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); border-bottom: 1.5px dashed var(--border-color); padding-bottom: 15px;">
                    <i class="far fa-calendar-check" style="color: var(--accent-blue); margin-right: 6px;"></i> Detail Agenda Pertemuan & Konsultasi
                </h3>

                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">No. Pesanan</span>
                    <span class="order-detail-value" style="color: var(--accent-blue); font-weight: 700;">{{ $order->order_number }}</span>
                </div>

                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Layanan</span>
                    <span class="order-detail-value" style="font-weight: 600; color: var(--text-primary);">{{ $order->service->name }}</span>
                </div>

                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Waktu Konsultasi</span>
                    <span class="order-detail-value" style="font-weight: 700; color: #1e293b;">
                        <i class="far fa-calendar-alt" style="color: var(--accent-blue); margin-right: 4px;"></i> {{ $order->consultation_date?->translatedFormat('d F Y') }} <br>
                        <i class="far fa-clock" style="color: var(--accent-blue); margin-right: 4px;"></i> Pukul {{ $order->consultation_time }}
                    </span>
                </div>

                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Tempat Pertemuan</span>
                    <span class="order-detail-value" style="font-weight: 600; color: var(--text-primary);">
                        <i class="fas fa-map-marker-alt" style="color: var(--accent-red); margin-right: 4px;"></i> {{ $order->consultation_place ?: $order->address }}
                    </span>
                </div>

                @if($order->budget_range)
                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Perkiraan Budget</span>
                    <span class="order-detail-value">
                        <span style="display: inline-block; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #10b981; font-weight: 700; padding: 4px 12px; border-radius: 20px; font-size: 0.88rem;">
                            {{ $order->budget_range }}
                        </span>
                    </span>
                </div>
                @endif

                @if($order->estimated_cost > 0 || $order->project_price > 0)
                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Estimasi Biaya Proyek</span>
                    <span class="order-detail-value" style="font-weight: 700; color: #10b981;">
                        Rp {{ number_format($order->project_price ?: $order->estimated_cost, 0, ',', '.') }}
                    </span>
                </div>
                @endif

                @if($order->materials->count() > 0)
                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Kebutuhan Bahan</span>
                    <div class="order-detail-value" style="width: 100%;">
                        <table style="width: 100%; font-size: 0.85rem; border-collapse: collapse; background: rgba(0,0,0,0.01); border: 1px solid var(--border-color); border-radius: 8px;">
                            <thead>
                                <tr style="border-bottom: 1px dashed var(--border-color); text-align: left;">
                                    <th style="padding: 8px; color: var(--text-secondary);">Bahan</th>
                                    <th style="padding: 8px; color: var(--text-secondary);">Spek</th>
                                    <th style="padding: 8px; color: var(--text-secondary); text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->materials as $mat)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                                    <td style="padding: 8px; font-weight: 600;">{{ $mat->material_name }}</td>
                                    <td style="padding: 8px; color: var(--text-muted);">{{ trim($mat->length . ' ' . $mat->shape) ?: '-' }}</td>
                                    <td style="padding: 8px; text-align: center;">{{ $mat->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($order->admin_notes)
                <div class="order-detail-row" style="display: flex; margin-bottom: 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); padding-bottom: 12px;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Catatan Tambahan Admin</span>
                    <span class="order-detail-value" style="font-style: italic; color: #475569;">{{ $order->admin_notes }}</span>
                </div>
                @endif

                <div class="order-detail-row" style="display: flex;">
                    <span class="order-detail-label" style="font-weight: 600; color: var(--text-secondary); width: 220px; flex-shrink: 0;">Alamat Lokasi Proyek</span>
                    <span class="order-detail-value" style="color: var(--text-secondary); line-height: 1.5;">{{ $order->address }}</span>
                </div>
            </div>

            <!-- Guide Card for Customer -->
            <div class="guide-card" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.03) 0%, rgba(59, 130, 246, 0.08) 100%); border: 1.5px solid rgba(59, 130, 246, 0.25); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
                <h4 style="font-weight: 700; color: var(--accent-blue); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; font-size: 1rem;">
                    <i class="fas fa-info-circle"></i> Apa yang terjadi selama proses konsultasi?
                </h4>
                <ul style="padding-left: 20px; margin: 0; color: var(--text-secondary); font-size: 0.88rem; line-height: 1.7;">
                    <li style="margin-bottom: 8px;"><strong>Survei Lapangan:</strong> Owner kami akan memeriksa lokasi untuk mengukur dimensi pengerjaan secara akurat.</li>
                    <li style="margin-bottom: 8px;"><strong>Diskusi Desain & Material:</strong> Anda dapat mendiskusikan opsi model pengelasan, tipe besi/baja, ketebalan, dan finishing terbaik.</li>
                    <li style="margin-bottom: 8px;"><strong>Penjadwalan Proyek:</strong> Setelah kesepakatan tercapai, admin kami akan memasukkan jadwal resmi proyek dan menugaskan mandor lapangan ke pesanan Anda.</li>
                    <li>Setelah dijadwalkan oleh admin, halaman ini akan otomatis berubah menjadi halaman <strong>Monitoring Proyek</strong>, di mana Anda bisa memantau update foto progres secara berkala dari mandor.</li>
                </ul>
            </div>

            <!-- WhatsApp Live Chat Quick Link -->
            @php
                $waPhone = config('app.admin_whatsapp', '6287865410555');
                $waText = "Halo Admin WeldTrack 👋\n\nSaya ingin menanyakan perihal jalannya proses survei/konsultasi untuk pesanan saya:\nNo. Pesanan: *" . $order->order_number . "*\nLayanan: *" . $order->service->name . "*";
                $waUrl = "https://wa.me/" . $waPhone . "?text=" . rawurlencode($waText);
            @endphp

            <div style="background: linear-gradient(135deg, rgba(37, 211, 102, 0.08) 0%, rgba(18, 140, 67, 0.08) 100%); border: 1.5px solid rgba(37, 211, 102, 0.35); border-radius: 20px; padding: 25px; text-align: center; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 12px;">
                    <i class="fab fa-whatsapp" style="color: #25d366; font-size: 1.8rem;"></i>
                    <strong style="font-size: 1.05rem; color: #15803d; font-weight: 700;">Butuh koordinasi cepat dengan tim admin?</strong>
                </div>
                <p style="font-size: 0.85rem; color: #166534; margin-bottom: 16px; max-width: 500px; margin-inline: auto;">
                    Hubungi kami langsung melalui WhatsApp untuk koordinasi perubahan jadwal survei atau menanyakan posisi tim survei kami.
                </p>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   style="display: inline-flex; align-items: center; gap: 8px; background: #25d366; color: #fff; text-decoration: none; font-weight: 700; font-size: 0.95rem; padding: 12px 24px; border-radius: 10px; transition: all 0.2s; box-shadow: 0 4px 10px rgba(37, 211, 102, 0.2);"
                   onmouseover="this.style.background='#128c43'" onmouseout="this.style.background='#25d366'">
                    <i class="fab fa-whatsapp" style="font-size: 1.2rem;"></i>
                    Hubungi Admin via WhatsApp
                </a>
            </div>

            <!-- Footer Action Buttons -->
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                @if($canManageAsCustomer ?? false)
                <a href="{{ route('order.index') }}" class="btn btn-secondary" style="border-radius: 10px;">
                    <i class="fas fa-list-check"></i> Semua Pesanan Saya
                </a>
                @endif
                <a href="{{ route('home') }}" class="btn btn-outline" style="border-radius: 10px;">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Inline animations -->
<style>
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }
    @keyframes pulse-shadow {
        0% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }
    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }
    
    /* Responsive adjustment for stepper and columns */
    @media (max-width: 600px) {
        .stepper-line {
            display: none;
        }
        .order-detail-row {
            flex-direction: column;
            gap: 4px;
        }
        .order-detail-row span {
            width: 100% !important;
        }
    }
</style>
@endsection
