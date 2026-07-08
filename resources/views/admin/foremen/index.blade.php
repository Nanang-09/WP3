@extends('layouts.admin')

@section('title', 'Kelola Mandor')

@section('content')
<div class="page-title">
    <div class="page-heading">
        <h2><i class="fas fa-hard-hat"></i> Kelola Mandor</h2>
        <p class="page-subtitle">Buat akun petugas lapangan agar progres harian dan foto proyek bisa dikirim langsung dari lokasi.</p>
    </div>
</div>

<div class="content-grid">
    <div>
        <div class="admin-table-card">
            <div class="admin-table-header">
                <div class="admin-table-title">
                    <h3>Daftar mandor</h3>
                    <p>Mandor yang dibuat di sini bisa login ke panel lapangan dan mengirim update proyek.</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mandor</th>
                            <th>Kontak</th>
                            <th>Order Ditangani</th>
                            <th>Update Terkirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($foremen as $foreman)
                        <tr>
                            <td>
                                <div class="table-inline">
                                    <span class="table-primary">{{ $foreman->name }}</span>
                                    <span class="table-muted">{{ $foreman->email }}</span>
                                </div>
                            </td>
                            <td class="table-muted">{{ $foreman->phone ?: 'Belum diisi' }}</td>
                            <td>{{ $foreman->assigned_orders_count }}</td>
                            <td>{{ $foreman->order_updates_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-hard-hat"></i>
                                    <strong>Belum ada akun mandor</strong>
                                    Buat akun pertama supaya tim lapangan bisa mengirim update.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <div class="summary-card">
            <h3 class="panel-title"><i class="fas fa-user-plus"></i> Tambah Mandor</h3>
            <form action="{{ route('admin.foremen.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxx">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" placeholder="Alamat atau area kerja">{{ old('address') }}</textarea>
                    @error('address') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan Akun Mandor
                </button>
            </form>
            <div class="summary-note" style="margin-top: 16px;">
                Setelah akun dibuat, admin bisa menetapkan mandor ke pesanan tertentu dari halaman detail pesanan.
            </div>
        </div>
    </div>
</div>
@endsection
