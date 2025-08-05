@extends('layouts.app')

@section('title', 'Setting Fuzzy')

@section('content')
<div class="container" style="max-width: 800px; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy</h2>
    <form action="{{ route('fuzzy.calculate') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <h5 class="mb-3">Tentukan Batas Harga Fuzzy</h5>
            <div class="row g-2">
                <div class="col-6">
                    <label for="p1" class="form-label">Batas 1</label>
                    <input type="number" class="form-control" id="p1" name="p1" required value="{{ old('p1', 8000) }}">
                </div>
                <div class="col-6">
                    <label for="p2" class="form-label">Batas 2</label>
                    <input type="number" class="form-control" id="p2" name="p2" required value="{{ old('p2', 12000) }}">
                </div>
                <div class="col-6">
                    <label for="p3" class="form-label">Batas 3</label>
                    <input type="number" class="form-control" id="p3" name="p3" required value="{{ old('p3', 16000) }}">
                </div>
                <div class="col-6">
                    <label for="p4" class="form-label">Batas 4</label>
                    <input type="number" class="form-control" id="p4" name="p4" required value="{{ old('p4', 20000) }}">
                </div>
            </div>
            <div class="form-text mt-3">
                Batas-batas ini akan diurutkan secara otomatis dan digunakan untuk menentukan rentang Murah, Sedang, dan Mahal.
            </div>
        </div>

        <hr>

        <div class="mb-4">
            <label for="harga" class="form-label" style="font-weight: bold;">Masukkan Harga yang Dihitung:</label>
            <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                   id="harga" name="harga" value="{{ old('harga') }}" required>
            @error('harga')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Hitung Derajat Keanggotaan</button>
        </div>
    </form>
</div>

<div class="container mt-5" style="max-width: 800px;">
    <h3 class="text-center mb-4">Histori Pencarian</h3>
    @if ($data->isEmpty())
        <div class="alert alert-info text-center">Belum ada data pencarian.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Waktu</th>
                        <th>Harga Input</th>
                        <th>Batasan (P1-P4)</th>
                        <th>Miu Murah</th>
                        <th>Miu Sedang</th>
                        <th>Miu Mahal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                        <td>Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->p1) }} - {{ number_format($item->p2) }} - {{ number_format($item->p3) }} - {{ number_format($item->p4) }}</td>
                        <td>{{ number_format($item->miu_murah, 3) }}</td>
                        <td>{{ number_format($item->miu_sedang, 3) }}</td>
                        <td>{{ number_format($item->miu_mahal, 3) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection