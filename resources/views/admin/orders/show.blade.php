@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-clipboard-list"></i> Detail Pesanan</h2>
        <p class="page-subtitle">{{ $order->order_number }} &mdash; {{ $order->service->name }}</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('admin.orders.history', ['search' => $order->email]) }}" class="btn btn-secondary btn-sm" title="Lihat semua riwayat pesanan pelanggan ini">
            <i class="fas fa-history"></i> Riwayat Pelanggan
        </a>
        <a href="{{ route('order.success', $order) }}" class="btn btn-secondary btn-sm" target="_blank" rel="noopener">
            <i class="fas fa-eye"></i> Tampilan Pelanggan
        </a>
        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini secara permanen?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </form>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 20px; padding: 14px 18px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; color: #166534; display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-check-circle" style="font-size: 1.1rem;"></i>
    {{ session('success') }}
</div>
@endif

<div class="content-grid" style="grid-template-columns: 1fr 380px; gap: 24px;">

    {{-- LEFT: Customer Info --}}
    <div>
        {{-- Status Banner --}}
        <div class="detail-card" style="
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid #334155;
            margin-bottom: 20px;
            padding: 20px 24px;
        ">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <p style="color: #94a3b8; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">Status Saat Ini</p>
                    <span class="status-badge status-{{ $order->status }}" style="font-size: 0.95rem; padding: 6px 16px;">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div style="text-align: right;">
                    <p style="color: #94a3b8; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Tanggal Pesanan</p>
                    <p style="color: #e2e8f0; font-weight: 600; font-size: 0.9rem;">{{ $order->created_at->format('d F Y, H:i') }}</p>
                </div>
            </div>

            {{-- Progress Steps --}}
            <div style="margin-top: 20px; display: flex; align-items: center; gap: 0;">
                @php
                    $steps = [
                        ['status' => 'scheduled', 'label' => 'Dijadwalkan', 'icon' => 'fa-calendar-check'],
                        ['status' => 'confirmed', 'label' => 'Dikonfirmasi', 'icon' => 'fa-handshake'],
                        ['status' => 'in_progress', 'label' => 'Dikerjakan', 'icon' => 'fa-hard-hat'],
                        ['status' => 'completed', 'label' => 'Selesai', 'icon' => 'fa-check-double'],
                    ];
                    $statusOrder = ['pending', 'scheduled', 'confirmed', 'in_progress', 'completed'];
                    $currentIdx = array_search($order->status, $statusOrder);
                @endphp
                @foreach($steps as $i => $step)
                    @php
                        $stepIdx = array_search($step['status'], $statusOrder);
                        $done = $currentIdx !== false && $currentIdx >= $stepIdx;
                        $active = $order->status === $step['status'];
                    @endphp
                    <div style="display: flex; align-items: center; flex: 1;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 5px; flex: 0 0 auto;">
                            <div style="
                                width: 34px; height: 34px; border-radius: 50%;
                                background: {{ $done ? '#10b981' : ($active ? '#3b82f6' : '#334155') }};
                                display: flex; align-items: center; justify-content: center;
                                font-size: 0.85rem; color: white;
                                box-shadow: {{ $active ? '0 0 0 4px rgba(59,130,246,0.3)' : 'none' }};
                                transition: all 0.3s;
                            ">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </div>
                            <span style="color: {{ $done ? '#10b981' : '#64748b' }}; font-size: 0.68rem; font-weight: {{ $active ? '700' : '500' }}; white-space: nowrap;">{{ $step['label'] }}</span>
                        </div>
                        @if($i < count($steps) - 1)
                            <div style="flex: 1; height: 2px; background: {{ $done && $currentIdx > $stepIdx ? '#10b981' : '#334155' }}; margin: 0 4px; margin-bottom: 18px; transition: background 0.3s;"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Customer Identity --}}
        <div class="detail-card" style="margin-bottom: 20px;">
            <h3><i class="fas fa-user"></i> Data Pelanggan</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0;">
                <div class="detail-row">
                    <span class="detail-label">Nama</span>
                    <span class="detail-value" style="font-weight: 700;">{{ $order->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Telepon</span>
                    <span class="detail-value">
                        @php
                            $phone = preg_replace('/\D+/', '', $order->phone);
                            if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                        @endphp
                        <a href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener" style="color: #25d366; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fab fa-whatsapp"></i> {{ $order->phone }}
                        </a>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $order->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-wallet" style="color:#10b981; margin-right:4px;"></i> Budget</span>
                    <span class="detail-value">
                        @if($order->budget_range)
                            <span style="background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); color:#10b981; font-weight:700; padding:3px 12px; border-radius:20px; font-size:0.88rem; display: inline-block;">
                                {{ $order->budget_range }}
                            </span>
                        @else
                            <span style="color:var(--text-muted); font-style:italic;">Belum diisi</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="detail-row" style="border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 12px;">
                <span class="detail-label"><i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 4px;"></i> Alamat Proyek</span>
                <span class="detail-value" style="line-height: 1.6;">{{ $order->address }}</span>
            </div>
        </div>

        {{-- Consultation Schedule (display only) --}}
        @if($order->consultation_date)
        <div class="detail-card" style="margin-bottom: 20px; border-left: 4px solid #3b82f6;">
            <h3 style="color: #3b82f6;"><i class="fas fa-calendar-check"></i> Jadwal Konsultasi</h3>
            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 4px;">
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal</p>
                    <p style="font-weight: 700; font-size: 1rem;">{{ $order->consultation_date->translatedFormat('d F Y') }}</p>
                </div>
                @if($order->consultation_time)
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Waktu</p>
                    <p style="font-weight: 700; font-size: 1rem;">{{ $order->consultation_time }}</p>
                </div>
                @endif
                @if($order->consultation_place)
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Tempat</p>
                    <p style="font-weight: 700; font-size: 1rem;">{{ $order->consultation_place }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Project Dates (display only when available) --}}
        @if($order->project_start_date)
        <div class="detail-card" style="border-left: 4px solid #10b981;">
            <h3 style="color: #10b981;"><i class="fas fa-hard-hat"></i> Jadwal Pengerjaan</h3>
            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 4px;">
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Mulai</p>
                    <p style="font-weight: 700; font-size: 1rem;">{{ $order->project_start_date->translatedFormat('d F Y') }}</p>
                </div>
                @if($order->project_end_date)
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Selesai Target</p>
                    <p style="font-weight: 700; font-size: 1rem;">{{ $order->project_end_date->translatedFormat('d F Y') }}</p>
                </div>
                @endif
                @if($order->project_price)
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Harga Kesepakatan</p>
                    <p style="font-weight: 700; font-size: 1rem; color: #10b981;">Rp {{ number_format($order->project_price, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: Action Panel --}}
    <div>

        {{-- Next Action Hint --}}
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1.5px solid #93c5fd; border-radius: 10px; padding: 16px 18px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <div style="background: #3b82f6; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0;">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <strong style="color: #1e40af; font-size: 0.9rem;">Tindakan Selanjutnya</strong>
            </div>
            <p style="margin: 0; color: #1d4ed8; font-size: 0.85rem; line-height: 1.6;">
                @if($order->status === \App\Models\Order::STATUS_PENDING)
                    Pesanan baru masuk! Jadwalkan konsultasi dengan pelanggan menggunakan form di bawah, lalu ubah status ke <strong>Konsultasi Dijadwalkan</strong>.
                @elseif($order->status === \App\Models\Order::STATUS_SCHEDULED)
                    Jadwal konsultasi sudah ditetapkan. Setelah konsultasi berlangsung dan kesepakatan tercapai, ubah status ke <strong>Dikonfirmasi</strong>.
                @elseif($order->status === \App\Models\Order::STATUS_CONFIRMED)
                    Kesepakatan telah tercapai. Mulai pengerjaan proyek dan ubah status ke <strong>Sedang Dikerjakan</strong>.
                @elseif($order->status === \App\Models\Order::STATUS_IN_PROGRESS)
                    Proyek sedang dikerjakan. Setelah selesai sepenuhnya, ubah status ke <strong>Selesai</strong>.
                @elseif($order->status === \App\Models\Order::STATUS_COMPLETED)
                    ✅ Proyek ini telah <strong>selesai</strong>. Tidak ada tindakan lanjutan.
                @else
                    Tinjau pesanan dan hubungi pelanggan jika diperlukan.
                @endif
            </p>
        </div>

        {{-- Status & Schedule Form --}}
        <div class="summary-card">
            <h3 class="panel-title"><i class="fas fa-edit"></i> Update Status</h3>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Status Pesanan</label>
                    <select name="status" class="form-control" id="order-status-select">
                        <option value="scheduled" {{ $order->status == 'scheduled' ? 'selected' : '' }}>📅 Konsultasi Dijadwalkan</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Dikonfirmasi</option>
                        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>🔧 Sedang Dikerjakan</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>🏁 Selesai</option>
                    </select>
                </div>

                {{-- Consultation Fields (visible when scheduled) --}}
                <div id="consultation-fields" style="display: {{ $order->status === 'scheduled' ? 'block' : 'none' }}; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 14px; margin-top: 4px; margin-bottom: 12px;">
                    <p style="font-size: 0.8rem; font-weight: 700; color: #0369a1; margin-bottom: 10px;"><i class="fas fa-calendar-alt"></i> Detail Jadwal Konsultasi</p>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="consultation_date" class="form-control" value="{{ old('consultation_date', optional($order->consultation_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Waktu</label>
                        <input type="text" name="consultation_time" class="form-control" value="{{ old('consultation_time', $order->consultation_time) }}" placeholder="Cth: 13:00 - 15:00">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tempat</label>
                        <input type="text" name="consultation_place" class="form-control" value="{{ old('consultation_place', $order->consultation_place) }}" placeholder="Lokasi pertemuan">
                    </div>
                </div>

                {{-- Project Fields (visible when confirmed/in_progress/completed) --}}
                <div id="project-fields" style="display: {{ in_array($order->status, ['confirmed', 'in_progress', 'completed']) ? 'block' : 'none' }}; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 14px; margin-top: 4px; margin-bottom: 12px;">
                    <p style="font-size: 0.8rem; font-weight: 700; color: #166534; margin-bottom: 10px;"><i class="fas fa-handshake"></i> Detail Pengerjaan Proyek</p>
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="project_start_date" class="form-control" value="{{ old('project_start_date', optional($order->project_start_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Selesai</label>
                        <input type="date" name="project_end_date" class="form-control" value="{{ old('project_end_date', optional($order->project_end_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Harga Kesepakatan (Rp)</label>
                        <input type="number" name="project_price" class="form-control" value="{{ old('project_price', $order->project_price) }}" placeholder="Harga fix yang disepakati">
                    </div>
                </div>

                {{-- Foreman Assignment --}}
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-hard-hat" style="color: #f59e0b;"></i> Mandor Lapangan</label>
                    <select name="foreman_id" class="form-control">
                        <option value="">— Belum ditugaskan —</option>
                        @foreach($foremen as $foreman)
                            <option value="{{ $foreman->id }}" {{ (string) $order->foreman_id === (string) $foreman->id ? 'selected' : '' }}>
                                {{ $foreman->name }}{{ $foreman->phone ? ' · ' . $foreman->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Catatan Admin --}}
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-sticky-note" style="color: #3b82f6;"></i> Catatan Admin (Dilihat Pelanggan)</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Tambahkan informasi atau catatan tambahan untuk pelanggan..." style="font-size: 0.85rem;">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem; font-weight: 700; letter-spacing: 0.3px;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Reject Order (only for pending) --}}
        @if($order->status === \App\Models\Order::STATUS_PENDING || $order->status === \App\Models\Order::STATUS_SCHEDULED)
        <div class="summary-card" style="margin-top: 16px; border-color: #fecaca; background: #fff8f8;">
            <h3 class="panel-title" style="color: var(--accent-red);"><i class="fas fa-ban"></i> Tolak Pesanan</h3>
            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pesanan ini?')">
                @csrf
                <div class="form-group">
                    <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Tulis alasan penolakan..." required></textarea>
                </div>
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <i class="fas fa-times-circle"></i> Tolak Pesanan
                </button>
            </form>
        </div>
        @endif

        {{-- WA Bot Shortcut Card (untuk status pending) --}}
        @if($order->status === \App\Models\Order::STATUS_PENDING)
        @php
            $adminWaPhone = config('app.admin_whatsapp', '6287865410555');
            $cleanAdminWa = preg_replace('/\D+/', '', $adminWaPhone);
            if (str_starts_with($cleanAdminWa, '0')) $cleanAdminWa = '62' . substr($cleanAdminWa, 1);
            $exampleDate  = now()->addDays(2)->format('Y-m-d');
            $botCommand   = "JADWAL#{$order->order_number}#{$exampleDate}#09:00 - 11:00 (Pagi)#Lokasi Proyek";
            $waUrl        = "https://wa.me/{$cleanAdminWa}?text=" . rawurlencode($botCommand);
        @endphp
        <div class="summary-card" style="margin-top: 16px; border-color: rgba(37,211,102,0.4); background: linear-gradient(135deg, rgba(37,211,102,0.05) 0%, rgba(18,140,67,0.05) 100%);">
            <h3 class="panel-title" style="color: #16a34a; display: flex; align-items: center; gap: 8px;">
                <i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i> Atur Jadwal via WA Bot
            </h3>
            <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 12px;">
                Alternatif: Kirim perintah ke bot WA dari nomor admin untuk langsung mengatur jadwal konsultasi & memperbarui sistem secara otomatis.
            </p>

            {{-- Preview format perintah bot --}}
            <div style="background: rgba(0,0,0,0.2); border-radius: 8px; padding: 12px 14px; font-family: monospace; font-size: 0.78rem; color: #86efac; line-height: 1.7; margin-bottom: 14px; border: 1px solid rgba(37,211,102,0.2); word-break: break-all;">
                <span style="color: #94a3b8; font-size: 0.7rem; display: block; margin-bottom: 4px; font-family: inherit;">Format perintah bot:</span>
                JADWAL#<strong style="color: #4ade80;">{{ $order->order_number }}</strong>#<span style="color: #fbbf24;">YYYY-MM-DD</span>#<span style="color: #60a5fa;">Waktu</span>#<span style="color: #f87171;">Tempat</span>
                <br>
                <span style="color: #94a3b8; font-size: 0.7rem; display: block; margin-top: 8px;">Contoh (sudah diisi, sesuaikan sebelum kirim):</span>
                <span style="color: #e2e8f0;">{{ $botCommand }}</span>
            </div>

            <a href="{{ $waUrl }}" target="_blank" rel="noopener"
               style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #16a34a; color: #fff; text-decoration: none; font-weight: 700; font-size: 0.88rem; padding: 11px 16px; border-radius: 8px; transition: background 0.2s;"
               onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                <i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i>
                Buka WA Bot & Atur Jadwal
            </a>
            <p style="font-size: 0.72rem; color: var(--text-muted); text-align: center; margin-top: 8px;">
                <i class="fas fa-info-circle"></i> Pesan sudah terisi → sesuaikan tanggal/waktu/tempat → tekan Kirim
            </p>
        </div>
        @endif

    </div>
</div>

{{-- ================================================================
     SECTION: KEBUTUHAN PROYEK & FOTO REFERENSI
     ================================================================ --}}
<div style="margin-top: 28px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

    {{-- PANEL 1: Kebutuhan Proyek (Teks Biasa) --}}
    <div class="detail-card" style="padding: 0; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 18px 22px; display: flex; align-items: center; gap: 10px;">
            <div style="background: #3b82f6; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.95rem; flex-shrink: 0;">
                <i class="fas fa-ruler-combined"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #f1f5f9; font-size: 1rem; font-weight: 700;">Kebutuhan Proyek</h3>
                <p style="margin: 0; color: #94a3b8; font-size: 0.78rem;">Catatan kebutuhan dan spesifikasi dari pelanggan (teks bebas)</p>
            </div>
        </div>

        <div style="padding: 16px 20px;">
            <form action="{{ route('admin.orders.updateRequirements', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <textarea name="project_requirements" class="form-control" rows="12" placeholder="Tuliskan spesifikasi, ukuran/dimensi (panjang x lebar x tebal), bentuk, warna, serta detail permintaan pelanggan di sini..." style="font-size: 0.88rem; line-height: 1.6; font-family: inherit; resize: vertical;">{{ old('project_requirements', $order->project_requirements) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; padding: 10px; font-weight: 700;">
                    <i class="fas fa-save"></i> Simpan Catatan Kebutuhan
                </button>
            </form>
        </div>
    </div>

    {{-- PANEL 2: Foto Referensi --}}
    <div class="detail-card" style="padding: 0; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 18px 22px; display: flex; align-items: center; gap: 10px;">
            <div style="background: #f59e0b; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.95rem; flex-shrink: 0;">
                <i class="fas fa-images"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #f1f5f9; font-size: 1rem; font-weight: 700;">Foto Referensi Model</h3>
                <p style="margin: 0; color: #94a3b8; font-size: 0.78rem;">Foto/gambar desain yang diinginkan pelanggan</p>
            </div>
            <span style="margin-left: auto; background: rgba(245,158,11,0.2); color: #fcd34d; border: 1px solid rgba(245,158,11,0.3); border-radius: 20px; padding: 2px 10px; font-size: 0.78rem; font-weight: 700;">
                {{ $order->referencePhotos->count() }} foto
            </span>
        </div>

        <div style="padding: 16px 20px;">

            {{-- Photo Grid --}}
            @if($order->referencePhotos->isNotEmpty())
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; margin-bottom: 20px;">
                @foreach($order->referencePhotos as $photo)
                <div style="position: relative; group;">
                    <img src="{{ $photo->photo_url }}"
                         alt="{{ $photo->caption ?? 'Foto Referensi' }}"
                         onclick="openLightbox('{{ $photo->photo_url }}', '{{ addslashes($photo->caption ?? '') }}')"
                         style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #e2e8f0; transition: border-color 0.2s, transform 0.2s;"
                         onmouseover="this.style.borderColor='#f59e0b'; this.style.transform='scale(1.03)'"
                         onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='scale(1)'">
                    @if($photo->caption)
                    <p style="font-size: 0.72rem; color: #64748b; margin: 4px 2px 0; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $photo->caption }}">{{ $photo->caption }}</p>
                    @endif
                    <form action="{{ route('admin.orders.photos.destroy', $photo) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')" style="position: absolute; top: 4px; right: 4px;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: rgba(0,0,0,0.6); border: none; color: white; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 0.7rem; display: flex; align-items: center; justify-content: center;" title="Hapus foto">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 20px 0; color: #94a3b8; margin-bottom: 16px;">
                <i class="fas fa-images" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                <p style="font-size: 0.85rem; margin: 0;">Belum ada foto referensi. Upload foto dari pelanggan.</p>
            </div>
            @endif

            {{-- Upload Form --}}
            <details id="photo-form-details" style="border: 1px dashed #cbd5e1; border-radius: 8px; overflow: hidden;">
                <summary style="padding: 10px 14px; background: #f8fafc; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: #f59e0b; list-style: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-upload"></i> Upload Foto Referensi
                </summary>
                <form action="{{ route('admin.orders.photos.store', $order) }}" method="POST" enctype="multipart/form-data" style="padding: 16px; background: #fafbfc;">
                    @csrf
                    {{-- Drop zone / preview --}}
                    <div id="photo-dropzone" onclick="document.getElementById('photo-file-input').click()"
                         style="border: 2px dashed #d1d5db; border-radius: 8px; padding: 24px; text-align: center; cursor: pointer; background: #fff; margin-bottom: 12px; transition: border-color 0.2s;"
                         onmouseover="this.style.borderColor='#f59e0b'" onmouseout="this.style.borderColor='#d1d5db'">
                        <div id="dropzone-placeholder">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 1.8rem; color: #94a3b8; display: block; margin-bottom: 8px;"></i>
                            <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Klik untuk pilih foto</p>
                            <p style="color: #94a3b8; font-size: 0.75rem; margin: 4px 0 0;">JPG, PNG, WEBP — maks. 5MB</p>
                        </div>
                        <img id="photo-preview" src="" alt="Preview" style="display: none; max-height: 180px; border-radius: 6px; margin: auto;">
                    </div>
                    <input type="file" id="photo-file-input" name="photo" accept="image/*" style="display: none;" onchange="previewPhoto(this)">
                    @error('photo')<p style="color: #ef4444; font-size: 0.8rem; margin: -8px 0 10px;">{{ $message }}</p>@enderror

                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Keterangan Foto (opsional)</label>
                        <input type="text" name="caption" class="form-control" placeholder="Cth: Contoh pagar yang diinginkan, Referensi desain" style="font-size: 0.85rem;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-weight: 700; background: #f59e0b; border-color: #f59e0b;">
                        <i class="fas fa-upload"></i> Upload Foto
                    </button>
                </form>
            </details>

        </div>
    </div>

</div>

{{-- Lightbox Modal --}}
<div id="lightbox" onclick="closeLightbox()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.88); z-index: 9999; align-items: center; justify-content: center; flex-direction: column; gap: 12px; padding: 20px;">
    <button onclick="closeLightbox(); event.stopPropagation()" style="position: absolute; top: 16px; right: 20px; background: rgba(255,255,255,0.15); border: none; color: white; border-radius: 50%; width: 36px; height: 36px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-times"></i>
    </button>
    <img id="lightbox-img" src="" alt="" style="max-width: 90vw; max-height: 80vh; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); object-fit: contain;">
    <p id="lightbox-caption" style="color: #e2e8f0; font-size: 0.9rem; text-align: center; max-width: 600px;"></p>
</div>

@endsection

@section('scripts')
<script>
    // ---- Status form sync ----
    const statusSelect = document.getElementById('order-status-select');
    const consultationFields = document.getElementById('consultation-fields');
    const projectFields = document.getElementById('project-fields');

    function syncFormFields() {
        if (!statusSelect) return;
        const status = statusSelect.value;
        consultationFields.style.display = status === 'scheduled' ? 'block' : 'none';
        projectFields.style.display = ['confirmed', 'in_progress', 'completed'].includes(status) ? 'block' : 'none';
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', syncFormFields);
        syncFormFields();
    }

    // ---- Photo preview ----
    function previewPhoto(input) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const placeholder = document.getElementById('dropzone-placeholder');
            const preview = document.getElementById('photo-preview');
            placeholder.style.display = 'none';
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // ---- Lightbox ----
    function openLightbox(src, caption) {
        const lb = document.getElementById('lightbox');
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-caption').textContent = caption;
        lb.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endsection
