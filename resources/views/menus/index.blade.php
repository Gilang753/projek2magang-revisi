@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Daftar Menu Warung Makan</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('menus.create') }}" class="btn btn-primary mb-3">Tambah Menu Baru</a>
    
    <div class="row">
        @foreach($menus as $menu)
        <div class="col-md-4 mb-4">
            <div class="card">
                @if($menu->gambar)
                    <img src="{{ asset('images/menus/'.$menu->gambar) }}" class="card-img-top" alt="{{ $menu->nama_menu }}" style="height: 200px; object-fit: cover;">
                @else
                    <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $menu->nama_menu }}</h5>
                    <p class="card-text">{{ $menu->deskripsi }}</p>
                    <p class="text-muted">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    <span class="badge bg-secondary">{{ $menu->kategori }}</span>
                </div>
                <div class="card-footer">
                    <a href="{{ route('menus.show', $menu->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection