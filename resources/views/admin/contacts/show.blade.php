@extends('layouts.admin')

@section('title', 'Detail Pesan')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-envelope-open"></i> Detail Pesan</h2>
        <p class="page-subtitle">Informasi pengirim dan isi pesan dipisahkan agar lebih nyaman dibaca.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="section-stack" style="max-width: 760px;">
    <div class="detail-card">
        <h3><i class="fas fa-user"></i> Informasi Pengirim</h3>
        <div class="detail-row">
            <span class="detail-label">Nama</span>
            <span class="detail-value">{{ $contact->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ $contact->email }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Telepon</span>
            <span class="detail-value">{{ $contact->phone ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Tanggal</span>
            <span class="detail-value">{{ $contact->created_at->format('d F Y, H:i') }}</span>
        </div>
    </div>

    <div class="detail-card">
        <h3><i class="fas fa-comment-alt"></i> {{ $contact->subject }}</h3>
        <p style="white-space: pre-line;">{{ $contact->message }}</p>
    </div>

    <div>
        <a href="mailto:{{ $contact->email }}" class="btn btn-primary">
            <i class="fas fa-reply"></i> Balas via Email
        </a>
    </div>
</div>
@endsection
