@extends('layouts.admin')

@section('title', 'Pesan Masuk')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-envelope"></i> Pesan Masuk</h2>
        <p class="page-subtitle">Daftar kontak dibuat lebih sederhana agar pesan baru mudah dikenali dan dibuka.</p>
    </div>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <div class="admin-table-title">
            <h3>Daftar pesan</h3>
            <p>Titik biru menandakan pesan yang belum dibuka.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Subjek</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr>
                    <td>
                        @if(!$contact->is_read) <span class="unread-badge"></span> @endif
                    </td>
                    <td class="{{ !$contact->is_read ? 'table-primary' : '' }}">{{ $contact->name }}</td>
                    <td class="table-muted">{{ $contact->email }}</td>
                    <td>{{ $contact->subject }}</td>
                    <td class="table-muted">{{ $contact->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i> Buka
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <strong>Belum ada pesan masuk</strong>
                            Pesan dari formulir kontak akan muncul di sini.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contacts->hasPages())
    <div class="pagination-wrapper">
        {{ $contacts->links() }}
    </div>
    @endif
</div>
@endsection
