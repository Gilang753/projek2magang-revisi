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

        <h5 class="text-center mb-3">Grafik Fungsi Keanggotaan Fuzzy</h5>
        <div style="height: 300px;">
            <canvas id="fuzzyChart"></canvas>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const p1Input = document.getElementById('p1');
        const p2Input = document.getElementById('p2');
        const p3Input = document.getElementById('p3');
        const p4Input = document.getElementById('p4');
        const hargaInput = document.getElementById('harga');
        const ctx = document.getElementById('fuzzyChart').getContext('2d');
        let fuzzyChart;

        const createFuzzyChart = (p1, p2, p3, p4, harga) => {
            const sortedPoints = [parseFloat(p1), parseFloat(p2), parseFloat(p3), parseFloat(p4)].sort((a, b) => a - b);
            const a = sortedPoints[0];
            const b = sortedPoints[1];
            const c = sortedPoints[2];
            const d = sortedPoints[3];
            const hargaValue = parseFloat(harga);

            if (fuzzyChart) {
                fuzzyChart.destroy();
            }

            const datasets = [
                {
                    label: 'Murah',
                    // Menambahkan titik data agar grafik dimulai dari y=1 di x=0
                    data: [{ x: 0, y: 1 }, { x: a, y: 1 }, { x: b, y: 0 }],
                    borderColor: '#28a745',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5,
                },
                {
                    label: 'Sedang',
                    data: [{ x: a, y: 0 }, { x: b, y: 1 }, { x: c, y: 1 }, { x: d, y: 0 }],
                    borderColor: '#ffc107',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5,
                },
                {
                    label: 'Mahal',
                    // Menambahkan titik data agar grafik berakhir di y=1 setelah x=d
                    data: [{ x: c, y: 0 }, { x: d, y: 1 }, { x: d + 5000, y: 1 }],
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5,
                },
            ];

            // Tambahkan garis input harga jika nilai harga valid
            if (!isNaN(hargaValue)) {
                datasets.push({
                    label: 'Harga Input',
                    data: [{ x: hargaValue, y: 0 }, { x: hargaValue, y: 1.0 }],
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                });
            }
            
            fuzzyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: { display: true, text: 'Harga' },
                            min: 0,
                            // Menyesuaikan nilai maksimum agar garis lurus akhir terlihat
                            max: d + 5000,
                        },
                        y: {
                            min: 0,
                            max: 1.0,
                            title: { display: true, text: 'Miu' }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        };

        const updateChart = () => {
            createFuzzyChart(
                p1Input.value,
                p2Input.value,
                p3Input.value,
                p4Input.value,
                hargaInput.value
            );
        };

        // Inisialisasi grafik saat halaman dimuat
        updateChart();

        // Perbarui grafik setiap kali input berubah
        [p1Input, p2Input, p3Input, p4Input, hargaInput].forEach(input => {
            input.addEventListener('input', updateChart);
        });
    });
</script>
@endpush