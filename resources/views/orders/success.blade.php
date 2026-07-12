@extends('layouts.app')

@section('title', 'Pesanan Berhasil - WeldTrack')

@section('content')
<section class="success-section">
    <div class="container">
        <div class="fade-in">
            <div class="success-icon {{ $order->status === \App\Models\Order::STATUS_QUEUED ? 'success-icon-queued' : '' }}">
                <i class="fas {{ $order->status === \App\Models\Order::STATUS_QUEUED ? 'fa-hourglass-half' : 'fa-check' }}"></i>
            </div>
            <h1>Pesanan Berhasil Dibuat!</h1>
            @if($order->status === \App\Models\Order::STATUS_QUEUED)
            <p>Pesanan Anda sudah tercatat dan saat ini masuk ke dalam antrean pengerjaan.</p>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Tim kami tetap akan menghubungi Anda untuk konfirmasi, lalu pesanan diproses sesuai urutan antrean.</p>
            @else
            <p>Terima kasih telah mempercayakan proyek Anda kepada WeldTrack.</p>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Tim kami akan segera menghubungi Anda untuk konfirmasi dan survei lokasi.</p>
            @endif

            <div class="order-detail-box">
                <div class="order-detail-row">
                    <span class="order-detail-label">No. Pesanan</span>
                    <span class="order-detail-value" style="color: var(--accent-blue);">{{ $order->order_number }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Layanan</span>
                    <span class="order-detail-value">{{ $order->service->name }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Nama</span>
                    <span class="order-detail-value">{{ $order->name }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Email</span>
                    <span class="order-detail-value">{{ $order->email }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Telepon</span>
                    <span class="order-detail-value">{{ $order->phone }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Status</span>
                    <span class="order-detail-value" style="color: {{ $order->status_color }};">{{ $order->status_label }}</span>
                </div>
                @if($order->status === \App\Models\Order::STATUS_PENDING && $order->preferred_consultation_date)
                <div class="order-detail-row">
                    <span class="order-detail-label">Usulan Jadwal Anda</span>
                    <span class="order-detail-value">
                        {{ $order->preferred_consultation_date->translatedFormat('d F Y') }} ({{ $order->preferred_consultation_time }})
                    </span>
                </div>
                @endif
                @if($order->status === \App\Models\Order::STATUS_QUEUED)
                <div class="order-detail-row">
                    <span class="order-detail-label">Posisi Antrean</span>
                    <span class="order-detail-value">#{{ $order->queue_position }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Pesanan di Depan</span>
                    <span class="order-detail-value">{{ $order->orders_ahead_count }} pesanan</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Estimasi Tunggu</span>
                    <span class="order-detail-value">{{ $order->estimated_wait_label }}</span>
                </div>
                @endif
            </div>

            {{-- Kebutuhan Proyek (Catatan Spesifikasi) --}}
            @if($order->project_requirements)
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 25px 30px; box-shadow: var(--shadow-sm); margin-top: 25px; margin-bottom: 25px; text-align: left; border-left: 5px solid #3b82f6;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                    <div style="background: rgba(59, 130, 246, 0.1); width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.1rem; flex-shrink: 0;">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Catatan Kebutuhan Proyek</h3>
                        <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Detail spesifikasi bahan & dimensi yang disepakati</p>
                    </div>
                </div>
                <div style="font-size: 0.9rem; line-height: 1.7; color: var(--text-secondary); white-space: pre-line;">{{ $order->project_requirements }}</div>
            </div>
            @endif

            {{-- Foto Referensi Model --}}
            @if($order->referencePhotos->isNotEmpty())
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 25px 30px; box-shadow: var(--shadow-sm); margin-top: 25px; margin-bottom: 25px; text-align: left; border-left: 5px solid #f59e0b;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                    <div style="background: rgba(245, 158, 11, 0.1); width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.1rem; flex-shrink: 0;">
                        <i class="fas fa-images"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Foto Referensi Model</h3>
                        <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Model/desain acuan yang Anda berikan kepada kami</p>
                    </div>
                </div>
                <div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px;">
                        @foreach($order->referencePhotos as $photo)
                        <div>
                            <img src="{{ $photo->photo_url }}"
                                 alt="{{ $photo->caption ?? 'Foto Referensi' }}"
                                 onclick="window.open('{{ $photo->photo_url }}', '_blank')"
                                 style="width: 100%; height: 130px; object-fit: cover; border-radius: 10px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.2s, transform 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                 onmouseover="this.style.borderColor='#f59e0b'; this.style.transform='scale(1.04)'"
                                 onmouseout="this.style.borderColor='transparent'; this.style.transform='scale(1)'">
                            @if($photo->caption)
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 6px 2px 0; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $photo->caption }}">{{ $photo->caption }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 14px; margin-bottom: 0;">
                        <i class="fas fa-info-circle"></i> Klik foto untuk memperbesar
                    </p>
                </div>
            </div>
            @endif

            @php
                $waPhone = config('app.admin_whatsapp', '6287865410555');
                $cleanWaPhone = preg_replace('/\D+/', '', $waPhone);
                if (str_starts_with($cleanWaPhone, '0')) $cleanWaPhone = '62' . substr($cleanWaPhone, 1);
            @endphp

            {{-- WhatsApp Notification Status Box --}}
            <div style="margin-top: 28px; background: linear-gradient(135deg, rgba(37,211,102,0.08) 0%, rgba(18,140,67,0.08) 100%); border: 1.5px solid rgba(37,211,102,0.35); border-radius: 16px; padding: 24px 28px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                    <div style="width: 44px; height: 44px; background: #25d366; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                        <i class="fab fa-whatsapp" style="color: #fff;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 1rem; color: #25d366;">Notifikasi WhatsApp Otomatis Terkirim</div>
                        <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 2px;">Rincian pesanan telah dikirim ke WA Anda & WA Admin secara otomatis</div>
                    </div>
                </div>
                <div style="background: rgba(0,0,0,0.15); border-radius: 10px; padding: 14px 16px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 16px; border: 1px solid rgba(37,211,102,0.15);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <i class="fas fa-check-circle" style="color: #25d366;"></i>
                        <span>WA ke <strong>Anda</strong> — ringkasan pesanan & instruksi selanjutnya</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle" style="color: #25d366;"></i>
                        <span>WA ke <strong>Admin</strong> — detail pesanan lengkap untuk ditindaklanjuti</span>
                    </div>
                </div>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">
                    <i class="fas fa-info-circle"></i>
                    Admin akan menghubungi Anda untuk konfirmasi jadwal konsultasi. Anda juga dapat memantau status pesanan di halaman <strong>Pesanan Saya</strong>. Hubungi admin di bawah ini jika ada pertanyaan.
                </p>
                <a href="https://wa.me/{{ $cleanWaPhone }}" target="_blank" rel="noopener"
                   style="display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(37,211,102,0.15); color: #25d366; text-decoration: none; font-weight: 700; font-size: 0.95rem; padding: 12px 24px; border-radius: 10px; border: 1.5px solid rgba(37,211,102,0.4); transition: all 0.2s;"
                   onmouseover="this.style.background='#25d366'; this.style.color='#fff';"
                   onmouseout="this.style.background='rgba(37,211,102,0.15)'; this.style.color='#25d366';">
                    <i class="fab fa-whatsapp" style="font-size: 1.2rem;"></i>
                    Chat Admin (Tanya Sesuatu)
                </a>
            </div>

            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
                <a href="{{ route('order.index') }}" class="btn btn-primary">
                    <i class="fas fa-list-check"></i> Pesanan Saya
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Layanan Lain
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
