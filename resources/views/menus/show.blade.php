@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if($menu->gambar)
                    <img src="{{ asset('images/menus/'.$menu->gambar) }}" class="card-img-top" alt="{{ $menu->nama_menu }}">
                @else
                    <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="No Image">
                @endif
                <div class="card-body">
                    <h1 class="card-title">{{ $menu->nama_menu }}</h1>
                    <p class="card-text">{{ $menu->deskripsi }}</p>
                    <p class="h4">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    <span class="badge bg-primary">{{ $menu->kategori }}</span>
                </div>
                <div class="card-footer">
                    <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">Hapus</button>
                    </form>
                    <a href="{{ route('menus.index') }}" class="btn btn-secondary float-end">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection