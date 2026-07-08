@extends('layouts.app')

@section('title', 'Pengaturan Akun - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Pengaturan Akun</h1>
        <p>Perbarui informasi profil dan kata sandi akun Anda</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <span>Pengaturan Akun</span>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="contact-grid">
                {{-- Kolom Kiri: Data Profil --}}
                <div class="contact-form-card fade-in">
                    <h3 style="font-size: 1.3rem; margin-bottom: 28px;">
                        <i class="fas fa-user-cog" style="color: var(--accent-blue); margin-right: 8px;"></i> Informasi Profil
                    </h3>
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap *</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name', $user->name) }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email *</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Alamat Email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">No. Telepon / WhatsApp</label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('phone', $user->phone) }}">
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="address">Alamat Proyek / Rumah</label>
                        <textarea id="address" name="address" class="form-control" placeholder="Alamat lengkap Anda..." style="min-height: 100px;">{{ old('address', $user->address) }}</textarea>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Kolom Kanan: Keamanan & Kata Sandi --}}
                <div class="contact-info-card fade-in" style="background: var(--surface); color: inherit;">
                    <h3 style="font-size: 1.3rem; margin-bottom: 28px;">
                        <i class="fas fa-shield-alt" style="color: var(--accent-blue); margin-right: 8px;"></i> Keamanan Akun
                    </h3>
                    
                    @if($user->provider_name)
                        <div class="summary-note" style="margin-bottom: 20px; background: var(--bg-soft); color: var(--text-secondary);">
                            <i class="fab fa-google" style="color: #ea4335; margin-right: 6px;"></i> Akun Anda terhubung dengan Google. 
                            Anda dapat menetapkan kata sandi lokal di bawah ini untuk opsi login tambahan.
                        </div>
                    @endif
                    
                    @if($user->password)
                        <div class="form-group">
                            <label class="form-label" for="current_password">Kata Sandi Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Masukkan kata sandi saat ini">
                            @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    
                    <div class="form-group">
                        <label class="form-label" for="new_password">Kata Sandi Baru</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimal 8 karakter">
                        @error('new_password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="new_password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
