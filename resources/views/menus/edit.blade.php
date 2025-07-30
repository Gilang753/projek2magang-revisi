@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Menu</h1>
    
    <form action="{{ route('menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nama_menu" class="form-label">Nama Menu</label>
            <input type="text" class="form-control" id="nama_menu" name="nama_menu" value="{{ $menu->nama_menu }}" required>
        </div>
        
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ $menu->deskripsi }}</textarea>
        </div>
        
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" class="form-control" id="harga" name="harga" value="{{ $menu->harga }}" required>
        </div>
        
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select class="form-select" id="kategori" name="kategori" required>
                <option value="Makanan" {{ $menu->kategori == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                <option value="Minuman" {{ $menu->kategori == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                <option value="Snack" {{ $menu->kategori == 'Snack' ? 'selected' : '' }}>Snack</option>
                <option value="Lainnya" {{ $menu->kategori == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Menu</label>
            @if($menu->gambar)
                <div class="mb-2">
                    <img src="{{ asset('images/menus/'.$menu->gambar) }}" alt="{{ $menu->nama_menu }}" style="max-width: 200px;">
                </div>
            @endif
            <input type="file" class="form-control" id="gambar" name="gambar">
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection