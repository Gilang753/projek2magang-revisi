@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Makanan')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Rekomendasi Menu Makanan</h2>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Tampilkan Inputan User -->
    @if(isset($hargaInput) || isset($ratingInput) || isset($rasaInput))
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 fw-bold">Kriteria Pencarian Anda</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @if(isset($hargaInput))
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="fw-bold text-primary">Harga</h6>
                        <p class="mb-0 fs-5">Rp {{ number_format($hargaInput, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif
                
                @if(isset($ratingInput))
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="fw-bold text-primary">Rating</h6>
                        <p class="mb-0 fs-5">
                            @php
                                $rating = $ratingInput;
                                $fullStars = floor($rating / 20); // Konversi 0-100 ke 0-5 bintang
                                $emptyStars = 5 - $fullStars;
                            @endphp
                            <span class="text-warning">
                                {{ str_repeat('★', $fullStars) }}{{ str_repeat('☆', $emptyStars) }}
                            </span>
                            ({{ $rating }})
                        </p>
                    </div>
                </div>
                @endif
                
                @if(isset($rasaInput))
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="fw-bold text-primary">Rasa</h6>
                        <p class="mb-0 fs-5">
                            @php
                                $rasa = '';
                                if ($rasaInput >= 0 && $rasaInput <= 25) {
                                    $rasa = 'Asam';
                                } elseif ($rasaInput > 25 && $rasaInput <= 50) {
                                    $rasa = 'Manis';
                                } elseif ($rasaInput > 50 && $rasaInput <= 75) {
                                    $rasa = 'Pedas';
                                } elseif ($rasaInput > 75 && $rasaInput <= 100) {
                                    $rasa = 'Asin';
                                } else {
                                    $rasa = 'Tidak diketahui';
                                }
                            @endphp
                            {{ $rasa }} ({{ $rasaInput }})
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($recommendedMenus) && count($recommendedMenus) > 0)
    <div class="alert alert-success mt-3">
        <div class="row">
            @foreach($recommendedMenus as $i => $rec)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 position-relative p-0">
                        @if($rec['menu']->gambar)
                            <img src="{{ asset('storage/'.$rec['menu']->gambar) }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;" 
                                 alt="{{ $rec['menu']->nama }}">
                        @else
                            <img src="{{ asset('images/tidak-ada-gambar.jpg') }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;" 
                                 alt="Tidak ada gambar">
                        @endif
                        <span class="position-absolute top-0 start-0 bg-primary text-white px-3 py-2 rounded-end">
                            #{{ $i+1 }}
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold">{{ $rec['menu']->nama }}</h5>
                        
                        <p class="card-text text-muted small">
                            {{ Str::limit($rec['menu']->deskripsi, 100) }}
                        </p>
                        
                        <div class="menu-details">
                            <div class="row small">
                                <div class="col-12 mb-2">
                                    <strong>Harga:</strong> Rp {{ number_format($rec['menu']->harga_seporsi, 0, ',', '.') }}
                                </div>
                                <div class="col-12 mb-2">
                                    <strong>Rasa:</strong> {{ ucfirst($rec['menu']->cita_rasa) }}
                                </div>
                                <div class="col-12 mb-2">
                                    <strong>Rating:</strong> 
                                    <span class="text-warning">
                                        @php
                                            $rating = $rec['menu']->rating ?? 0;
                                            $fullStars = floor($rating);
                                            $emptyStars = 5 - $fullStars;
                                        @endphp
                                        {{ str_repeat('★', $fullStars) }}{{ str_repeat('☆', $emptyStars) }}
                                    </span>
                                    ({{ $rating }})
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert alert-warning text-center">
        <h5>Tidak ada rekomendasi yang ditemukan</h5>
        <p class="mb-0">Coba ubah kriteria pencarian Anda</p>
    </div>
    @endif

</div>

<style>
.card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #e0e0e0;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.menu-details {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    margin: 10px 0;
}

.card-img-top {
    border-bottom: 1px solid #e0e0e0;
}

/* Styling untuk card kriteria */
.card .card-header {
    border-bottom: 2px solid #007bff;
}
</style>
@endsection