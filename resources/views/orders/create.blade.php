@extends('layouts.app')

@section('title', 'Pesan ' . $service->name . ' - WeldTrack')

@section('content')
@php
    $priceStart = (int) round((float) $service->price_start);
    $budgetStep = 100000;
    $budgetFloor = 100000;
    $budgetComfort = max($budgetFloor + 400000, $priceStart);
    $budgetPremium = max($budgetComfort + 500000, $priceStart * 3);
    $budgetCeiling = max($budgetPremium + 1000000, $priceStart * 10);

    $orderTemplates = [
        [
            'id' => 'starter',
            'title' => 'Starter',
            'caption' => 'Untuk kebutuhan awal, perbaikan ringan, atau proyek skala kecil.',
            'focus' => 'hemat',
            'budget_min' => $budgetFloor,
            'budget_max' => $budgetComfort,
            'description' => "Saya membutuhkan layanan {$service->name} dengan lingkup awal sebagai berikut:\n- Area / ukuran pekerjaan: [isi]\n- Kebutuhan utama: [isi]\n- Target hasil: [isi]\n- Material atau finishing yang diinginkan: [opsional]\n- Waktu mulai yang diharapkan: [isi]",
            'notes' => "Prioritas saya saat ini adalah efisiensi biaya.\nMohon bantu sarankan opsi kerja yang paling efektif untuk kebutuhan awal ini.",
        ],
        [
            'id' => 'standard',
            'title' => 'Standar',
            'caption' => 'Cocok untuk pekerjaan menengah dengan hasil yang seimbang.',
            'focus' => 'seimbang',
            'budget_min' => $budgetComfort,
            'budget_max' => $budgetPremium,
            'description' => "Saya ingin memesan {$service->name} untuk proyek dengan kebutuhan berikut:\n- Area / ukuran pekerjaan: [isi]\n- Kondisi existing saat ini: [isi]\n- Ruang lingkup pekerjaan: [isi]\n- Preferensi material / gaya: [isi]\n- Target selesai: [isi]",
            'notes' => "Saya butuh penawaran yang seimbang antara harga, kualitas hasil, dan durasi pengerjaan.",
        ],
        [
            'id' => 'premium',
            'title' => 'Lengkap',
            'caption' => 'Untuk proyek custom, spesifikasi tinggi, atau lingkup yang lebih luas.',
            'focus' => 'kualitas',
            'budget_min' => $budgetPremium,
            'budget_max' => $budgetCeiling,
            'description' => "Saya membutuhkan {$service->name} untuk proyek lengkap / custom dengan detail berikut:\n- Area / ukuran pekerjaan: [isi]\n- Fungsi utama proyek: [isi]\n- Spesifikasi material / struktur / finishing: [isi]\n- Kebutuhan desain atau custom tambahan: [isi]\n- Target mulai dan target selesai: [isi]",
            'notes' => "Saya ingin prioritas pada kualitas hasil, detail pengerjaan, dan fleksibilitas penyesuaian desain.",
        ],
    ];

    $budgetPresets = [
        ['id' => 'hemat', 'label' => 'Hemat', 'min' => $budgetFloor, 'max' => $budgetComfort],
        ['id' => 'ideal', 'label' => 'Ideal', 'min' => $budgetComfort, 'max' => $budgetPremium],
        ['id' => 'fleksibel', 'label' => 'Leluasa', 'min' => $budgetPremium, 'max' => $budgetCeiling],
    ];

    $budgetFocuses = [
        ['id' => 'hemat', 'label' => 'Fokus Hemat'],
        ['id' => 'seimbang', 'label' => 'Seimbang'],
        ['id' => 'kualitas', 'label' => 'Fokus Kualitas'],
    ];
@endphp
<section class="page-header">
    <div class="container">
        <h1>Form Pemesanan</h1>
        <p>Isi detail proyek Anda untuk memesan layanan {{ $service->name }}</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <a href="{{ route('services.index') }}">Layanan</a>
            <span>/</span>
            <span>Pemesanan</span>
        </div>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <div class="order-grid">
            <div class="order-form-card fade-in">
                <h3 style="font-size: 1.3rem; margin-bottom: 28px;"><i class="fas fa-clipboard-list" style="color: var(--accent-blue);"></i> Detail Pemesanan</h3>
                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">


                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">No. Telepon *</label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="08xxx" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Alamat Proyek *</label>
                        <textarea id="address" name="address" class="form-control" placeholder="Masukkan alamat lengkap lokasi proyek" style="min-height: 80px;" required>{{ old('address') }}</textarea>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Budget Estimator --}}
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
                        <div id="budget-estimator-card" style="background: linear-gradient(135deg, rgba(0,212,255,0.04) 0%, rgba(124,58,237,0.04) 100%); border: 1.5px solid rgba(0,212,255,0.2); border-radius: var(--radius); padding: 24px;">
                            <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 18px;">
                                Masukkan perkiraan ukuran proyek Anda agar kami bisa mempersiapkan estimasi anggaran yang lebih akurat. <strong>Harga final ditentukan setelah survei lokasi.</strong>
                            </p>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label class="form-label" for="qty_estimate" style="font-size: 0.88rem;">
                                        Perkiraan Ukuran / Volume *
                                    </label>
                                    <div style="position: relative;">
                                        <input
                                            type="number"
                                            id="qty_estimate"
                                            class="form-control"
                                            placeholder="Contoh: 50"
                                            min="1"
                                            step="0.5"
                                            style="padding-right: 56px;"
                                            oninput="recalcBudget()"
                                        >
                                        <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem; font-weight: 700; color: var(--accent-blue);">{{ $unitShort }}</span>
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Satuan: {{ $unitLabel }}</p>
                                </div>

                                <div>
                                    <label class="form-label" style="font-size: 0.88rem;">
                                        Harga Referensi per {{ $unitShort }}
                                    </label>
                                    <div style="padding: 12px 14px; background: rgba(0,0,0,0.15); border-radius: 8px; border: 1px solid var(--border-color); font-size: 1rem; font-weight: 800; color: var(--accent-gold);">
                                        Rp {{ number_format($pricePerUnit, 0, ',', '.') }} / {{ $unitShort }}
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Harga mulai, bisa berbeda setelah survei</p>
                                </div>
                            </div>

                            {{-- Hasil Estimasi --}}
                            <div id="budget-result" style="display:none; background: rgba(16,185,129,0.06); border: 1.5px solid rgba(16,185,129,0.25); border-radius: 10px; padding: 18px; margin-bottom: 18px;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                    <i class="fas fa-chart-bar" style="color: #10b981; font-size: 1.1rem;"></i>
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
                                    <i class="fas fa-info-circle"></i> Estimasi berdasarkan <span id="qty-display">0</span> {{ $unitShort }} × harga referensi. Harga asli ditetapkan setelah survei.
                                </p>
                            </div>

                            {{-- Opsi Budget Range --}}
                            <div id="budget-options-group" style="display:none;">
                                <label class="form-label" style="font-size: 0.88rem; font-weight: 700;">
                                    Pilih Range Budget yang Siap Anda Keluarkan *
                                </label>
                                <div id="budget-range-options" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 8px;"></div>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">
                                    <i class="fas fa-shield-alt" style="color: #10b981;"></i> Informasi ini hanya referensi untuk Owner CV. Harga final 100% berdasarkan hasil survei.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Budget Range (hidden, diisi JS, atau manual input kalau non-unit service) --}}
                    @if(!$showEstimator)
                    <div class="form-group">
                        <label class="form-label" for="budget_range">Perkiraan Budget Proyek</label>
                        <input type="text" id="budget_range" name="budget_range" class="form-control"
                            placeholder="Contoh: Rp 10.000.000 - Rp 25.000.000 (opsional)"
                            value="{{ old('budget_range') }}">
                        <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Opsional — membantu Owner CV mempersiapkan penawaran yang sesuai.
                        </p>
                    </div>
                    @else
                    <input type="hidden" id="budget_range" name="budget_range" value="{{ old('budget_range') }}">
                    @endif

                    <button type="submit" id="order-submit-btn" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                        <i class="fas fa-paper-plane"></i> <span id="submit-label">Kirim Pesanan</span>
                    </button>
                </form>
            </div>

            <div class="order-summary fade-in">
                <h3><i class="fas fa-receipt" style="color: var(--accent-blue);"></i> Ringkasan</h3>
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

                {{-- Live Budget Summary (shown after user picks a range) --}}
                <div id="sidebar-budget-badge" style="display:none; margin-top:16px; padding:14px 16px; background:rgba(16,185,129,0.06); border:1.5px solid rgba(16,185,129,0.2); border-radius:10px;">
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:4px; font-weight:700; text-transform:uppercase;"><i class="fas fa-wallet"></i> Budget Dipilih</p>
                    <p id="sidebar-budget-value" style="font-size:0.95rem; font-weight:800;"></p>
                </div>
                <div class="budget-check-card" style="margin-top: 20px; padding: 20px; background: rgba(0, 212, 255, 0.03); border: 1px solid rgba(0, 212, 255, 0.15); border-radius: var(--radius);">
                    <div class="queue-preview-header" style="display: flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 12px;">
                        <i class="fas fa-route" style="color: var(--accent-blue);"></i>
                        <strong>Alur Proses Layanan</strong>
                    </div>
                    <div class="summary-live-list" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.88rem;">
                        <div style="display: flex; gap: 10px;">
                            <span style="font-weight: 700; color: var(--accent-blue);">1.</span>
                            <span><strong>Ajukan Pesanan:</strong> Isi deskripsi proyek dan usulkan waktu konsultasi tatap muka yang diinginkan.</span>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <span style="font-weight: 700; color: var(--accent-blue);">2.</span>
                            <span><strong>Penjadwalan Konsultasi:</strong> Owner CV akan mengonfirmasi jadwal pertemuan fisik.</span>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <span style="font-weight: 700; color: var(--accent-blue);">3.</span>
                            <span><strong>Kesepakatan Bersama:</strong> Survei lokasi dilakukan, harga proyek disepakati bersama, & mandor ditugaskan.</span>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <span style="font-weight: 700; color: var(--accent-blue);">4.</span>
                            <span><strong>Monitoring Lapangan:</strong> Proyek berjalan, mandor mengirim 4 update milestone foto (25%, 50%, 75%, 100%).</span>
                        </div>
                    </div>
                </div>
                @if($queuePreview)
                <div class="queue-preview-card">
                    <div class="queue-preview-header">
                        <i class="fas fa-hourglass-half"></i>
                        <strong>Pesanan baru akan masuk antrean</strong>
                    </div>
                    <p>Saat ini ada {{ $queuePreview['orders_ahead'] }} pesanan aktif/antrean sebelum pesanan baru diproses.</p>
                    <div class="queue-preview-stats">
                        <span>Posisi antrean baru: #{{ $queuePreview['position'] }}</span>
                        <span>Estimasi tunggu: {{ $queuePreview['estimated_wait_days'] }} hari kerja</span>
                    </div>
                </div>
                @endif
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-glass);">
                    <p style="font-size: 0.8rem; color: var(--text-muted);"><i class="fas fa-info-circle" style="color: var(--accent-blue);"></i> Tim kami akan menghubungi Anda dalam 1x24 jam untuk konfirmasi dan survei lokasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    /* ─── Double-Submit Protection ─── */
    let formSubmitted = false;

    // Attach to form submit event
    document.querySelector('form[action]').addEventListener('submit', function(e) {
        if (formSubmitted) {
            e.preventDefault();
            return false;
        }
        formSubmitted = true;

        const btn = document.getElementById('order-submit-btn');
        if (btn) {
            // Disable button slightly later so the submit event finishes bubbling in the browser
            setTimeout(() => {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            }, 10);
            
            const label = document.getElementById('submit-label');
            if (label) label.textContent = 'Mengirim...';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Mengirim pesanan...</span>';
        }
    });
    /* ─── Budget Estimator ─── */
    const PRICE_PER_UNIT = {{ (int) round((float) ($service->price_start ?? 0)) }};
    const UNIT_SHORT     = '{{ $unitShort ?? '' }}';

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
            if (estResult)  estResult.style.display  = 'none';
            if (optsGroup)  optsGroup.style.display   = 'none';
            if (budgetField) budgetField.value = '';
            updateSidebarBudget(null);
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

        if (estResult)  estResult.style.display  = '';

        /* Render range option cards */
        const optsContainer = document.getElementById('budget-range-options');
        optsContainer.innerHTML = '';

        const ranges = [
            { label: 'Hemat',   from: minV,        to: minV * 1.10,  color: '#10b981', bg: 'rgba(16,185,129,0.07)',  border: 'rgba(16,185,129,0.25)' },
            { label: 'Standar', from: midV * 0.95,  to: midV * 1.10,  color: '#00d4ff', bg: 'rgba(0,212,255,0.06)',   border: 'rgba(0,212,255,0.30)' },
            { label: 'Premium', from: maxV * 0.95,  to: maxV * 1.15,  color: '#a78bfa', bg: 'rgba(124,58,237,0.06)',  border: 'rgba(124,58,237,0.25)' },
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
                /* deselect siblings */
                optsContainer.querySelectorAll('button').forEach(b => {
                    b.style.boxShadow = '';
                    b.style.transform = '';
                });
                btn.style.boxShadow = `0 0 0 3px ${r.color}55`;
                btn.style.transform = 'scale(1.03)';
                if (budgetField) budgetField.value = rangeStr;
                updateSidebarBudget(rangeStr, r.color);
            });
            optsContainer.appendChild(btn);
        });

        if (optsGroup) optsGroup.style.display = '';
        updateSidebarBudget(null);
    }

    function updateSidebarBudget(rangeStr, color) {
        let el = document.getElementById('sidebar-budget-badge');
        if (!el) return;
        if (!rangeStr) {
            el.style.display = 'none';
            return;
        }
        el.style.display = '';
        el.style.color   = color || '#10b981';
        el.querySelector('#sidebar-budget-value').textContent = rangeStr;
    }
</script>
@endsection
