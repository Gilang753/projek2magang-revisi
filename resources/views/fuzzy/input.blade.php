@extends('layouts.app')

@section('title', 'Setting Fuzzy')

@section('content')
<div class="container" style="max-width: 1200px; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy</h2>

    <form action="{{ route('fuzzy.boundaries.store') }}" method="POST" class="mb-5">
        <div style="width: 100%; max-width: 900px; margin: 0 auto 32px auto; background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.09); padding: 24px 16px 32px 16px;">
            <div class="mb-2" style="font-size:13px; color:#555; display:flex; flex-wrap:wrap; justify-content:center; gap:16px;">
                <span><span style="color:#dc3545;font-weight:600;">P1</span> = Inputan 1 (Murah Puncak)</span>
                <span><span style="color:#dc3545;font-weight:600;">P2</span> = Inputan 2 (Murah Akhir / Sedang Awal)</span>
                <span><span style="color:#dc3545;font-weight:600;">P3</span> = Inputan 3 (Sedang Puncak)</span>
                <span><span style="color:#dc3545;font-weight:600;">P4</span> = Inputan 4 (Sedang Akhir / Mahal Awal)</span>
                <span><span style="color:#dc3545;font-weight:600;">P5</span> = Inputan 5 (Mahal Puncak)</span>
                <span><span style="color:#dc3545;font-weight:600;">P6</span> = Inputan 6 (Mahal Akhir)</span>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="fuzzyChartGabungan"></canvas>
                <div class="x-labels" id="x-labels-gabungan" style="position: absolute; left: 0; right: 0; bottom: -32px; width: 100%; pointer-events: none; height: 22px;"></div>
            </div>
            <div class="text-center mt-4 fw-bold" style="color:#3b3b3b; letter-spacing:0.5px;">Gabungan</div>
        </div>
        @csrf
        <h5 class="mb-3">Tentukan Batas Fuzzy</h5>
        
        {{-- Kurva Murah (Bahu Kiri) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Murah (Bahu Kiri)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_murah_awal" class="form-label">Inputan 1 (Awal)</label>
                    <input type="number" class="form-control" id="batas_murah_awal" name="batas_murah_awal" value="{{ $boundaries->batas_murah_awal ?? 0 }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="batas_murah_puncak" class="form-label">Inputan 2 (Puncak)</label>
                    <input type="number" class="form-control" id="batas_murah_puncak" name="batas_murah_puncak" required value="{{ $boundaries->batas_murah_puncak ?? 8000 }}">
                </div>
                <div class="col-md-4">
                    <label for="batas_murah_akhir" class="form-label">Inputan 3 (Akhir)</label>
                    <input type="number" class="form-control" id="batas_murah_akhir" name="batas_murah_akhir" required value="{{ $boundaries->batas_murah_akhir ?? 12000 }}">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartMurah"></canvas>
            </div>
        </div>

        {{-- Kurva Sedang (Segitiga) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Sedang (Segitiga)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_sedang_awal" class="form-label">Inputan 1 (P2)</label>
                    <input type="number" class="form-control" id="batas_sedang_awal" name="batas_sedang_awal" required value="{{ $boundaries->batas_sedang_awal ?? 12000 }}">
                </div>
                <div class="col-md-4">
                    <label for="batas_sedang_puncak" class="form-label">Inputan 2 (P3)</label>
                    <input type="number" class="form-control" id="batas_sedang_puncak" name="batas_sedang_puncak" required value="{{ $boundaries->batas_sedang_puncak ?? 16000 }}">
                </div>
                <div class="col-md-4">
                    <label for="batas_sedang_akhir" class="form-label">Inputan 3 (P4)</label>
                    <input type="number" class="form-control" id="batas_sedang_akhir" name="batas_sedang_akhir" required value="{{ $boundaries->batas_sedang_akhir ?? 20000 }}">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartSedang"></canvas>
            </div>
        </div>

        {{-- Kurva Mahal (Bahu Kanan) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Mahal (Bahu Kanan)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_mahal_awal" class="form-label">Inputan 1 (P4)</label>
                    <input type="number" class="form-control" id="batas_mahal_awal" name="batas_mahal_awal" required value="{{ $boundaries->batas_mahal_awal ?? 20000 }}">
                </div>
                <div class="col-md-4">
                    <label for="batas_mahal_puncak" class="form-label">Inputan 2 (P5)</label>
                    <input type="number" class="form-control" id="batas_mahal_puncak" name="batas_mahal_puncak" required value="{{ $boundaries->batas_mahal_puncak ?? 24000 }}">
                </div>
                <div class="col-md-4">
                    <label for="batas_mahal_akhir" class="form-label">Inputan 3 (P6)</label>
                    <input type="number" class="form-control" id="batas_mahal_akhir" name="batas_mahal_akhir" required value="{{ $boundaries->batas_mahal_akhir ?? 24000 }}">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartMahal"></canvas>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success">Simpan Batas</button>
        </div>
    </form>
    
    <hr>
    
    <form action="{{ route('fuzzy.calculate') }}" method="POST">
        @csrf
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
    
    @if(isset($results) && !empty($results))
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                Hasil Perhitungan untuk Harga Rp. {{ number_format($results['harga'], 0, ',', '.') }}
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Derajat Keanggotaan Murah: <span class="fw-bold">{{ number_format($results['miu_murah'], 3) }}</span></li>
                <li class="list-group-item">Derajat Keanggotaan Sedang: <span class="fw-bold">{{ number_format($results['miu_sedang'], 3) }}</span></li>
                <li class="list-group-item">Derajat Keanggotaan Mahal: <span class="fw-bold">{{ number_format($results['miu_mahal'], 3) }}</span></li>
            </ul>
        </div>
    @endif
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
                        <th>Batasan (P1-P5)</th>
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
                        <td>{{ number_format($item->p1) }} - {{ number_format($item->p2) }} - {{ number_format($item->p3) }} - {{ number_format($item->p4) }} - {{ number_format($item->p5) }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const batasMurahAwal = document.getElementById('batas_murah_awal');
        const batasMurahPuncak = document.getElementById('batas_murah_puncak');
        const batasMurahAkhir = document.getElementById('batas_murah_akhir');
        
        const batasSedangAwal = document.getElementById('batas_sedang_awal');
        const batasSedangPuncak = document.getElementById('batas_sedang_puncak');
        const batasSedangAkhir = document.getElementById('batas_sedang_akhir');
        
        const batasMahalAwal = document.getElementById('batas_mahal_awal');
        const batasMahalPuncak = document.getElementById('batas_mahal_puncak');
        const batasMahalAkhir = document.getElementById('batas_mahal_akhir');

    const ctxGabungan = document.getElementById('fuzzyChartGabungan').getContext('2d');
    const ctxMurah = document.getElementById('fuzzyChartMurah').getContext('2d');
    const ctxSedang = document.getElementById('fuzzyChartSedang').getContext('2d');
    const ctxMahal = document.getElementById('fuzzyChartMahal').getContext('2d');
    let chartGabungan;
        const createCombinedChart = (p1, p2, p2_sedang, p3_sedang, p4_sedang, p4_mahal, p5, p6_mahal) => {
            // Gabungan semua dataset
            const datasets = [
                {
                    label: 'Murah',
                    data: [{ x: 0, y: 1 }, { x: p1, y: 1 }, { x: p2, y: 0 }],
                    borderColor: '#28a745',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                },
                {
                    label: 'Sedang',
                    data: [{ x: p2_sedang, y: 0 }, { x: p3_sedang, y: 1 }, { x: p4_sedang, y: 0 }],
                    borderColor: '#ffc107',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                },
                {
                    label: 'Mahal',
                    data: [{ x: p4_mahal, y: 0 }, { x: p5, y: 1 }, { x: p6_mahal, y: 1 }],
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                }
            ];
            // Ticks gabungan
            let tickValues = [0, p1, p2, p2_sedang, p3_sedang, p4_sedang, p4_mahal, p5, p6_mahal].filter(v => !isNaN(v));
            tickValues = [...new Set(tickValues)].sort((a, b) => a - b);
            if (chartGabungan) chartGabungan.destroy();
            chartGabungan = new Chart(ctxGabungan, {
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
                                callback: function(value) {
                                    if (value === 0) return 0;
                                    if (value === p1) return 'P1: ' + p1;
                                    if (value === p2) return 'P2: ' + p2;
                                    if (value === p2_sedang) return 'P2: ' + p2_sedang;
                                    if (value === p3_sedang) return 'P3: ' + p3_sedang;
                                    if (value === p4_sedang) return 'P4: ' + p4_sedang;
                                    if (value === p4_mahal) return 'P4: ' + p4_mahal;
                                    if (value === p5) return 'P5: ' + p5;
                                    if (value === p6_mahal) return 'P6: ' + p6_mahal;
                                    return null;
                                },
                                values: tickValues
                            }
                        },
                        y: {
                            min: 0,
                            max: 1.0,
                            title: { display: true, text: 'Derajat Keanggotaan' }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
            // Render label di bawah grafik gabungan, pixel-perfect di bawah x-axis, with P-labels
            const labelContainer = document.getElementById('x-labels-gabungan');
            if (labelContainer) {
                labelContainer.innerHTML = '';
                const chartArea = chartGabungan.chartArea;
                const xScale = chartGabungan.scales.x;
                if (chartArea && xScale) {
                    tickValues.forEach(function(val) {
                        let label = '';
                        let pLabel = '';
                        if (val === p1) { label = p1; pLabel = 'P1'; }
                        else if (val === p2) { label = p2; pLabel = 'P2'; }
                        else if (val === p2_sedang) { label = p2_sedang; pLabel = 'P2'; }
                        else if (val === p3_sedang) { label = p3_sedang; pLabel = 'P3'; }
                        else if (val === p4_sedang) { label = p4_sedang; pLabel = 'P4'; }
                        else if (val === p4_mahal) { label = p4_mahal; pLabel = 'P4'; }
                        else if (val === p5) { label = p5; pLabel = 'P5'; }
                        else if (val === p6_mahal) { label = p6_mahal; pLabel = 'P6'; }
                        else if (val === 0) { label = 0; pLabel = ''; }
                        else label = '';
                        if (label !== '') {
                            const div = document.createElement('div');
                            div.style.position = 'absolute';
                            div.style.left = (xScale.getPixelForValue(val) - chartArea.left) + 'px';
                            div.style.transform = 'translateX(-50%)';
                            div.style.bottom = '0';
                            div.style.fontSize = '13px';
                            div.style.fontWeight = '600';
                            div.style.color = '#dc3545';
                            div.style.background = 'rgba(255,255,255,0.85)';
                            div.style.padding = '0 4px';
                            div.style.borderRadius = '4px';
                            div.style.pointerEvents = 'none';
                            div.style.lineHeight = '1.2';
                            div.innerHTML = pLabel ? `<span style='font-size:11px;font-weight:700;'>${pLabel}</span><br>${label}` : label;
                            labelContainer.appendChild(div);
                        }
                    });
                }
            }
        };

        const createChart = (ctx, dataset, label, color, minX, p1, p2, p3, maxX) => {
            if (ctx.chart) {
                ctx.chart.destroy();
            }
            
            const tickValues = [p1, p2, p3].filter(v => !isNaN(v));

            ctx.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: label,
                        data: dataset,
                        borderColor: color,
                        borderWidth: 2,
                        fill: false,
                        tension: 0,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: { display: true, text: 'Harga' },
                            min: minX,
                            max: maxX,
                            ticks: {
                                autoSkip: false,
                                callback: function(value) {
                                    if (value === p1) return p1;
                                    if (value === p2) return p2;
                                    if (value === p3) return p3;
                                    return null;
                                }
                            }
                        },
                        y: {
                            min: 0,
                            max: 1.0,
                            title: { display: true, text: 'Derajat Keanggotaan' }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        };

        const updateCharts = () => {
            // Gabungan
            createCombinedChart(
                parseFloat(batasMurahPuncak.value),
                parseFloat(batasMurahAkhir.value),
                parseFloat(batasSedangAwal.value),
                parseFloat(batasSedangPuncak.value),
                parseFloat(batasSedangAkhir.value),
                parseFloat(batasMahalAwal.value),
                parseFloat(batasMahalPuncak.value),
                parseFloat(batasMahalAkhir.value)
            );
            const p1 = parseFloat(batasMurahPuncak.value);
            const p2 = parseFloat(batasMurahAkhir.value);
            
            const p2_sedang = parseFloat(batasSedangAwal.value);
            const p3_sedang = parseFloat(batasSedangPuncak.value);
            const p4_sedang = parseFloat(batasSedangAkhir.value);

            const p4_mahal = parseFloat(batasMahalAwal.value);
            const p5 = parseFloat(batasMahalPuncak.value);
            const p6_mahal = parseFloat(batasMahalAkhir.value);

            // Grafik Murah (Bahu Kiri)
            const datasetMurah = [{ x: 0, y: 1 }, { x: p1, y: 1 }, { x: p2, y: 0 }];
            createChart(ctxMurah, datasetMurah, 'Murah', '#28a745', 0, 0, p1, p2, p2 + 2000); // Batas maksimum 2000 lebih besar dari p2
            
            // Grafik Sedang (Segitiga)
            const datasetSedang = [{ x: p2_sedang, y: 0 }, { x: p3_sedang, y: 1 }, { x: p4_sedang, y: 0 }];
            createChart(ctxSedang, datasetSedang, 'Sedang', '#ffc107', p2_sedang - 2000, p2_sedang, p3_sedang, p4_sedang, p4_sedang + 2000);

            // Grafik Mahal (Bahu Kanan)
            const datasetMahal = [{ x: p4_mahal, y: 0 }, { x: p5, y: 1 }, { x: p6_mahal, y: 1 }];
            createChart(ctxMahal, datasetMahal, 'Mahal', '#dc3545', p4_mahal - 2000, p4_mahal, p5, p6_mahal, p6_mahal + 2000);
        };

        [batasMurahPuncak, batasMurahAkhir,
        batasSedangAwal, batasSedangPuncak, batasSedangAkhir,
        batasMahalAwal, batasMahalPuncak, batasMahalAkhir].forEach(input => {
            input.addEventListener('input', updateCharts);
        });
        
        updateCharts();
    });
</script>
@endpush