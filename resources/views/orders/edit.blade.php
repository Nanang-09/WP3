@extends('layouts.app')

@section('title', 'Edit Pesanan ' . $service->name . ' - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Edit Pesanan</h1>
        <p>Perbarui detail proyek Anda untuk pesanan layanan {{ $service->name }}</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('order.index') }}">Pesanan & Progres</a>
            <span>/</span>
            <span>Edit Pesanan</span>
        </div>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <div class="order-grid">
            <div class="order-form-card fade-in">
                <h3 style="font-size: 1.3rem; margin-bottom: 28px;">
                    <i class="fas fa-edit" style="color: var(--accent-blue); margin-right: 8px;"></i> Formulir Edit Pesanan ({{ $order->order_number }})
                </h3>
                
                <form action="{{ route('order.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name', $order->name) }}" required>
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email" value="{{ old('email', $order->email) }}" required>
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">No. Telepon *</label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="08xxx" value="{{ old('phone', $order->phone) }}" required>
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight: 700; font-size: 1.05rem;">Usulan Jadwal Konsultasi Ulang</label>
                        <div class="budget-planner-card" style="padding: 24px; background: rgba(30, 41, 59, 0.02); border: 1px solid var(--border-color); border-radius: var(--radius);">
                            <div class="order-block-header" style="margin-bottom: 20px;">
                                <div>
                                    <span class="order-block-kicker" style="color: var(--accent-blue); font-weight: 600;">Tahap 1: Konsultasi Fisik</span>
                                    <h4 style="margin-top: 4px; font-weight: 700;">Pengajuan Jadwal Pertemuan</h4>
                                </div>
                                <p style="margin-top: 6px; font-size: 0.9rem; color: var(--text-secondary);">
                                    Atur ulang jadwal pertemuan Anda dengan Owner CV di lokasi proyek.
                                </p>
                            </div>

                            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label class="form-label" for="preferred_consultation_date">Pilih Tanggal Pertemuan *</label>
                                    <input
                                        type="date"
                                        id="preferred_consultation_date"
                                        name="preferred_consultation_date"
                                        class="form-control"
                                        min="{{ now()->addDay()->toDateString() }}"
                                        value="{{ old('preferred_consultation_date', $order->preferred_consultation_date?->toDateString()) }}"
                                        required
                                    >
                                    @error('preferred_consultation_date') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="preferred_consultation_time">Pilih Waktu Pertemuan *</label>
                                    <select
                                        id="preferred_consultation_time"
                                        name="preferred_consultation_time"
                                        class="form-control"
                                        required
                                    >
                                        <option value="">-- Pilih Jam / Slot Waktu --</option>
                                        <option value="09:00 - 11:00 (Pagi)" {{ old('preferred_consultation_time', $order->preferred_consultation_time) === '09:00 - 11:00 (Pagi)' ? 'selected' : '' }}>09:00 - 11:00 (Pagi)</option>
                                        <option value="11:00 - 13:00 (Siang)" {{ old('preferred_consultation_time', $order->preferred_consultation_time) === '11:00 - 13:00 (Siang)' ? 'selected' : '' }}>11:00 - 13:00 (Siang)</option>
                                        <option value="13:00 - 15:00 (Siang)" {{ old('preferred_consultation_time', $order->preferred_consultation_time) === '13:00 - 15:00 (Siang)' ? 'selected' : '' }}>13:00 - 15:00 (Siang)</option>
                                        <option value="15:00 - 17:00 (Sore)" {{ old('preferred_consultation_time', $order->preferred_consultation_time) === '15:00 - 17:00 (Sore)' ? 'selected' : '' }}>15:00 - 17:00 (Sore)</option>
                                    </select>
                                    @error('preferred_consultation_time') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Alamat Proyek *</label>
                        <textarea id="address" name="address" class="form-control" placeholder="Masukkan alamat lengkap lokasi proyek" style="min-height: 80px;" required>{{ old('address', $order->address) }}</textarea>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Budget Estimator (edit) --}}
                    @php
                        $priceUnit = strtolower($service->price_unit ?? '');
                        $isPerMeter = str_contains($priceUnit, 'meter') || str_contains($priceUnit, 'm2') || str_contains($priceUnit, 'm²');
                        $isPerKg    = str_contains($priceUnit, 'kg') || str_contains($priceUnit, 'kilo');
                        $showEstimator = $isPerMeter || $isPerKg;
                        $unitLabel = $isPerKg ? 'kilogram (kg)' : 'meter persegi (m²)';
                        $unitShort = $isPerKg ? 'kg' : 'm²';
                        $pricePerUnit = (int) round((float) $service->price_start);
                    @endphp

                    @if($showEstimator)
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 700; font-size: 1.05rem;">
                            <i class="fas fa-calculator" style="color: var(--accent-blue);"></i>
                            Estimasi Budget Proyek
                        </label>
                        <div style="background: linear-gradient(135deg, rgba(0,212,255,0.04) 0%, rgba(124,58,237,0.04) 100%); border: 1.5px solid rgba(0,212,255,0.2); border-radius: var(--radius); padding: 24px;">
                            <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 18px;">
                                Perbarui perkiraan ukuran proyek agar Owner CV bisa menyiapkan penawaran yang lebih akurat.
                                <strong>Harga final ditentukan setelah survei lokasi.</strong>
                            </p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label class="form-label" for="qty_estimate" style="font-size: 0.88rem;">Perkiraan Ukuran / Volume</label>
                                    <div style="position: relative;">
                                        <input type="number" id="qty_estimate" class="form-control"
                                            placeholder="Contoh: 50" min="1" step="0.5"
                                            style="padding-right: 56px;"
                                            oninput="recalcBudget()">
                                        <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem; font-weight: 700; color: var(--accent-blue);">{{ $unitShort }}</span>
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Satuan: {{ $unitLabel }}</p>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size: 0.88rem;">Harga Referensi per {{ $unitShort }}</label>
                                    <div style="padding: 12px 14px; background: rgba(0,0,0,0.15); border-radius: 8px; border: 1px solid var(--border-color); font-size: 1rem; font-weight: 800; color: var(--accent-gold);">
                                        Rp {{ number_format($pricePerUnit, 0, ',', '.') }} / {{ $unitShort }}
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Harga mulai, bisa berbeda setelah survei</p>
                                </div>
                            </div>

                            <div id="budget-result" style="display:none; background: rgba(16,185,129,0.06); border: 1.5px solid rgba(16,185,129,0.25); border-radius: 10px; padding: 18px; margin-bottom: 18px;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                    <i class="fas fa-chart-bar" style="color: #10b981;"></i>
                                    <strong style="color: #10b981; font-size: 0.95rem;">Estimasi Anggaran Proyek</strong>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center;">
                                    <div style="background: rgba(255,255,255,0.04); border-radius: 8px; padding: 12px 8px; border: 1px solid rgba(16,185,129,0.15);">
                                        <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase; font-weight: 700;">Estimasi Minimum</p>
                                        <p id="est-min" style="font-size: 1rem; font-weight: 800; color: #10b981;">-</p>
                                    </div>
                                    <div style="background: rgba(0,212,255,0.06); border-radius: 8px; padding: 12px 8px; border: 1.5px solid rgba(0,212,255,0.25);">
                                        <p style="font-size: 0.72rem; color: var(--accent-blue); margin-bottom: 4px; text-transform: uppercase; font-weight: 700;">Estimasi Ideal</p>
                                        <p id="est-mid" style="font-size: 1.1rem; font-weight: 800; color: var(--accent-blue);">-</p>
                                    </div>
                                    <div style="background: rgba(124,58,237,0.05); border-radius: 8px; padding: 12px 8px; border: 1px solid rgba(124,58,237,0.15);">
                                        <p style="font-size: 0.72rem; color: #7c3aed; margin-bottom: 4px; text-transform: uppercase; font-weight: 700;">Estimasi Maksimum</p>
                                        <p id="est-max" style="font-size: 1rem; font-weight: 800; color: #7c3aed;">-</p>
                                    </div>
                                </div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 10px;">
                                    <i class="fas fa-info-circle"></i> Estimasi berdasarkan <span id="qty-display">0</span> {{ $unitShort }} × harga referensi.
                                </p>
                            </div>

                            <div id="budget-options-group" style="display:none;">
                                <label class="form-label" style="font-size: 0.88rem; font-weight: 700;">Pilih Range Budget *</label>
                                <div id="budget-range-options" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 8px;"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="budget_range" name="budget_range" value="{{ old('budget_range', $order->budget_range) }}">
                    @else
                    <div class="form-group">
                        <label class="form-label" for="budget_range">Perkiraan Budget Proyek</label>
                        <input type="text" id="budget_range" name="budget_range" class="form-control"
                            placeholder="Contoh: Rp 10.000.000 - Rp 25.000.000 (opsional)"
                            value="{{ old('budget_range', $order->budget_range) }}">
                    </div>
                    @endif

                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <a href="{{ route('order.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center; display: inline-flex; justify-content: center; align-items: center;">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary" style="flex: 2;">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="order-summary fade-in">
                <h3><i class="fas fa-receipt" style="color: var(--accent-blue);"></i> Ringkasan Layanan</h3>
                <div class="order-service-info">
                    <div class="order-service-icon">
                        <i class="{{ $service->icon }}"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1rem;">{{ $service->name }}</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">{{ $service->price_unit }}</p>
                    </div>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 20px;">{{ $service->short_description }}</p>
                <div style="padding: 16px; background: rgba(0, 212, 255, 0.05); border-radius: var(--radius); border: 1px solid rgba(0, 212, 255, 0.1);">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Harga mulai dari</p>
                    <p style="font-size: 1.4rem; font-weight: 800; color: var(--accent-gold);">Rp {{ number_format($service->price_start, 0, ',', '.') }}</p>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">{{ $service->price_unit }}</p>
                </div>

                @if($order->budget_range)
                <div id="sidebar-budget-badge" style="margin-top:16px; padding:14px 16px; background:rgba(16,185,129,0.06); border:1.5px solid rgba(16,185,129,0.2); border-radius:10px;">
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:4px; font-weight:700; text-transform:uppercase;"><i class="fas fa-wallet"></i> Budget Sekarang</p>
                    <p id="sidebar-budget-value" style="font-size:0.95rem; font-weight:800; color:#10b981;">{{ $order->budget_range }}</p>
                </div>
                @else
                <div id="sidebar-budget-badge" style="display:none; margin-top:16px; padding:14px 16px; background:rgba(16,185,129,0.06); border:1.5px solid rgba(16,185,129,0.2); border-radius:10px;">
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:4px; font-weight:700; text-transform:uppercase;"><i class="fas fa-wallet"></i> Budget Dipilih</p>
                    <p id="sidebar-budget-value" style="font-size:0.95rem; font-weight:800;"></p>
                </div>
                @endif
                
                @if($queuePreview)
                <div class="queue-preview-card" style="margin-top: 20px; border-color: rgba(30,41,59,0.1); background: rgba(30,41,59,0.01);">
                    <div class="queue-preview-header">
                        <i class="fas fa-hourglass-half"></i>
                        <strong>Estimasi Antrean Aktif</strong>
                    </div>
                    <p>Ada {{ $queuePreview['orders_ahead'] }} pesanan berjalan sebelum pesanan Anda diproses.</p>
                    <div class="queue-preview-stats">
                        <span>Posisi antrean: #{{ $queuePreview['position'] }}</span>
                        <span>Estimasi: {{ $queuePreview['estimated_wait_days'] }} hari kerja</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const PRICE_PER_UNIT = {{ (int) round((float) ($service->price_start ?? 0)) }};

    function formatRupiah(val) {
        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
    }

    function recalcBudget() {
        const qtyInput    = document.getElementById('qty_estimate');
        const budgetField = document.getElementById('budget_range');
        if (!qtyInput) return;

        const qty = parseFloat(qtyInput.value) || 0;
        const estResult  = document.getElementById('budget-result');
        const optsGroup  = document.getElementById('budget-options-group');
        const qtyDisplay = document.getElementById('qty-display');

        if (qty <= 0) {
            if (estResult) estResult.style.display = 'none';
            if (optsGroup) optsGroup.style.display  = 'none';
            return;
        }

        const base = PRICE_PER_UNIT * qty;
        const minV = base;
        const midV = base * 1.20;
        const maxV = base * 1.40;

        if (qtyDisplay) qtyDisplay.textContent = qty % 1 === 0 ? qty : qty.toFixed(1);
        document.getElementById('est-min').textContent = formatRupiah(minV);
        document.getElementById('est-mid').textContent = formatRupiah(midV);
        document.getElementById('est-max').textContent = formatRupiah(maxV);
        if (estResult) estResult.style.display = '';

        const optsContainer = document.getElementById('budget-range-options');
        optsContainer.innerHTML = '';

        const ranges = [
            { label: 'Hemat',   from: minV,       to: minV * 1.10, color: '#10b981', bg: 'rgba(16,185,129,0.07)',  border: 'rgba(16,185,129,0.25)' },
            { label: 'Standar', from: midV * 0.95, to: midV * 1.10, color: '#00d4ff', bg: 'rgba(0,212,255,0.06)',   border: 'rgba(0,212,255,0.30)' },
            { label: 'Premium', from: maxV * 0.95, to: maxV * 1.15, color: '#a78bfa', bg: 'rgba(124,58,237,0.06)',  border: 'rgba(124,58,237,0.25)' },
        ];

        ranges.forEach(r => {
            const rangeStr = formatRupiah(r.from) + ' – ' + formatRupiah(r.to);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.style.cssText = `cursor:pointer; background:${r.bg}; border:1.5px solid ${r.border}; border-radius:10px; padding:12px 8px; text-align:center; transition:all .2s; outline:none;`;
            btn.innerHTML = `
                <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;color:${r.color};margin-bottom:4px;">${r.label}</div>
                <div style="font-size:.78rem;font-weight:700;color:#e2e8f0;line-height:1.4;">${rangeStr}</div>
            `;
            btn.addEventListener('click', () => {
                optsContainer.querySelectorAll('button').forEach(b => {
                    b.style.boxShadow = '';
                    b.style.transform = '';
                });
                btn.style.boxShadow = `0 0 0 3px ${r.color}55`;
                btn.style.transform = 'scale(1.03)';
                if (budgetField) budgetField.value = rangeStr;
                // update sidebar
                const badge = document.getElementById('sidebar-budget-badge');
                if (badge) {
                    badge.style.display = '';
                    badge.style.color = r.color;
                    document.getElementById('sidebar-budget-value').textContent = rangeStr;
                }
            });
            optsContainer.appendChild(btn);
        });

        if (optsGroup) optsGroup.style.display = '';
    }
</script>
@endsection
