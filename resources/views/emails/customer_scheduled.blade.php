<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Konsultasi Ditentukan - {{ config('app.business_name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 20px;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        /* Header */
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            padding: 36px 32px;
            text-align: center;
        }
        .header .logo-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .header p {
            color: rgba(255,255,255,0.85);
            font-size: 14px;
        }
        /* Order number */
        .order-number-bar {
            background: #0f172a;
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #334155;
        }
        .order-number-bar .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .order-number-bar .value {
            font-size: 20px;
            font-weight: 800;
            color: #38bdf8;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }
        .status-chip {
            background: rgba(59,130,246,0.15);
            border: 1px solid rgba(59,130,246,0.4);
            color: #60a5fa;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* Body */
        .body {
            padding: 28px 32px;
        }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #38bdf8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #334155;
        }
        /* Consultation box */
        .consultation-box {
            background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(99,102,241,0.12) 100%);
            border: 1.5px solid rgba(99,102,241,0.4);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .consultation-box .con-title {
            font-size: 13px;
            font-weight: 700;
            color: #a5b4fc;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .consultation-box .con-date {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }
        .consultation-box .con-time {
            font-size: 15px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
        }
        .consultation-box .con-place {
            font-size: 14px;
            color: #94a3b8;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 10px;
            margin-top: 10px;
        }
        /* Description */
        .desc-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-left: 3px solid #3b82f6;
            border-radius: 0 10px 10px 0;
            padding: 16px 18px;
            margin-bottom: 24px;
        }
        .desc-box p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.7;
            white-space: pre-line;
        }
        /* CTA */
        .cta-section {
            text-align: center;
            padding: 24px 0 8px;
            border-top: 1px solid #334155;
        }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 32px;
            border-radius: 10px;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }
        .cta-note {
            font-size: 12px;
            color: #64748b;
        }
        /* Footer */
        .footer {
            background: #0f172a;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #334155;
        }
        .footer p {
            font-size: 12px;
            color: #475569;
            line-height: 1.6;
        }
        .footer strong {
            color: #64748b;
        }
        @media (max-width: 480px) {
            .order-number-bar { flex-direction: column; gap: 10px; }
            .body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <div class="logo-badge">{{ config('app.business_name') }}</div>
            <h1>📅 Jadwal Konsultasi Ditentukan!</h1>
            <p>Admin kami telah menjadwalkan kunjungan survei fisik & konsultasi tatap muka</p>
        </div>

        <!-- Order Number Bar -->
        <div class="order-number-bar">
            <div>
                <div class="label">Nomor Pesanan</div>
                <div class="value">{{ $order->order_number }}</div>
            </div>
            <div class="status-chip">{{ $order->status_label }}</div>
        </div>

        <!-- Body -->
        <div class="body">

            <!-- Agenda Box -->
            <div class="section-title">🗓️ Agenda Pertemuan</div>
            <div class="consultation-box">
                <div class="con-title">Survei & Konsultasi Fisik Lapangan:</div>
                <div class="con-date">
                    📅 {{ $order->consultation_date?->translatedFormat('l, d F Y') }}
                </div>
                <div class="con-time">
                    ⏰ Jam: {{ $order->consultation_time }}
                </div>
                <div class="con-place">
                    📍 Tempat: <strong>{{ $order->consultation_place ?: 'Lokasi Proyek' }}</strong>
                </div>
            </div>

            @if($order->admin_notes)
            <div class="section-title">💬 Catatan dari Admin / Owner</div>
            <div class="desc-box">
                <p>{{ $order->admin_notes }}</p>
            </div>
            @endif

            <!-- Detail Pesanan -->
            <div class="section-title">👤 Informasi Pesanan</div>
            <div style="background: #0f172a; border: 1px solid #334155; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; margin-bottom: 8px;">
                    <span style="color: #64748b;">Nama Pelanggan:</span>
                    <strong style="color: #e2e8f0;">{{ $order->name }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; margin-bottom: 8px;">
                    <span style="color: #64748b;">Layanan:</span>
                    <strong style="color: #e2e8f0;">{{ $order->service->name ?? '-' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; margin-bottom: 8px;">
                    <span style="color: #64748b;">Alamat:</span>
                    <strong style="color: #e2e8f0; max-width: 60%; text-align: right;">{{ $order->address }}</strong>
                </div>
                @if($order->budget_range)
                <div style="display: flex; justify-content: space-between; font-size: 13.5px;">
                    <span style="color: #64748b;">Estimasi Budget:</span>
                    <strong style="color: #34d399;">{{ $order->budget_range }}</strong>
                </div>
                @endif
            </div>

            <!-- CTA -->
            <div class="cta-section">
                <a href="{{ config('app.url') }}/pesanan-saya/{{ $order->id }}/konsultasi" class="cta-btn">
                    📋 Buka Halaman Konsultasi Saya
                </a>
                <p class="cta-note">
                    Atau buka: {{ config('app.url') }}/pesanan-saya/{{ $order->id }}/konsultasi
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Email ini dikirim otomatis oleh sistem <strong>{{ config('app.business_name') }}</strong>.<br>
                Mohon pastikan Anda berada di lokasi pada waktu yang telah ditentukan.<br><br>
                CV SUNRISE ISLAND &copy; 2026. All rights reserved.
            </p>
        </div>

    </div>
</body>
</html>
