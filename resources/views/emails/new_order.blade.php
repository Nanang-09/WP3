<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Baru - {{ config('app.business_name') }}</title>
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
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
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
        .alert-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.2);
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            margin-top: 16px;
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
            background: rgba(234,179,8,0.15);
            border: 1px solid rgba(234,179,8,0.4);
            color: #fbbf24;
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
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }
        .info-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 14px 16px;
        }
        .info-card .card-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }
        .info-card .card-value {
            font-size: 14px;
            font-weight: 600;
            color: #e2e8f0;
            line-height: 1.4;
        }
        .info-card.full {
            grid-column: 1 / -1;
        }
        .info-card.highlight .card-value {
            color: #34d399;
            font-size: 16px;
        }
        /* Consultation box */
        .consultation-box {
            background: linear-gradient(135deg, rgba(14,165,233,0.1) 0%, rgba(99,102,241,0.1) 100%);
            border: 1px solid rgba(99,102,241,0.3);
            border-radius: 12px;
            padding: 20px;
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
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }
        .consultation-box .con-time {
            font-size: 14px;
            color: #94a3b8;
        }
        /* Description */
        .desc-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-left: 3px solid #38bdf8;
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
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
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
            .info-grid { grid-template-columns: 1fr; }
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
            <h1>🔔 Ada Pesanan Baru Masuk!</h1>
            <p>Segera tinjau dan jadwalkan konsultasi dengan pemesan</p>
            <div class="alert-badge">
                <span>⚡</span> Diterima: {{ now()->setTimezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
            </div>
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

            <!-- Pemesan Info -->
            <div class="section-title">👤 Data Pemesan</div>
            <div class="info-grid">
                <div class="info-card">
                    <div class="card-label">Nama Lengkap</div>
                    <div class="card-value">{{ $order->name }}</div>
                </div>
                <div class="info-card">
                    <div class="card-label">No. Telepon / WA</div>
                    <div class="card-value">{{ $order->phone }}</div>
                </div>
                <div class="info-card">
                    <div class="card-label">Email</div>
                    <div class="card-value">{{ $order->email }}</div>
                </div>
                <div class="info-card">
                    <div class="card-label">Layanan Dipesan</div>
                    <div class="card-value">{{ $order->service->name ?? '-' }}</div>
                </div>
                <div class="info-card full">
                    <div class="card-label">Alamat / Lokasi Proyek</div>
                    <div class="card-value">{{ $order->address }}</div>
                </div>
                @if($order->budget_range)
                <div class="info-card highlight full">
                    <div class="card-label">💰 Kisaran Budget Pemesan</div>
                    <div class="card-value">{{ $order->budget_range }}</div>
                </div>
                @endif
            </div>

            <!-- Jadwal Konsultasi -->
            <div class="section-title">📅 Jadwal Konsultasi</div>
            <div class="consultation-box" style="background: linear-gradient(135deg, rgba(234,179,8,0.1) 0%, rgba(249,115,22,0.1) 100%); border: 1px solid rgba(249,115,22,0.3);">
                <div class="con-title" style="color: #fbd561;">🗓️ Status Penjadwalan:</div>
                <div class="con-date" style="font-size: 16px; margin-bottom: 6px; color: #fff;">
                    Menunggu Jadwal dari Admin
                </div>
                <div class="con-time" style="color: #94a3b8; font-size: 12.5px; line-height: 1.5;">
                    Silakan tentukan jadwal konsultasi melalui dashboard admin atau balas pesan WhatsApp otomatis menggunakan bot dengan format:<br>
                    <code style="display: block; background: #0f172a; padding: 6px 10px; border-radius: 6px; margin-top: 6px; border: 1px solid #334155; color: #fbbf24; font-family: monospace; font-size: 12px; font-weight: bold;">JADWAL#{{ $order->order_number }}#Tanggal(YYYY-MM-DD)#Waktu#Tempat</code>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="section-title">📝 Deskripsi Proyek</div>
            <div class="desc-box">
                <p>{{ $order->description }}</p>
            </div>

            @if($order->notes)
            <div class="section-title">💬 Catatan Tambahan</div>
            <div class="desc-box">
                <p>{{ $order->notes }}</p>
            </div>
            @endif

            <!-- CTA -->
            <div class="cta-section">
                <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}" class="cta-btn">
                    📋 Buka Detail Pesanan di Dashboard
                </a>
                <p class="cta-note">
                    Atau buka: {{ config('app.url') }}/admin/orders/{{ $order->id }}
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Email ini dikirim otomatis oleh sistem <strong>{{ config('app.business_name') }}</strong>.<br>
                Pesanan baru diterima pada {{ now()->setTimezone('Asia/Makassar')->translatedFormat('d F Y \p\u\k\u\l H:i') }} WITA.<br><br>
                Jika ada kendala, hubungi tim teknis Anda.
            </p>
        </div>

    </div>
</body>
</html>
