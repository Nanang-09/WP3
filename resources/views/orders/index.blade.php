@extends('layouts.app')

@section('title', 'Pesanan & Progres - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Pesanan & Progres</h1>
        <p>Lihat layanan yang sedang Anda pesan dan pantau perubahan status serta progres proyek secara real time.</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <span>Pesanan & Progres</span>
        </div>
    </div>
</section>

<section class="order-section">
    <div class="container">
        <div class="customer-orders-shell fade-in">

            {{-- Tab Navigation --}}
            <div style="display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 0; gap: 0;">
                <button id="tab-btn-active" onclick="switchTab('active')"
                    style="padding: 13px 24px; font-size: 0.92rem; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.2s; color: #64748b; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-list-check"></i>
                    Pesanan Aktif
                    <span id="tab-active-count" style="background: #3b82f6; color: white; border-radius: 20px; padding: 1px 8px; font-size: 0.78rem; font-weight: 700; min-width: 20px; text-align: center;">{{ count($activeOrders) }}</span>
                </button>
                <button id="tab-btn-history" onclick="switchTab('history')"
                    style="padding: 13px 24px; font-size: 0.92rem; font-weight: 600; border: none; background: none; cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.2s; color: #64748b; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-history"></i>
                    Riwayat
                    @if($totalHistoryCount > 0)
                        <span style="background: #10b981; color: white; border-radius: 20px; padding: 1px 8px; font-size: 0.78rem; font-weight: 700; min-width: 20px; text-align: center;">{{ $totalHistoryCount }}</span>
                    @endif
                </button>
            </div>

            {{-- Tab: Pesanan Aktif --}}
            <div id="tab-active">
                <div class="customer-orders-header">
                    <div>
                        <h2><i class="fas fa-list-check"></i> Pesanan Aktif</h2>
                        <p>Halaman ini akan memperbarui data otomatis setiap 15 detik.</p>
                    </div>
                    <div class="realtime-badge">
                        <span class="realtime-dot"></span>
                        <span>Terakhir diperbarui: <strong id="lastUpdatedAt">{{ $lastUpdatedAt }}</strong></span>
                    </div>
                </div>

                <div id="orderRealtimeNotice" class="realtime-notice" hidden>
                    Data pesanan sedang diperbarui...
                </div>

                <div id="customerOrdersList" class="customer-orders-list">
                    @forelse($activeOrders as $order)
                        <article class="customer-order-card" data-order-id="{{ $order['id'] }}">
                            <div class="customer-order-top">
                                <div>
                                    <p class="customer-order-number">{{ $order['order_number'] }}</p>
                                    <h3>{{ $order['service_name'] }}</h3>
                                    <p class="customer-order-date">Dipesan pada {{ $order['created_at_label'] }}</p>
                                </div>
                                <span class="customer-order-status" style="--status-color: {{ $order['status_color'] }};">
                                    {{ $order['status_label'] }}
                                </span>
                            </div>

                            <div class="customer-order-grid">
                                <div class="customer-order-panel">
                                    <span class="customer-order-label">Yang dipesan</span>
                                    <p>{{ $order['description'] }}</p>
                                </div>
                                <div class="customer-order-panel">
                                    <span class="customer-order-label">Lokasi proyek</span>
                                    <p>{{ $order['address'] }}</p>
                                </div>
                            </div>

                            <div class="customer-order-meta">
                                @if($order['project_start_date_label'])
                                    <span><strong>Jadwal Proyek:</strong> {{ $order['project_start_date_label'] }} s/d {{ $order['project_end_date_label'] }}</span>
                                @else
                                    @if($order['consultation_date_label'])
                                        <span><strong>Jadwal Konsultasi:</strong> {{ $order['consultation_date_label'] }}, {{ $order['consultation_time'] }} ({{ $order['consultation_place'] }})</span>
                                    @else
                                        <span><strong>Usulan Konsultasi:</strong> {{ $order['preferred_consultation_date_label'] }}, {{ $order['preferred_consultation_time'] }}</span>
                                    @endif
                                @endif
                                <span><strong>Catatan Anda:</strong> {{ $order['notes'] ?: 'Tidak ada catatan tambahan.' }}</span>
                                <span><strong>Tim Lapangan:</strong> {{ $order['foreman_name'] ?? 'Belum ditetapkan' }}</span>
                                <span><strong>Update lapangan:</strong> {{ $order['progress_updates_count'] }} update</span>
                            </div>

                            @if($order['queue_position'])
                                <div class="customer-order-queue">
                                    <span>Posisi antrean: <strong>#{{ $order['queue_position'] }}</strong></span>
                                    <span>Pesanan di depan: <strong>{{ $order['orders_ahead_count'] }}</strong></span>
                                    <span>Estimasi tunggu: <strong>{{ $order['estimated_wait_label'] }}</strong></span>
                                </div>
                            @endif

                            @if($order['admin_notes'])
                                <div class="customer-order-admin-note">
                                    <strong>Catatan admin</strong>
                                    <p>{{ $order['admin_notes'] }}</p>
                                </div>
                            @endif

                            @if(!empty($order['updates']))
                                <div class="progress-timeline">
                                    <h4 class="timeline-title"><i class="fas fa-camera-retro"></i> Update Lapangan Terbaru</h4>
                                    @foreach($order['updates'] as $update)
                                        <div class="timeline-item">
                                            <div class="timeline-item-top">
                                                <div>
                                                    <strong>{{ $update['title'] }}</strong>
                                                    <p class="muted-text">{{ $update['update_date_label'] }} oleh {{ $update['updated_by'] }}</p>
                                                </div>
                                                @if($update['progress_percent'] !== null)
                                                    <span class="progress-pill">{{ $update['progress_percent'] }}%</span>
                                                @endif
                                            </div>
                                            <p>{{ $update['description'] }}</p>
                                            @if($update['status_after_update_label'])
                                                <p class="muted-text" style="margin-top: 8px;">Status setelah update: {{ $update['status_after_update_label'] }}</p>
                                            @endif
                                            @if($update['photo_url'])
                                                <img src="{{ $update['photo_url'] }}" alt="{{ $update['title'] }}" class="timeline-photo"
                                                     onclick="openPhotoLightbox('{{ $update['photo_url'] }}', '{{ addslashes($update['title']) }}')"
                                                     title="Klik untuk melihat foto lebih besar">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="customer-order-actions">
                                <a href="{{ $order['detail_url'] }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                                @if($order['can_approve_alternative'])
                                    <button type="button" class="btn btn-primary btn-sm" style="background: #2563eb; border-color: #2563eb; color: #fff;" onclick="approveAlternative('{{ $order['accept_alternative_url'] }}')">
                                        <i class="fas fa-check-double" style="margin-right: 4px;"></i> Setujui Jadwal
                                    </button>
                                @endif
                                @if($order['can_edit'])
                                    <a href="{{ $order['edit_url'] }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit" style="margin-right: 4px;"></i> Edit
                                    </a>
                                @endif
                                @if($order['can_cancel'])
                                    <button type="button" class="btn btn-danger btn-sm" onclick="cancelOrder('{{ $order['cancel_url'] }}')">
                                        <i class="fas fa-times-circle" style="margin-right: 4px;"></i> Batalkan
                                    </button>
                                @endif
                                <a href="{{ route('services.show', $order['service_slug']) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-briefcase"></i> Detail Layanan
                                </a>
                            </div>
                        </article>
                    @empty
                        <div id="customerOrdersEmpty" class="customer-orders-empty">
                            <i class="fas fa-box-open"></i>
                            <h3>Belum ada pesanan aktif</h3>
                            <p>Setelah Anda membuat pemesanan, detail layanan dan progresnya akan tampil di sini.</p>
                            <a href="{{ route('services.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Lihat Layanan
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tab: Riwayat --}}
            <div id="tab-history" style="display: none;">
                <div class="customer-orders-header" style="border-bottom: none; padding-bottom: 0;">
                    <div>
                        <h2><i class="fas fa-history"></i> Riwayat Pesanan Saya</h2>
                        <p>Menampilkan 10 pesanan terakhir yang sudah selesai atau dibatalkan.</p>
                    </div>
                </div>

                <div id="historyOrdersList" class="customer-orders-list">
                    @forelse($historyOrders as $order)
                        <article class="customer-order-card" style="opacity: 0.9;">
                            <div class="customer-order-top">
                                <div>
                                    <p class="customer-order-number">{{ $order['order_number'] }}</p>
                                    <h3>{{ $order['service_name'] }}</h3>
                                    <p class="customer-order-date">Dipesan pada {{ $order['created_at_label'] }}</p>
                                </div>
                                <span class="customer-order-status" style="--status-color: {{ $order['status_color'] }};">
                                    {{ $order['status_label'] }}
                                </span>
                            </div>

                            <div class="customer-order-grid">
                                <div class="customer-order-panel">
                                    <span class="customer-order-label">Yang dipesan</span>
                                    <p>{{ $order['description'] }}</p>
                                </div>
                                <div class="customer-order-panel">
                                    <span class="customer-order-label">Lokasi proyek</span>
                                    <p>{{ $order['address'] }}</p>
                                </div>
                            </div>

                            <div class="customer-order-meta">
                                @if($order['project_start_date_label'])
                                    <span><strong>Jadwal Proyek:</strong> {{ $order['project_start_date_label'] }} s/d {{ $order['project_end_date_label'] }}</span>
                                @endif
                                <span><strong>Tim Lapangan:</strong> {{ $order['foreman_name'] ?? 'Belum ditetapkan' }}</span>
                            </div>

                            @if($order['admin_notes'])
                                <div class="customer-order-admin-note">
                                    <strong>Catatan admin</strong>
                                    <p>{{ $order['admin_notes'] }}</p>
                                </div>
                            @endif

                            <div class="customer-order-actions">
                                <a href="{{ $order['detail_url'] }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                                <a href="{{ route('services.show', $order['service_slug']) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-briefcase"></i> Pesan Lagi
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="customer-orders-empty">
                            <i class="fas fa-history"></i>
                            <h3>Belum ada riwayat pesanan</h3>
                            <p>Pesanan yang sudah selesai atau dibatalkan akan tampil di sini.</p>
                        </div>
                    @endforelse

                    @if($totalHistoryCount > 10)
                        <div style="text-align: center; padding: 16px 0; color: #94a3b8; font-size: 0.88rem;">
                            <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                            Menampilkan 10 dari <strong>{{ $totalHistoryCount }}</strong> riwayat pesanan Anda.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Lightbox Modal untuk Foto Progres --}}
<div id="photoLightbox" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.88); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); align-items:center; justify-content:center; cursor:zoom-out;" onclick="closePhotoLightbox()">
    <div style="position:relative; max-width:92vw; max-height:92vh; display:flex; flex-direction:column; align-items:center; gap:12px;" onclick="event.stopPropagation()">
        <button onclick="closePhotoLightbox()" style="position:absolute; top:-14px; right:-14px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); border:1.5px solid rgba(255,255,255,0.3); color:#fff; font-size:1.1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s; z-index:10;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">&times;</button>
        <img id="photoLightboxImg" src="" alt="" style="max-width:88vw; max-height:80vh; object-fit:contain; border-radius:10px; box-shadow:0 8px 48px rgba(0,0,0,0.6); display:block;">
        <p id="photoLightboxCaption" style="color:rgba(255,255,255,0.85); font-size:0.9rem; text-align:center; max-width:80vw; margin:0;"></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ---- Tab switching ----
    const TAB_ACTIVE_BTN_STYLE_ON  = 'border-bottom: 3px solid #3b82f6; color: #3b82f6;';
    const TAB_ACTIVE_BTN_STYLE_OFF = 'border-bottom: 3px solid transparent; color: #64748b;';

    function switchTab(tab) {
        const tabActive   = document.getElementById('tab-active');
        const tabHistory  = document.getElementById('tab-history');
        const btnActive   = document.getElementById('tab-btn-active');
        const btnHistory  = document.getElementById('tab-btn-history');

        if (tab === 'active') {
            tabActive.style.display  = '';
            tabHistory.style.display = 'none';
            btnActive.style.cssText  += TAB_ACTIVE_BTN_STYLE_ON;
            btnHistory.style.cssText += TAB_ACTIVE_BTN_STYLE_OFF;
        } else {
            tabActive.style.display  = 'none';
            tabHistory.style.display = '';
            btnActive.style.cssText  += TAB_ACTIVE_BTN_STYLE_OFF;
            btnHistory.style.cssText += TAB_ACTIVE_BTN_STYLE_ON;
        }

        // Persist tab in session storage
        sessionStorage.setItem('orderTab', tab);
    }

    // Restore last tab
    const savedTab = sessionStorage.getItem('orderTab');
    if (savedTab === 'history') {
        switchTab('history');
    } else {
        switchTab('active');
    }

    // ---- Realtime polling (only for active orders) ----
    document.addEventListener('DOMContentLoaded', () => {
        const list        = document.getElementById('customerOrdersList');
        const notice      = document.getElementById('orderRealtimeNotice');
        const lastUpdatedAt = document.getElementById('lastUpdatedAt');
        const endpoint    = @json(route('order.data'));
        const serviceUrlTemplate = @json(route('services.show', ['service' => '__SERVICE_SLUG__']));
        const pollIntervalMs = {{ $pollIntervalMs }};
        let requestInFlight = false;

        const tabActiveCount = document.getElementById('tab-active-count');

        const escapeHtml = (value) => {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        };

        const buildServiceUrl = (slug) => serviceUrlTemplate.replace('__SERVICE_SLUG__', slug);

        const renderUpdates = (updates) => {
            if (!Array.isArray(updates) || !updates.length) return '';

            const items = updates.map((update) => `
                <div class="timeline-item">
                    <div class="timeline-item-top">
                        <div>
                            <strong>${escapeHtml(update.title)}</strong>
                            <p class="muted-text">${escapeHtml(update.update_date_label)} oleh ${escapeHtml(update.updated_by)}</p>
                        </div>
                        ${update.progress_percent !== null && update.progress_percent !== undefined
                            ? `<span class="progress-pill">${escapeHtml(String(update.progress_percent))}%</span>`
                            : ''}
                    </div>
                    <p>${escapeHtml(update.description)}</p>
                    ${update.status_after_update_label
                        ? `<p class="muted-text" style="margin-top: 8px;">Status setelah update: ${escapeHtml(update.status_after_update_label)}</p>`
                        : ''}
                    ${update.photo_url
                        ? `<img src="${escapeHtml(update.photo_url)}" alt="${escapeHtml(update.title)}" class="timeline-photo" onclick="openPhotoLightbox('${escapeHtml(update.photo_url)}', '${escapeHtml(update.title)}')" title="Klik untuk melihat foto lebih besar">`
                        : ''}
                </div>
            `).join('');

            return `
                <div class="progress-timeline">
                    <h4 class="timeline-title"><i class="fas fa-camera-retro"></i> Update Lapangan Terbaru</h4>
                    ${items}
                </div>
            `;
        };

        const renderOrderCard = (order) => {
            const queueSection = order.queue_position
                ? `<div class="customer-order-queue">
                        <span>Posisi antrean: <strong>#${escapeHtml(String(order.queue_position))}</strong></span>
                        <span>Pesanan di depan: <strong>${escapeHtml(String(order.orders_ahead_count ?? 0))}</strong></span>
                        <span>Estimasi tunggu: <strong>${escapeHtml(order.estimated_wait_label ?? '-')}</strong></span>
                   </div>`
                : '';

            const adminNotesSection = order.admin_notes
                ? `<div class="customer-order-admin-note">
                       <strong>Catatan admin</strong>
                       <p>${escapeHtml(order.admin_notes)}</p>
                   </div>`
                : '';

            const approveAlternativeBtn = order.can_approve_alternative
                ? `<button type="button" class="btn btn-primary btn-sm" style="background: #2563eb; border-color: #2563eb; color: #fff;" onclick="approveAlternative('${escapeHtml(order.accept_alternative_url)}')"><i class="fas fa-check-double" style="margin-right: 4px;"></i> Setujui Jadwal</button>`
                : '';

            const editBtn = order.can_edit
                ? `<a href="${escapeHtml(order.edit_url)}" class="btn btn-primary btn-sm"><i class="fas fa-edit" style="margin-right: 4px;"></i> Edit</a>`
                : '';

            const cancelBtn = order.can_cancel
                ? `<button type="button" class="btn btn-danger btn-sm" onclick="cancelOrder('${escapeHtml(order.cancel_url)}')"><i class="fas fa-times-circle" style="margin-right: 4px;"></i> Batalkan</button>`
                : '';

            return `
                <article class="customer-order-card" data-order-id="${escapeHtml(String(order.id))}">
                    <div class="customer-order-top">
                        <div>
                            <p class="customer-order-number">${escapeHtml(order.order_number)}</p>
                            <h3>${escapeHtml(order.service_name)}</h3>
                            <p class="customer-order-date">Dipesan pada ${escapeHtml(order.created_at_label)}</p>
                        </div>
                        <span class="customer-order-status" style="--status-color: ${escapeHtml(order.status_color)};">
                            ${escapeHtml(order.status_label)}
                        </span>
                    </div>
                    <div class="customer-order-grid">
                        <div class="customer-order-panel">
                            <span class="customer-order-label">Yang dipesan</span>
                            <p>${escapeHtml(order.description)}</p>
                        </div>
                        <div class="customer-order-panel">
                            <span class="customer-order-label">Lokasi proyek</span>
                            <p>${escapeHtml(order.address)}</p>
                        </div>
                    </div>
                    <div class="customer-order-meta">
                        ${order.project_start_date_label
                            ? `<span><strong>Jadwal Proyek:</strong> ${escapeHtml(order.project_start_date_label)} s/d ${escapeHtml(order.project_end_date_label)}</span>`
                            : (order.consultation_date_label
                                ? `<span><strong>Jadwal Konsultasi:</strong> ${escapeHtml(order.consultation_date_label)}, ${escapeHtml(order.consultation_time)} (${escapeHtml(order.consultation_place)})</span>`
                                : `<span><strong>Usulan Konsultasi:</strong> ${escapeHtml(order.preferred_consultation_date_label)}, ${escapeHtml(order.preferred_consultation_time)}</span>`
                              )
                        }
                        <span><strong>Catatan Anda:</strong> ${escapeHtml(order.notes || 'Tidak ada catatan tambahan.')}</span>
                        <span><strong>Tim Lapangan:</strong> ${escapeHtml(order.foreman_name || 'Belum ditetapkan')}</span>
                        <span><strong>Update lapangan:</strong> ${escapeHtml(String(order.progress_updates_count ?? 0))} update</span>
                    </div>
                    ${queueSection}
                    ${adminNotesSection}
                    ${renderUpdates(order.updates)}
                    <div class="customer-order-actions">
                        <a href="${escapeHtml(order.detail_url)}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                        ${approveAlternativeBtn}
                        ${editBtn}
                        ${cancelBtn}
                        <a href="${escapeHtml(buildServiceUrl(order.service_slug))}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-briefcase"></i> Detail Layanan
                        </a>
                    </div>
                </article>
            `;
        };

        const renderEmptyState = () => `
            <div id="customerOrdersEmpty" class="customer-orders-empty">
                <i class="fas fa-box-open"></i>
                <h3>Belum ada pesanan aktif</h3>
                <p>Setelah Anda membuat pemesanan, detail layanan dan progresnya akan tampil di sini.</p>
                <a href="{{ route('services.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Layanan
                </a>
            </div>
        `;

        const refreshOrders = async () => {
            if (requestInFlight) return;

            requestInFlight = true;
            notice.hidden = false;

            try {
                const response = await fetch(endpoint, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data pesanan');

                const payload = await response.json();
                const activeOrders = Array.isArray(payload.active_orders) ? payload.active_orders : [];

                list.innerHTML = activeOrders.length
                    ? activeOrders.map(renderOrderCard).join('')
                    : renderEmptyState();

                // Update tab count badge
                if (tabActiveCount) {
                    tabActiveCount.textContent = activeOrders.length;
                }

                if (payload.last_updated_at && lastUpdatedAt) {
                    lastUpdatedAt.textContent = payload.last_updated_at;
                }
            } catch (error) {
                console.error(error);
            } finally {
                requestInFlight = false;
                notice.hidden = true;
            }
        };

        window.setInterval(refreshOrders, pollIntervalMs);
    });
</script>
<form id="globalCancelOrderForm" action="" method="POST" style="display: none;">
    @csrf
</form>
<form id="globalAcceptAlternativeForm" action="" method="POST" style="display: none;">
    @csrf
</form>

<style>
.timeline-photo {
    cursor: zoom-in;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    border-radius: 8px;
}
.timeline-photo:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 20px rgba(0,0,0,0.18);
    opacity: 0.92;
}
</style>

<script>
function openPhotoLightbox(src, caption) {
    const lb = document.getElementById('photoLightbox');
    document.getElementById('photoLightboxImg').src = src;
    document.getElementById('photoLightboxImg').alt = caption || '';
    document.getElementById('photoLightboxCaption').textContent = caption || '';
    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePhotoLightbox() {
    document.getElementById('photoLightbox').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('photoLightboxImg').src = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePhotoLightbox();
});

function cancelOrder(actionUrl) {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
        const form = document.getElementById('globalCancelOrderForm');
        form.action = actionUrl;
        form.submit();
    }
}

function approveAlternative(actionUrl) {
    if (confirm('Apakah Anda setuju dengan jadwal alternatif tersebut dan ingin memulai pengerjaan proyek?')) {
        const form = document.getElementById('globalAcceptAlternativeForm');
        form.action = actionUrl;
        form.submit();
    }
}
</script>
@endsection
