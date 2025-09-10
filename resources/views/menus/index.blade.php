@extends('layouts.app')

@section('title', 'Daftar Menu')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Menu Makanan</h1>
        <a href="{{ route('menus.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Menu
        </a>
    </div>

    <div class="row">
        @foreach($menus as $menu)
        <div class="col-md-4 mb-4">
            <a href="{{ route('menus.show', $menu->id) }}" class="text-decoration-none text-dark">
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
                        <div class="mt-2">
                            <span class="badge bg-info text-dark">Defuzzifikasi: 
                                {{
                                    optional(\App\Models\RuleExecution::where('menu_id', $menu->id)->first())->z_admin !== null
                                        ? \App\Models\RuleExecution::where('menu_id', $menu->id)->first()->z_admin
                                        : '-' 
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
@endsection