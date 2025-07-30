@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Tambah Menu Baru</h1>
    
    <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label for="nama_menu" class="form-label">Nama Menu</label>
            <input type="text" class="form-control" id="nama_menu" name="nama_menu" required>
        </div>
        
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
        </div>
        
        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="number" class="form-control" id="harga" name="harga" required>
        </div>
        
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select class="form-select" id="kategori" name="kategori" required>
                <option value="">Pilih Kategori</option>
                <option value="Makanan">Makanan</option>
                <option value="Minuman">Minuman</option>
                <option value="Snack">Snack</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="gambar" class="form-label">Gambar Menu</label>
            <input type="file" class="form-control" id="gambar" name="gambar">
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection