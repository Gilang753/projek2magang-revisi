@extends('layouts.app')

@section('title', 'Detail Menu')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detail Menu</h1>
        <div>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    @if($menu->gambar)
                        <img src="{{ asset('storage/'.$menu->gambar) }}" class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: cover;" alt="{{ $menu->nama }}">
                    @else
                        <img src="{{ asset('images/tidak-ada-gambar.jpg') }}" class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: cover;" alt="Tidak ada gambar">
                    @endif
                </div>
                <div class="col-md-7">
                    <h3>{{ $menu->nama }}</h3>
                    <p class="text-muted">{{ $menu->deskripsi }}</p>
                    <table class="table table-bordered mt-3">
                        <tr>
                            <th width="30%">Harga per Porsi</th>
                            <td>Rp {{ number_format($menu->harga_seporsi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Cita Rasa</th>
                            <td>{{ ucfirst($menu->cita_rasa) }}</td>
                        </tr>
                        <tr>
                            <th>Rating</th>
                            <td class="text-warning">
                                {{ str_repeat('★', $menu->rating) }}{{ str_repeat('☆', 5 - $menu->rating) }}
                                ({{ $menu->rating }} bintang)
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection