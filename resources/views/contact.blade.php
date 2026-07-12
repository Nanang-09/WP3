@extends('layouts.app')

@section('title', 'Hubungi Kami - WeldTrack')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Hubungi Kami</h1>
        <p>Kami siap membantu mewujudkan proyek konstruksi impian Anda</p>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <span>/</span>
            <span>Kontak</span>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info-card fade-in">
                <h3 style="font-size: 1.3rem; margin-bottom: 28px;">Informasi Kontak</h3>
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h4>Alamat</h4>
                        <p>Jl. Ahmad Yani, Gerimax Indah, Kec. Narmada<br>Kabupaten Lombok Barat, Nusa Tenggara Barat</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h4>Telepon</h4>
                        <p>+62 878-6541-0555</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4>Email</h4>
                        <p>rifaifarid@gmail.com</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h4>Jam Kerja</h4>
                        <p>Senin - Sabtu: 08.00 - 17.00<br>Minggu: Tutup</p>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <a href="https://wa.me/6287865410555" class="btn btn-primary" style="width: 100%;" target="_blank">
                        <i class="fab fa-whatsapp"></i> Chat WhatsApp
                    </a>
                </div>
            </div>

            <div class="contact-form-card fade-in">
                <h3 style="font-size: 1.3rem; margin-bottom: 28px;">Kirim Pesan</h3>
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap *</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email" value="{{ old('email') }}" required>
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="phone">Telepon</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Masukkan no. telepon" value="{{ old('phone') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subject">Subjek *</label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="Subjek pesan" value="{{ old('subject') }}" required>
                            @error('subject') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="message">Pesan *</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Tuliskan pesan Anda..." required>{{ old('message') }}</textarea>
                        @error('message') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
