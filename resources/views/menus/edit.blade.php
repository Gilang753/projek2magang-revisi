@extends('layouts.app')

@section('title', 'Edit Menu')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Menu</h1>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $menu->nama }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Menu</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ $menu->deskripsi }}</textarea>
                    <div class="form-text">Maksimal 500 karakter</div>
                </div>
                
                <div class="mb-3">
                    <label for="harga_seporsi" class="form-label">Harga per Porsi</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga_seporsi" name="harga_seporsi" 
                               value="{{ $menu->harga_seporsi }}" min="0" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="cita_rasa" class="form-label">Cita Rasa</label>
                    <select class="form-select" id="cita_rasa" name="cita_rasa" required>
                        @foreach($citaRasaOptions as $value => $label)
                            <option value="{{ $value }}" {{ $menu->cita_rasa == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-select" id="rating" name="rating" required>
                        @foreach($ratingOptions as $value => $label)
                            <option value="{{ $value }}" {{ $menu->rating == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar Menu</label>
                    @if($menu->gambar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$menu->gambar) }}" alt="Gambar Saat Ini" style="max-height: 150px;" class="img-thumbnail">
                        </div>
                    @endif
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                    <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar</div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection