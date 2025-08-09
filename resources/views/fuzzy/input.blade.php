@extends('layouts.app')

@section('title', 'Setting Fuzzy')

@section('content')
<div class="container" style="max-width: 1200px; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy</h2>

    <form action="{{ route('fuzzy.boundaries.store') }}" method="POST" class="mb-4">
        @csrf
        <h5 class="mb-3">Tentukan Batas Harga Fuzzy</h5>
        <div class="row g-2">
            <div class="col-6">
                <label for="batas1" class="form-label">Batas 1</label>
                <input type="number" class="form-control" id="batas1" name="batas1" required value="{{ $boundaries->batas1 ?? old('batas1', 8000) }}">
            </div>
            <div class="col-6">
                <label for="batas2" class="form-label">Batas 2</label>
                <input type="number" class="form-control" id="batas2" name="batas2" required value="{{ $boundaries->batas2 ?? old('batas2', 12000) }}">
            </div>
            <div class="col-6">
                <label for="batas3" class="form-label">Batas 3</label>
                <input type="number" class="form-control" id="batas3" name="batas3" required value="{{ $boundaries->batas3 ?? old('batas3', 16000) }}">
            </div>
            <div class="col-6">
                <label for="batas4" class="form-label">Batas 4</label>
                <input type="number" class="form-control" id="batas4" name="batas4" required value="{{ $boundaries->batas4 ?? old('batas4', 20000) }}">
            </div>
        </div>
        <div class="form-text mt-3">
            Batas-batas ini akan digunakan untuk menentukan rentang Murah, Sedang, dan Mahal.
        </div>
        <div class="d-grid mt-3">
            <button type="submit" class="btn btn-success">Simpan Batas</button>
        </div>
    </form>

    <form action="{{ route('fuzzy.calculate') }}" method="POST">
        @csrf

        <hr>

        <h5 class="text-center mb-3">Grafik Fungsi Keanggotaan Fuzzy</h5>

        <div class="mb-4 d-flex flex-column align-items-center gap-4">
            <div style="width: 100%; max-width: 900px; margin-bottom: 32px;">
                <div style="height: 350px; position: relative;">
                    <canvas id="fuzzyChartMurah"></canvas>
                    <div class="x-labels" id="x-labels-murah"></div>
                </div>
                <div class="text-center mt-2">Murah</div>
            </div>
            <div style="width: 100%; max-width: 900px; margin-bottom: 32px;">
                <div style="height: 350px; position: relative;">
                    <canvas id="fuzzyChartSedang"></canvas>
                    <div class="x-labels" id="x-labels-sedang"></div>
                </div>
                <div class="text-center mt-2">Sedang</div>
            </div>
            <div style="width: 100%; max-width: 900px;">
                <div style="height: 350px; position: relative;">
                    <canvas id="fuzzyChartMahal"></canvas>
                    <div class="x-labels" id="x-labels-mahal"></div>
                </div>
                <div class="text-center mt-2">Mahal</div>
            </div>
        </div>

        <hr>



        <div class="mb-4">
            <label for="harga" class="form-label" style="font-weight: bold;">Masukkan Harga yang Dihitung:</label>
        <input type="number" class="form-control @error('harga') is-invalid @enderror" 
            id="harga" name="harga" value="{{ old('harga', isset($data[0]) ? $data[0]->harga : '') }}" required>
            @error('harga')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <div id="harga-terpilih" class="text-center mt-2"></div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Hitung Derajat Keanggotaan</button>
        </div>
    </form>
</div>

<div class="container mt-5" style="max-width: 1200px;">
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
        // Ambil boundaries dari PHP
        const boundaries = {
            batas1: {{ $boundaries->batas1 ?? 8000 }},
            batas2: {{ $boundaries->batas2 ?? 12000 }},
            batas3: {{ $boundaries->batas3 ?? 16000 }},
            batas4: {{ $boundaries->batas4 ?? 20000 }}
        };
        const hargaInput = document.getElementById('harga');
        const ctxMurah = document.getElementById('fuzzyChartMurah').getContext('2d');
        const ctxSedang = document.getElementById('fuzzyChartSedang').getContext('2d');
        const ctxMahal = document.getElementById('fuzzyChartMahal').getContext('2d');
        let chartMurah, chartSedang, chartMahal;

    const createChart = (ctx, dataset, hargaValue, minX, maxX, warnaHarga, a, b, c, d, labelContainerId) => {
            // Hapus chart lama jika ada
            if (ctx.chart) {
                ctx.chart.destroy();
            }
            const datasets = [dataset];
            // Tambahkan garis harga jika valid
            if (!isNaN(hargaValue)) {
                datasets.push({
                    label: 'Harga Input',
                    data: [{ x: hargaValue, y: 0 }, { x: hargaValue, y: 1.0 }],
                    borderColor: warnaHarga || '#0d6efd',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                });
            }

            // Buat array ticks custom: batas dan harga input
            let tickValues = [a, b, c, d];
            if (!isNaN(hargaValue) && !tickValues.includes(hargaValue)) {
                tickValues.push(hargaValue);
            }
            tickValues = tickValues.filter(v => !isNaN(v)).sort((x, y) => x - y);

            ctx.chart = new Chart(ctx, {
                type: 'line',
                data: { datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: { display: true, text: 'Harga' },
                            min: Math.min(...tickValues),
                            max: Math.max(...tickValues),
                            ticks: {
                                autoSkip: false,
                                minRotation: 0,
                                maxRotation: 0,
                                stepSize: null,
                                callback: function(value) {
                                    if (value === a) return 'B1: ' + a;
                                    if (value === b) return 'B2: ' + b;
                                    if (value === c) return 'B3: ' + c;
                                    if (value === d) return 'B4: ' + d;
                                    if (!isNaN(hargaValue) && value === hargaValue) return 'Harga: ' + hargaValue;
                                    return '';
                                },
                                // Hanya tampilkan ticks pada batas dan harga input
                                values: tickValues
                            }
                        },
                        y: {
                            min: 0,
                            max: 1.0,
                            title: { display: true, text: 'Miu' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });

            // Render label di bawah grafik
            if (labelContainerId) {
                const labelContainer = document.getElementById(labelContainerId);
                if (labelContainer) {
                    labelContainer.innerHTML = '';
                    labelContainer.style.display = 'block';
                    labelContainer.style.position = 'absolute';
                    labelContainer.style.left = 0;
                    labelContainer.style.right = 0;
                    labelContainer.style.bottom = '-28px';
                    labelContainer.style.width = '100%';
                    labelContainer.style.pointerEvents = 'none';
                    tickValues.forEach(function(val) {
                        let label = '';
                        if (!isNaN(hargaValue) && val === hargaValue) label = hargaValue;
                        else label = '';
                        const div = document.createElement('div');
                        div.style.position = 'absolute';
                        div.style.transform = 'translateX(-50%)';
                        div.style.left = ((val - Math.min(a, b, c, d, hargaValue)) / (Math.max(a, b, c, d, hargaValue) - Math.min(a, b, c, d, hargaValue)) * 100) + '%';
                        div.style.textAlign = 'center';
                        div.style.minWidth = '60px';
                        div.style.fontWeight = 'bold';
                        div.style.color = '#dc3545';
                        div.innerText = label;
                        labelContainer.appendChild(div);
                    });
                }
            }
        };

        const updateCharts = () => {
            const a = boundaries.batas1;
            const b = boundaries.batas2;
            const c = boundaries.batas3;
            const d = boundaries.batas4;
            const hargaInput = document.getElementById('harga');
            const hargaValue = parseFloat(hargaInput.value);

            // Tampilkan harga input di bawah input
            const hargaTerpilih = document.getElementById('harga-terpilih');
            if (!isNaN(hargaValue)) {
                hargaTerpilih.innerText = 'Harga input: ' + hargaValue.toLocaleString();
            } else {
                hargaTerpilih.innerText = '';
            }

            // Dataset untuk masing-masing grafik
            const datasetMurah = {
                label: 'Murah',
                data: [{ x: 0, y: 1 }, { x: a, y: 1 }, { x: b, y: 0 }],
                borderColor: '#28a745',
                borderWidth: 2,
                fill: false,
                tension: 0,
                pointRadius: 5,
            };
            const datasetSedang = {
                label: 'Sedang',
                data: [{ x: a, y: 0 }, { x: b, y: 1 }, { x: c, y: 1 }, { x: d, y: 0 }],
                borderColor: '#ffc107',
                borderWidth: 2,
                fill: false,
                tension: 0,
                pointRadius: 5,
            };
            const datasetMahal = {
                label: 'Mahal',
                data: [{ x: c, y: 0 }, { x: d, y: 1 }, { x: d + 5000, y: 1 }],
                borderColor: '#dc3545',
                borderWidth: 2,
                fill: false,
                tension: 0,
                pointRadius: 5,
            };

            // Render masing-masing grafik
            createChart(ctxMurah, datasetMurah, hargaValue, 0, d + 5000, '#28a745', a, b, c, d, 'x-labels-murah');
            createChart(ctxSedang, datasetSedang, hargaValue, 0, d + 5000, '#ffc107', a, b, c, d, 'x-labels-sedang');
            createChart(ctxMahal, datasetMahal, hargaValue, 0, d + 5000, '#dc3545', a, b, c, d, 'x-labels-mahal');
        };

        // Inisialisasi grafik saat halaman dimuat
        updateCharts();

        // Perbarui grafik setiap kali input berubah
        [p1Input, p2Input, p3Input, p4Input, hargaInput].forEach(input => {
            input.addEventListener('input', updateCharts);
        });
    });
</script>
@endpush