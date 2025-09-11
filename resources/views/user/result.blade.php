@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Makanan')

@section('content')
<div class="container">
    <h2 class="mb-3">Hasil Rekomendasi Menu Makanan</h2>
    @if(isset($recommendedMenus) && count($recommendedMenus) > 0)
    <div class="alert alert-success mt-3">
        <h4 class="alert-heading">Rekomendasi Menu Terdekat (Max 10)</h4>
        <div class="row">
            @foreach($recommendedMenus as $i => $rec)
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2" style="font-size:1.1em; min-width:32px;">{{ $i+1 }}</span>
                    @if($rec['menu']->gambar)
                        <img src="{{ asset('storage/'.$rec['menu']->gambar) }}" style="height: 60px; width: 60px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                    @endif
                    <div>
                        <strong>{{ $rec['menu']->nama }}</strong><br>
                        <span class="text-muted">Rp {{ number_format($rec['menu']->harga_seporsi, 0, ',', '.') }}</span><br>
                        <span class="badge bg-info text-dark">z_user: {{ $z_user }}, z_admin: {{ $rec['z_admin'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3">Detail Eksekusi Rule</h5>
            @if(isset($inferenceResults) && count($inferenceResults) > 0)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Hasil Eksekusi Rule</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">
                                Input: Harga Rp. {{ number_format($hargaInput, 0, ',', '.') }}, 
                                Rating {{ $ratingInput }},
                                Rasa {{ $rasaInput }}
                            </small>
                        </div>
                        @foreach ($inferenceResults as $index => $result)
                            <div class="mb-2 p-2 border-bottom">
                                IF Harga <strong>{{ $result['rule']->harga_fuzzy }}</strong> 
                                ({{ number_format($result['miu_harga'], 3) }}) 
                                And Rating <strong>{{ $result['rule']->rating_fuzzy }}</strong> 
                                ({{ number_format($result['miu_rating'], 3) }}) 
                                And Rasa <strong>{{ $result['rule']->rasa_fuzzy }}</strong>
                                ({{ number_format($result['miu_rasa'], 3) }})
                                Then <strong>{{ $result['rekomendasi'] }}</strong> 
                                ({{ number_format($result['alpha'], 3) }})
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <a href="{{ route('user.index') }}" class="btn btn-secondary mt-2">Kembali ke Input User</a>
        </div>
    </div>
</div>
@endsection
