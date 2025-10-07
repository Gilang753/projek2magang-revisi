@extends('layouts.app')

@section('title', 'User')

@section('content')
<div class="container">
    <h2 class="mb-3 fw-bold">Inputkan Nilai Kategori yang Anda inginkan</h2>
    
    <!-- Form Input dan Eksekusi Rule -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('user.executeRule') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="harga" class="form-label">Input Harga</label>
                        <input type="number" step="any" min="0" name="harga" id="harga" class="form-control" required 
                               value="{{ isset($hargaInput) ? $hargaInput : old('harga') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rating" class="form-label">Input Rating</label>
                        <input type="number" step="any" min="0" max="100" name="rating" id="rating" class="form-control" required
                               value="{{ isset($ratingInput) ? $ratingInput : old('rating') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rasa" class="form-label">Input Rasa</label>
                        <input type="number" step="any" min="0" max="100" name="rasa" id="rasa" class="form-control" required
                               value="{{ isset($rasaInput) ? $rasaInput : old('rasa') }}">
                    </div>
                </div>
                
                <p class="text-muted mt-3" style="font-size: 1rem; line-height: 1.5;">
                    <span class="fw-bold">Keterangan Rasa:</span><br>
                    0 - 25 = Asam<br>
                    26 - 50 = Manis<br>
                    51 - 75 = Pedas<br>
                    76 - 100 = Asin
                </p>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-play-circle me-1"></i> Eksekusi
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Menu Makanan -->
    <h2 class="mb-3 fw-bold">Daftar Menu Makanan</h2>
        @if(isset($menus) && count($menus) > 0)
        <div class="row">
            
        @foreach($menus as $menu)
        
        <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <a href="{{ route('user.show', $menu->id) }}" class="text-decoration-none text-dark">
                    @if($menu->gambar)
                        <img src="{{ asset('storage/'.$menu->gambar) }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $menu->nama }}">
                    @else
                        <img src="{{ asset('images/tidak-ada-gambar.jpg') }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Tidak ada gambar">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $menu->nama }}</h5>
                        <p class="card-text text-muted mb-2">
                            {{ Str::limit($menu->deskripsi, 100) }}
                        </p>
                        <p class="card-text">
                            <strong>Rp {{ number_format($menu->harga_seporsi, 0, ',', '.') }}</strong><br>
                            <span class="text-muted">{{ ucfirst($menu->cita_rasa) }}</span>
                        </p>
                        <div class="text-warning">
                            {{ str_repeat('★', $menu->rating) }}{{ str_repeat('☆', 5 - $menu->rating) }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
        </div>
        @else
            <div class="col-12">
                <p class="text-muted">Belum ada menu yang tersedia.</p>
            </div>
        @endif
    </div>
</div>
@endsection