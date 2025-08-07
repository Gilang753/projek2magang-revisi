@extends('layouts.app')

@section('title', 'Hasil Fuzzy Rating')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-star"></i> Hasil Perhitungan Fuzzy Rating</h5>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="star-display mb-3" style="font-size: 3rem;">
                            @for($i = 1; $i <= $rating_bintang; $i++)
                                <i class="fas fa-star text-warning"></i>
                            @endfor
                        </div>
                        <h4>{{ $rating_bintang }} Bintang</h4>
                        <h5 class="text-muted">Nilai: {{ $nilai_rating }}</h5>
                    </div>

                    <hr>

                    <h5 class="mb-3">Derajat Keanggotaan:</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-3 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Rendah</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h3>{{ number_format($keanggotaan_rendah, 3) }}</h3>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-danger" 
                                             style="width: {{ $keanggotaan_rendah * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3 border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">Sedang</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h3>{{ number_format($keanggotaan_sedang, 3) }}</h3>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" 
                                             style="width: {{ $keanggotaan_sedang * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Tinggi</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h3>{{ number_format($keanggotaan_tinggi, 3) }}</h3>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ $keanggotaan_tinggi * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-{{ $kategori_dominan == 'Tinggi' ? 'success' : 
                                              ($kategori_dominan == 'Sedang' ? 'warning' : 'danger') }} mt-4">
                        <h5 class="alert-heading">Kategori Dominan: {{ $kategori_dominan }}</h5>
                        <p class="mb-0">
                            Rating termasuk dalam kategori <strong>{{ $kategori_dominan }}</strong> 
                            dengan derajat keanggotaan tertinggi.
                        </p>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <a href="{{ route('fuzzy.inputRating') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('fuzzy.inputRating') }}" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Hitung Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection