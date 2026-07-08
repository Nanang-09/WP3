@extends('layouts.app')

@section('title', 'Monitoring Progres Proyek - WeldTrack')

@section('content')
<section class="success-section" style="padding: 60px 0; background: linear-gradient(180deg, rgba(16,185,129,0.03) 0%, rgba(16,185,129,0) 100%);">
    <div class="container" style="max-width: 960px; margin: 0 auto; padding: 0 20px;">
        <div class="fade-in">

            {{-- ===== HEADER ===== --}}
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                    <div style="width: 80px; height: 80px; background: rgba(16,185,129,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        @if($order->status === \App\Models\Order::STATUS_COMPLETED)
                            <i class="fas fa-trophy" style="color: #f59e0b; font-size: 2.2rem;"></i>
                        @else
                            <i class="fas fa-hard-hat" style="color: #10b981; font-size: 2.2rem; animation: float 3s ease-in-out infinite;"></i>
                        @endif
                    </div>
                    <span style="position: absolute; bottom: 4px; right: 4px; width: 16px; height: 16px; background: {{ $order->status === \App\Models\Order::STATUS_COMPLETED ? '#f59e0b' : '#10b981' }}; border-radius: 50%; border: 3px solid var(--surface); box-shadow: 0 0 10px rgba(16,185,129,0.6); animation: pulse 2s infinite;"></span>
                </div>
                @if($order->status === \App\Models\Order::STATUS_COMPLETED)
                    <h1 style="font-family: var(--font-heading); font-size: 2rem; font-weight: 800; color: var(--text-primary); margin-bottom: 10px;">Proyek Selesai! 🎉</h1>
                    <p style="color: var(--text-secondary); max-width: 580px; margin: 0 auto; font-size: 1rem; line-height: 1.6;">
                        Terima kasih telah mempercayakan proyek Anda kepada WeldTrack. Proyek pengelasan Anda telah berhasil diselesaikan.
                    </p>
                @else
                    <h1 style="font-family: var(--font-heading); font-size: 2rem; font-weight: 800; color: var(--text-primary); margin-bottom: 10px;">Proyek Sedang Dikerjakan</h1>
                    <p style="color: var(--text-secondary); max-width: 580px; margin: 0 auto; font-size: 1rem; line-height: 1.6;">
                        Tim lapangan WeldTrack sedang mengerjakan proyek Anda. Pantau kemajuan pengerjaan secara berkala di halaman ini.
                    </p>
                @endif
            </div>

            {{-- ===== PROGRESS STEPPER ===== --}}
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-md); margin-bottom: 30px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; margin-bottom: 24px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-route" style="color: #10b981;"></i> Status Alur Transaksi
                </h3>
                <div style="display: flex; justify-content: space-between; position: relative; flex-wrap: wrap; gap: 20px;">
                    <div class="stepper-line" style="position: absolute; top: 22px; left: 40px; right: 40px; height: 3px; background: #10b981; z-index: 1;"></div>

                    @foreach([
                        ['label' => 'Pengajuan Pesanan', 'sub' => 'Sukses dikirim', 'done' => true],
                        ['label' => 'Jadwal Disepakati', 'sub' => 'Waktu survei deal', 'done' => true],
                        ['label' => 'Proses Konsultasi', 'sub' => 'Selesai', 'done' => true],
                        ['label' => 'Monitoring Proyek', 'sub' => $order->status === \App\Models\Order::STATUS_COMPLETED ? 'Selesai ✓' : 'Sedang Berjalan', 'done' => true, 'active' => $order->status !== \App\Models\Order::STATUS_COMPLETED, 'completed' => $order->status === \App\Models\Order::STATUS_COMPLETED],
                    ] as $i => $step)
                    <div style="flex: 1; text-align: center; z-index: 2; min-width: 100px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: {{ isset($step['completed']) && $step['completed'] ? '#f59e0b' : (isset($step['active']) && $step['active'] ? '#10b981' : '#10b981') }}; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid var(--surface); box-shadow: 0 0 0 2px {{ isset($step['completed']) && $step['completed'] ? '#f59e0b' : '#10b981' }}; {{ isset($step['active']) ? 'animation: pulse-shadow-green 2.5s infinite;' : '' }}">
                            @if(isset($step['completed']) && $step['completed'])
                                <i class="fas fa-trophy" style="font-size: 0.9rem;"></i>
                            @else
                                <i class="fas fa-check" style="font-size: 0.9rem;"></i>
                            @endif
                        </div>
                        <span style="font-weight: {{ isset($step['active']) ? '700' : '600' }}; font-size: 0.82rem; color: {{ isset($step['active']) ? '#10b981' : 'var(--text-primary)' }}; display: block;">{{ $step['label'] }}</span>
                        <small style="color: {{ isset($step['active']) ? '#10b981' : 'var(--text-muted)' }}; font-size: 0.7rem; font-weight: {{ isset($step['active']) ? '700' : '400' }};">{{ $step['sub'] }}</small>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ===== PROJECT INFO CARD ===== --}}
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); border-bottom: 1.5px dashed var(--border-color); padding-bottom: 15px;">
                    <i class="fas fa-clipboard-list" style="color: #10b981; margin-right: 6px;"></i> Detail Proyek
                </h3>

                @php $rows = [
                    ['label' => 'No. Pesanan', 'value' => $order->order_number, 'blue' => true],
                    ['label' => 'Layanan', 'value' => $order->service->name],
                    ['label' => 'Nama Pelanggan', 'value' => $order->name . ' (' . $order->phone . ')'],
                    ['label' => 'Lokasi Proyek', 'value' => $order->address],
                ]; @endphp

                @foreach($rows as $row)
                <div style="display: flex; margin-bottom: 14px; border-bottom: 1px solid rgba(226,232,240,0.5); padding-bottom: 11px;">
                    <span style="font-weight: 600; color: var(--text-secondary); width: 200px; flex-shrink: 0; font-size: 0.9rem;">{{ $row['label'] }}</span>
                    <span style="color: {{ isset($row['blue']) ? 'var(--accent-blue)' : 'var(--text-primary)' }}; font-weight: {{ isset($row['blue']) ? '700' : '500' }}; font-size: 0.9rem;">{{ $row['value'] }}</span>
                </div>
                @endforeach
                
                @if($order->estimated_cost > 0 || $order->project_price > 0)
                <div style="display: flex; margin-bottom: 14px; border-bottom: 1px solid rgba(226,232,240,0.5); padding-bottom: 11px;">
                    <span style="font-weight: 600; color: var(--text-secondary); width: 200px; flex-shrink: 0; font-size: 0.9rem;">Biaya Proyek</span>
                    <span style="font-weight: 700; color: #10b981; font-size: 0.9rem;">
                        Rp {{ number_format($order->project_price ?: $order->estimated_cost, 0, ',', '.') }}
                    </span>
                </div>
                @endif
                
                @if($order->materials->count() > 0)
                <div style="margin-bottom: 14px; border-bottom: 1px solid rgba(226,232,240,0.5); padding-bottom: 11px;">
                    <span style="font-weight: 600; color: var(--text-secondary); display: block; margin-bottom: 10px; font-size: 0.9rem;">Rincian Bahan (Transparansi Biaya)</span>
                    <div style="background: rgba(0,0,0,0.01); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px;">
                        <table style="width: 100%; font-size: 0.85rem; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px dashed var(--border-color); text-align: left;">
                                    <th style="padding-bottom: 8px; color: var(--text-secondary);">Bahan</th>
                                    <th style="padding-bottom: 8px; color: var(--text-secondary);">Spek</th>
                                    <th style="padding-bottom: 8px; color: var(--text-secondary); text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->materials as $mat)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                                    <td style="padding: 6px 0; font-weight: 600;">{{ $mat->material_name }}</td>
                                    <td style="padding: 6px 0; color: var(--text-muted);">{{ trim($mat->length . ' ' . $mat->shape) ?: '-' }}</td>
                                    <td style="padding: 6px 0; text-align: center;">{{ $mat->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Jadwal & Mandor --}}
                @if($order->project_start_date)
                <div style="display: flex; margin-bottom: 14px; border-bottom: 1px solid rgba(226,232,240,0.5); padding-bottom: 11px;">
                    <span style="font-weight: 600; color: var(--text-secondary); width: 200px; flex-shrink: 0; font-size: 0.9rem;">Jadwal Pengerjaan</span>
                    <span style="font-weight: 700; color: #10b981; font-size: 0.9rem;">
                        {{ $order->project_start_date->translatedFormat('d F Y') }} s/d {{ $order->project_end_date?->translatedFormat('d F Y') }}
                    </span>
                </div>
                @endif

                @if($order->foreman)
                <div style="display: flex; margin-bottom: 14px; border-bottom: 1px solid rgba(226,232,240,0.5); padding-bottom: 11px;">
                    <span style="font-weight: 600; color: var(--text-secondary); width: 200px; flex-shrink: 0; font-size: 0.9rem;">Tim Lapangan</span>
                    <span style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: rgba(16,185,129,0.1); border-radius: 50%; color: #10b981; font-size: 0.75rem;"><i class="fas fa-user-hard-hat"></i></span>
                        {{ $order->foreman->name }}
                    </span>
                </div>
                @endif

                @if($order->agreement_notes)
                <div style="display: flex;">
                    <span style="font-weight: 600; color: var(--text-secondary); width: 200px; flex-shrink: 0; font-size: 0.9rem;">Catatan Kesepakatan</span>
                    <span style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; font-style: italic;">{{ $order->agreement_notes }}</span>
                </div>
                @endif
            </div>

            {{-- ============================================================
                 SECTION: CATATAN ADMIN
                 ============================================================ --}}
            @if($order->admin_notes)
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 25px 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px; border-left: 5px solid #3b82f6;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                    <div style="background: rgba(59, 130, 246, 0.1); width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.1rem; flex-shrink: 0;">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Catatan dari Admin</h3>
                        <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Informasi tambahan dari pihak admin</p>
                    </div>
                </div>
                <div style="font-size: 0.9rem; line-height: 1.7; color: var(--text-secondary); white-space: pre-line;">{{ $order->admin_notes }}</div>
            </div>
            @endif

            {{-- ============================================================
                 SECTION: KEBUTUHAN PROYEK (Catatan Spesifikasi)
                 ============================================================ --}}
            @if($order->project_requirements)
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 25px 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px; border-left: 5px solid #3b82f6;">
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

            {{-- ============================================================
                 SECTION: FOTO REFERENSI MODEL
                 ============================================================ --}}
            @if($order->referencePhotos->isNotEmpty())
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 25px 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px; border-left: 5px solid #f59e0b;">
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
                                 onclick="openProgressLightbox('{{ $photo->photo_url }}', '{{ addslashes($photo->caption ?? '') }}')"
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


            {{-- ===== MILESTONE PROGRESS TRACKER ===== --}}
            <div style="background: var(--surface); border: 1.5px solid var(--border-color); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
                <h3 style="font-family: var(--font-heading); font-size: 1.2rem; font-weight: 700; margin-bottom: 8px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-chart-line" style="color: #10b981;"></i> Progres Pengerjaan Proyek
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 24px;">Foto dan laporan dari tim lapangan akan muncul di bawah setelah mandor mengirimkan update.</p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    @foreach([25, 50, 75, 100] as $percent)
                        @php $milestoneUpdate = $order->updates->where('progress_percent', $percent)->first(); @endphp
                        @if($milestoneUpdate)
                            <div style="background: var(--surface); border: 2px solid #10b981; border-radius: 16px; padding: 20px; text-align: center; box-shadow: var(--shadow-sm); position: relative;">
                                <span style="position: absolute; top: 12px; right: 12px; background: #10b981; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;"><i class="fas fa-check"></i></span>
                                <div style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; color: #10b981; margin-bottom: 10px;">{{ $percent }}%</div>
                                <div style="aspect-ratio: 4/3; overflow: hidden; border-radius: 10px; margin-bottom: 12px; border: 1px solid var(--border-color); background: #eee;">
                                    <button class="portfolio-zoom-trigger"
                                        data-lightbox-src="{{ $milestoneUpdate->photo_url }}"
                                        data-lightbox-caption="Progres {{ $percent }}% — {{ $milestoneUpdate->title }}"
                                        style="width: 100%; height: 100%; border: 0; padding: 0; cursor: zoom-in; display: block; overflow: hidden;">
                                        <img src="{{ $milestoneUpdate->photo_url }}" alt="Progres {{ $percent }}%" style="width: 100%; height: 100%; object-fit: cover;">
                                    </button>
                                </div>
                                <strong style="display: block; font-size: 0.92rem; margin-bottom: 4px; color: var(--text-primary);">{{ $milestoneUpdate->title }}</strong>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 8px; line-height: 1.4;">{{ $milestoneUpdate->description }}</p>
                                <span style="font-size: 0.73rem; color: var(--text-muted);">{{ $milestoneUpdate->update_date->translatedFormat('d F Y') }}</span>
                            </div>
                        @else
                            <div style="background: rgba(30,41,59,0.01); border: 1.5px dashed var(--border-color); border-radius: 16px; padding: 20px; text-align: center; opacity: 0.6; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 220px;">
                                <div style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 8px;">{{ $percent }}%</div>
                                <div style="font-size: 2rem; color: var(--text-muted); margin-bottom: 12px;"><i class="fas fa-lock"></i></div>
                                <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Belum Tercapai</span>
                                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">Menunggu laporan progres {{ $percent }}% dari lapangan</p>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Empty state when no updates at all --}}
                @if($order->updates->where('is_visible_to_customer', true)->isEmpty())
                <div style="text-align: center; padding: 30px 20px; border-top: 1px dashed var(--border-color); margin-top: 24px;">
                    <i class="fas fa-clock" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; display: block;"></i>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Update progres pertama dari tim lapangan akan segera muncul di sini.</p>
                </div>
                @endif
            </div>

            {{-- ===== WhatsApp CTA ===== --}}
            @php
                $waPhone = config('app.admin_whatsapp', '6287865410555');
                $waText = "Halo Admin WeldTrack 👋\n\nSaya ingin menanyakan perkembangan proyek saya:\nNo. Pesanan: *" . $order->order_number . "*\nLayanan: *" . $order->service->name . "*";
                $waUrl = "https://wa.me/" . $waPhone . "?text=" . rawurlencode($waText);
            @endphp

            <div style="background: linear-gradient(135deg, rgba(37,211,102,0.08) 0%, rgba(18,140,67,0.08) 100%); border: 1.5px solid rgba(37,211,102,0.35); border-radius: 20px; padding: 24px; text-align: center; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fab fa-whatsapp" style="color: #25d366; font-size: 1.7rem;"></i>
                    <strong style="font-size: 1rem; color: #15803d; font-weight: 700;">Butuh update atau ada pertanyaan?</strong>
                </div>
                <p style="font-size: 0.85rem; color: #166534; margin-bottom: 16px; max-width: 480px; margin-inline: auto;">
                    Hubungi admin WeldTrack langsung via WhatsApp untuk menanyakan perkembangan terbaru proyek Anda.
                </p>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   style="display: inline-flex; align-items: center; gap: 8px; background: #25d366; color: #fff; text-decoration: none; font-weight: 700; font-size: 0.95rem; padding: 12px 24px; border-radius: 10px; transition: all 0.2s; box-shadow: 0 4px 10px rgba(37,211,102,0.2);"
                   onmouseover="this.style.background='#128c43'" onmouseout="this.style.background='#25d366'">
                    <i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i> Tanya Update via WhatsApp
                </a>
            </div>

            {{-- ===== FOOTER ACTIONS ===== --}}
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
    </div>
</section>

{{-- Progress Lightbox Modal --}}
<div id="progress-lightbox" onclick="closeProgressLightbox()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center; flex-direction: column; gap: 12px; padding: 20px;">
    <button onclick="closeProgressLightbox(); event.stopPropagation()" style="position: absolute; top: 16px; right: 20px; background: rgba(255,255,255,0.15); border: none; color: white; border-radius: 50%; width: 36px; height: 36px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-times"></i>
    </button>
    <img id="progress-lightbox-img" src="" alt="" style="max-width: 90vw; max-height: 80vh; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); object-fit: contain;">
    <p id="progress-lightbox-caption" style="color: #e2e8f0; font-size: 0.9rem; text-align: center; max-width: 600px;"></p>
</div>

<style>
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    @keyframes pulse-shadow-green {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    @media (max-width: 600px) {
        .stepper-line { display: none !important; }
    }
</style>
@endsection

@section('scripts')
<script>
    function openProgressLightbox(src, caption) {
        const lb = document.getElementById('progress-lightbox');
        if (!lb) return;
        document.getElementById('progress-lightbox-img').src = src;
        document.getElementById('progress-lightbox-caption').textContent = caption;
        lb.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeProgressLightbox() {
        const lb = document.getElementById('progress-lightbox');
        if (lb) lb.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Attach to existing milestone zoom triggers
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.portfolio-zoom-trigger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const src = this.getAttribute('data-lightbox-src');
                const caption = this.getAttribute('data-lightbox-caption');
                openProgressLightbox(src, caption);
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeProgressLightbox();
    });
</script>
@endsection

