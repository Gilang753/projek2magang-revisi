@extends('layouts.app')

@section('title', 'User')

@section('content')
<div class="container">
    <h2 class="mb-3">Daftar Menu</h2>
    <div class="row mb-4">
        @if(isset($menus) && count($menus) > 0)
            @foreach($menus as $menu)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @if(isset($menu->gambar) && $menu->gambar)
                            <img src="{{ asset('storage/' . $menu->gambar) }}" class="card-img-top" alt="{{ $menu->nama }}" style="height:180px;object-fit:cover;">
                        @else
                            <img src="https://via.placeholder.com/300x180?text=No+Image" class="card-img-top" alt="No Image" style="height:180px;object-fit:cover;">
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $menu->nama }}</h5>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <p class="text-muted">Belum ada menu yang tersedia.</p>
            </div>
        @endif
    </div>
    <h1 class="mb-4">Halaman User</h1>
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
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-play-circle me-1"></i> Eksekusi Rule
                </button>
            </form>

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
