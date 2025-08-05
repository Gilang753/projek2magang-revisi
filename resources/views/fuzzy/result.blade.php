@extends('layouts.app')

@section('title', 'Hasil Fuzzy')

@section('content')
<div class="container" style="max-width: 600px; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <h2 class="text-center mb-4">Hasil Perhitungan Derajat Keanggotaan</h2>
    <div class="mb-3">
        <p class="result-item" style="font-size: 1.1rem; margin-bottom: 10px;">
            <strong style="display: inline-block; min-width: 100px;">Harga Input:</strong> 
            Rp. {{ number_format($harga, 0, ',', '.') }}
        </p>
        <hr>
        <p class="result-item" style="font-size: 1.1rem; margin-bottom: 10px;">
            <strong style="display: inline-block; min-width: 100px;">Derajat Keanggotaan:</strong>
        </p>
        <ul class="list-unstyled">
            <li class="result-item mb-3" style="font-size: 1.1rem; margin-bottom: 10px;">
                <strong style="display: inline-block; min-width: 100px;">Murah:</strong> 
                <span>{{ number_format($miu['murah'], 3) }}</span>
                <div class="membership-bar" style="height: 20px; background-color: #e9ecef; border-radius: 4px; margin-top: 5px; overflow: hidden;">
                    <div class="membership-fill-murah" style="height: 100%; background-color: #28a745; width: {{ $miu['murah'] * 100 }}%"></div>
                </div>
            </li>
            <li class="result-item mb-3" style="font-size: 1.1rem; margin-bottom: 10px;">
                <strong style="display: inline-block; min-width: 100px;">Sedang:</strong> 
                <span>{{ number_format($miu['sedang'], 3) }}</span>
                <div class="membership-bar" style="height: 20px; background-color: #e9ecef; border-radius: 4px; margin-top: 5px; overflow: hidden;">
                    <div class="membership-fill-sedang" style="height: 100%; background-color: #ffc107; width: {{ $miu['sedang'] * 100 }}%"></div>
                </div>
            </li>
            <li class="result-item mb-3" style="font-size: 1.1rem; margin-bottom: 10px;">
                <strong style="display: inline-block; min-width: 100px;">Mahal:</strong> 
                <span>{{ number_format($miu['mahal'], 3) }}</span>
                <div class="membership-bar" style="height: 20px; background-color: #e9ecef; border-radius: 4px; margin-top: 5px; overflow: hidden;">
                    <div class="membership-fill-mahal" style="height: 100%; background-color: #dc3545; width: {{ $miu['mahal'] * 100 }}%"></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="d-grid">
        <a href="{{ route('fuzzy.input') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection