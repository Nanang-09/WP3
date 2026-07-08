<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lapangan - {{ config('app.business_name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 24px; }
        .wrapper { max-width: 620px; margin: 0 auto; background: #1e293b; border-radius: 16px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); padding: 28px 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; color: #fff; }
        .content { padding: 24px; line-height: 1.6; }
        .badge { display: inline-block; background: #334155; color: #fff; padding: 6px 12px; border-radius: 999px; font-size: 13px; }
        .box { background: #0f172a; border: 1px solid #334155; border-radius: 12px; padding: 16px; margin-top: 16px; }
        .btn { display: inline-block; margin-top: 20px; background: #0ea5e9; color: #fff !important; text-decoration: none; padding: 12px 18px; border-radius: 10px; font-weight: 700; }
        .muted { color: #94a3b8; font-size: 14px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Update Lapangan Proyek Anda</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $order->name }}</strong>,</p>
            <p>Mandor lapangan kami baru saja mengirim update untuk pesanan <strong>#{{ $order->order_number }}</strong> ({{ $order->service->name }}).</p>

            <div class="box">
                <p style="margin-top:0;"><span class="badge">Progres {{ $update->progress_percent }}%</span></p>
                <h2 style="margin: 8px 0 12px; font-size: 18px;">{{ $update->title }}</h2>
                <p style="margin: 0 0 12px;">{{ $update->description }}</p>
                <p class="muted" style="margin:0;">Tanggal lapangan: {{ $update->update_date->translatedFormat('d F Y') }}</p>
                @if($update->status_after_update)
                    <p class="muted" style="margin: 8px 0 0;">Status pesanan: {{ (new \App\Models\Order(['status' => $update->status_after_update]))->status_label }}</p>
                @endif
            </div>

            <a href="{{ route('order.success', $order) }}" class="btn">Lihat Detail Pesanan</a>

            <p class="muted" style="margin-top: 24px;">Email ini dikirim otomatis oleh {{ config('app.business_name') }}.</p>
        </div>
    </div>
</body>
</html>
