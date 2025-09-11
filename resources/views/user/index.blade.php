    @if(isset($recommendedMenus) && count($recommendedMenus) > 0)
    <div class="alert alert-success mt-3">
        <h4 class="alert-heading">Rekomendasi Menu Terdekat (Max 10)</h4>
        <div class="row">
            @foreach($recommendedMenus as $rec)
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    @if($rec['menu']->gambar)
                        <img src="{{ asset('storage/'.$rec['menu']->gambar) }}" style="height: 60px; width: 60px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                    @endif
                    <div>
                        <strong>{{ $rec['menu']->nama }}</strong><br>
                        <span class="text-muted">Rp {{ number_format($rec['menu']->harga_seporsi, 0, ',', '.') }}</span><br>
                        <span class="badge bg-info text-dark">z_user: {{ $z_user }}, z_admin: {{ $rec['z_admin'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@extends('layouts.app')

@section('title', 'User')

@section('content')
<div class="container">
    <h2 class="mb-3">Daftar Menu Makanan</h2>
    <div class="row mb-4">
        @if(isset($menus) && count($menus) > 0)
        <div class="row">
        @foreach($menus as $menu)
        <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($menu->gambar)
                        <img src="{{ asset('storage/'.$menu->gambar) }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $menu->nama }}">
                    @else
                        <img src="{{ asset('images/tidak-ada-gambar.jpg') }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Tidak ada gambar">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $menu->nama }}</h5>
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
    <div class="card">
        <div class="card-body">
            <form action="{{ route('user.executeRule') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="harga" class="form-label">Input Harga</label>
                    <input type="number" step="any" min="0" name="harga" id="harga" class="form-control" required 
                           value="{{ isset($hargaInput) ? $hargaInput : old('harga') }}">
                </div>
                <div class="mb-3">
                    <label for="rating" class="form-label">Input Rating</label>
                    <input type="number" step="any" min="0" max="100" name="rating" id="rating" class="form-control" required
                           value="{{ isset($ratingInput) ? $ratingInput : old('rating') }}">
                </div>
                <div class="mb-3">
                    <label for="rasa" class="form-label">Input Rasa</label>
                    <input type="number" step="any" min="0" max="100" name="rasa" id="rasa" class="form-control" required
                           value="{{ isset($rasaInput) ? $rasaInput : old('rasa') }}">
                </div>
                <p class="text-muted mt-3" style="font-size: 1rem; line-height: 1.5;">
                    <span class="fw-bold">Keterangan Rasa:</span><br>
                    0 - 40 = Asam<br>
                    20 - 60 = Manis<br>
                    40 - 80 = Pedas<br>
                    60 - 100 = Asin
                </p>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-play-circle me-1"></i> Eksekusi Rule
                </button>
            </form>

            @if(isset($inferenceResults) && count($inferenceResults) > 0)
                {{-- Hasil Eksekusi --}}
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Hasil Eksekusi Rule</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">
                                Input: Harga Rp. {{ number_format($hargaInput, 0, ',', '.') }}, 
                                Rating {{ $ratingInput }},
                                Rasa {{ $rasaInput }}
                            </small>
                        </div>
                        
                        @foreach ($inferenceResults as $index => $result)
                            <div class="mb-2 p-2 border-bottom">
                                IF Harga <strong>{{ $result['rule']->harga_fuzzy }}</strong> 
                                ({{ number_format($result['miu_harga'], 3) }}) 
                                And Rating <strong>{{ $result['rule']->rating_fuzzy }}</strong> 
                                ({{ number_format($result['miu_rating'], 3) }}) 
                                And Rasa <strong>{{ $result['rule']->rasa_fuzzy }}</strong>
                                ({{ number_format($result['miu_rasa'], 3) }})
                                Then <strong>{{ $result['rekomendasi'] }}</strong> 
                                ({{ number_format($result['alpha'], 3) }})
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection