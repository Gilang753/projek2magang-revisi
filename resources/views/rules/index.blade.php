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
            <div class="mt-4">
                <h5 class="mb-3">Hasil Eksekusi Rule</h5>
                @php
                    // Kelompokkan hasil per rule
                    $groupedResults = [];
                    foreach ($inferenceResults as $result) {
                        $ruleId = $result['rule']->id;
                        if (!isset($groupedResults[$ruleId])) {
                            $groupedResults[$ruleId] = [
                                'rule' => $result['rule'],
                                'rekomendasi' => $result['rekomendasi'],
                                'items' => []
                            ];
                        }
                        $groupedResults[$ruleId]['items'][] = $result;
                    }
                @endphp
                @foreach ($groupedResults as $ruleGroup)
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <strong>Rule:</strong> IF Harga <strong>{{ $ruleGroup['rule']->harga_fuzzy }}</strong>, Rating <strong>{{ $ruleGroup['rule']->rating_fuzzy }}</strong>, Rasa <strong>{{ $ruleGroup['rule']->rasa_fuzzy }}</strong> THEN <strong>{{ $ruleGroup['rekomendasi'] }}</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>Menu</th>
                                        <th>Miu Harga</th>
                                        <th>Miu Rating</th>
                                        <th>Miu Rasa</th>
                                        <th>Alpha</th>
                                        <th>Z_Crisp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ruleGroup['items'] as $item)
                                        <tr class="text-center">
                                            <td>{{ $item['menu']->nama ?? '-' }}</td>
                                            <td>{{ number_format($item['miu_harga'], 3) }}</td>
                                            <td>{{ number_format($item['miu_rating'], 3) }}</td>
                                            <td>{{ number_format($item['miu_rasa'], 3) }}</td>
                                            <td>{{ number_format($item['alpha'], 3) }}</td>
                                            <td>{{ number_format($item['z_crisp'], 3) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection