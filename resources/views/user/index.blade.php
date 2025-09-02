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
                    <input type="number" step="any" min="0" name="harga" id="harga" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="rating" class="form-label">Input Rating</label>
                    <input type="number" step="any" min="0" max="100" name="rating" id="rating" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="rasa" class="form-label">Input Rasa</label>
                    <input type="number" step="any" min="0" max="100" name="rasa" id="rasa" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-play-circle me-1"></i> Eksekusi Rule
                </button>
            </form>
            <p class="text-muted mt-3" style="font-size: 1rem; line-height: 1.5;">
                <span class="fw-bold">Keterangan Rasa:</span><br>
                0 - 30 = Asam<br>
                20 - 40 = Manis<br>
                50 - 70 = Pedas<br>
                75 - 100 = Asin
            </p>

            @if(isset($inferenceResults) && count($inferenceResults) > 0)
                <hr>
                <h5 class="mb-3">Hasil Eksekusi Rule</h5>
                @foreach ($inferenceResults as $result)
                    <div class="mb-2">
                        IF Harga <strong>{{ $result['rule']->harga_fuzzy }}</strong> ({{ number_format($result['miu_harga'], 3) }})
                        And Rating <strong>{{ $result['rule']->rating_fuzzy }}</strong> ({{ number_format($result['miu_rating'], 3) }})
                        Then Menu <strong>{{ $result['menu']->nama }}</strong> ({{ number_format($result['alpha'], 3) }})
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection