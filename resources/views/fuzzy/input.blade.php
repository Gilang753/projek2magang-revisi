@extends('layouts.app')

@section('title', 'Setting Fuzzy')

@section('content')
<div class="container" style="max-width: 500px; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy</h2>
    <form action="{{ route('fuzzy.calculate') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="harga" class="form-label" style="font-weight: bold;">Masukkan Harga (Rp. 5.000 - Rp. 25.000):</label>
            <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                   id="harga" name="harga" min="5000" max="25000" 
                   value="{{ old('harga') }}" required>
            @error('harga')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <div class="form-text">
                Kategori: Murah (≤12rb), Sedang (10rb-18rb), Mahal (≥16rb)
            </div>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Hitung Derajat Keanggotaan</button>
        </div>
    </form>
</div>
@endsection