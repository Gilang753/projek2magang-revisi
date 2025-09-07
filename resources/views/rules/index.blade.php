@extends('layouts.app')

@section('title', 'Daftar Aturan Fuzzy')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <x-alert/>

            {{-- Card untuk Aturan Baru --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Aturan Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rules.store') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label class="form-label">IF Harga</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="harga_fuzzy">
                                    <option value="Murah">Murah</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Mahal">Mahal</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">And Rating</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="rating_fuzzy">
                                    <option value="Rendah">Rendah</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Tinggi">Tinggi</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">And Rasa</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="rasa_fuzzy">
                                    <option value="Asam">Asam</option>
                                    <option value="Manis">Manis</option>
                                    <option value="Pedas">Pedas</option>
                                    <option value="Asin">Asin</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">Then</label>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="rekomendasi">
                                    <option value="Rekomendasi">Rekomendasi</option>
                                    <option value="Tidak Rekomendasi">Tidak Rekomendasi</option>
                                </select>
                            </div>
                            <div class="col-md-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah Aturan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card untuk List Rule --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">List Rule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">No</th>
                                    <th scope="col">Rule</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rules as $rule)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            IF Harga {{ $rule->harga_fuzzy }} And Rating {{ $rule->rating_fuzzy }} And Rasa {{ $rule->rasa_fuzzy }}, Then {{ $rule->rekomendasi }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('rules.edit', $rule->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('rules.destroy', $rule->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus aturan ini?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada aturan yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-end">
                        <form action="{{ route('rules.execute') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-play-circle me-1"></i> Eksekusi Rule
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Hasil Eksekusi --}}
            @if(isset($inferenceResults) && count($inferenceResults) > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Hasil Eksekusi Rule</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">
                            Input: Harga Rp. {{ number_format($lastHarga->harga, 0, ',', '.') }}, 
                            Rating {{ $lastRating->rating }},
                            Rasa {{ $lastRasa->rasa }}
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
        </div>
    </div>
</div>
@endsection