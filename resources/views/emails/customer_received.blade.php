<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Diterima - {{ config('app.business_name') }}</title>
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
            background: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
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
        /* Info Box */
        .status-info-box {
            background: linear-gradient(135deg, rgba(14,165,233,0.1) 0%, rgba(16,185,129,0.1) 100%);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .status-info-box .title {
            font-size: 14px;
            font-weight: 700;
            color: #34d399;
            margin-bottom: 8px;
        }
        .status-info-box .desc {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
        }
        /* Description */
        .desc-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-left: 3px solid #10b981;
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
            background: linear-gradient(135deg, #0ea5e9, #10b981);
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
            <h1>🛠️ Pesanan Anda Telah Diterima!</h1>
            <p>Terima kasih telah mempercayakan proyek pengelasan Anda kepada kami</p>
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

            <!-- Next Step Alert -->
            <div class="status-info-box">
                <div class="title">📋 Langkah Selanjutnya: Penjadwalan Survei</div>
                <div class="desc">
                    Pesanan Anda telah masuk ke sistem kami. Saat ini, <strong>Admin kami sedang memproses dan menjadwalkan waktu konsultasi tatap muka serta survei fisik ke lokasi Anda.</strong> Anda akan menerima pemberitahuan lebih lanjut via WhatsApp dan Email begitu jadwal telah ditentukan.
                </div>
            </div>

            <!-- Detail Pesanan -->
            <div class="section-title">👤 Data Pemesan & Proyek</div>
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
                    <div class="card-label">Layanan Dipesan</div>
                    <div class="card-value">{{ $order->service->name ?? '-' }}</div>
                </div>
                <div class="info-card">
                    <div class="card-label">Email</div>
                    <div class="card-value">{{ $order->email }}</div>
                </div>
                <div class="info-card full">
                    <div class="card-label">Alamat / Lokasi Proyek</div>
                    <div class="card-value">{{ $order->address }}</div>
                </div>
                @if($order->budget_range)
                <div class="info-card highlight full">
                    <div class="card-label">💰 Perkiraan Budget Proyek</div>
                    <div class="card-value">{{ $order->budget_range }}</div>
                </div>
                @endif
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
                <a href="{{ config('app.url') }}/pesanan-saya" class="cta-btn">
                    📋 Pantau Status Pesanan Saya
                </a>
                <p class="cta-note">
                    Atau buka: {{ config('app.url') }}/pesanan-saya
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Email ini dikirim otomatis oleh sistem <strong>{{ config('app.business_name') }}</strong>.<br>
                Terima kasih atas kerja samanya.<br><br>
                CV SUNRISE ISLAND &copy; 2026. All rights reserved.
            </p>
        </div>

    </div>
</body>
</html>
