@extends('layouts.app')

@section('title', 'Pengaturan Fuzzy Rasa')

@section('content')
<div class="container" style="max-width: 1200px; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy Rasa</h2>

    <form action="{{ route('fuzzy.rasa.boundaries.store') }}" method="POST" class="mb-5">
        <div style="width: 100%; max-width: 900px; margin: 0 auto 32px auto; background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.09); padding: 24px 16px 32px 16px;">
            <div class="mb-2" style="font-size:13px; color:#555; display:flex; flex-wrap:wrap; justify-content:center; gap:16px;">
                <span><span style="color:#28a745;font-weight:600;">A1</span> = Asam Awal</span>
                <span><span style="color:#28a745;font-weight:600;">A2</span> = Asam Akhir / Manis Awal</span>
                <span><span style="color:#ffc107;font-weight:600;">M1</span> = Manis Awal</span>
                <span><span style="color:#ffc107;font-weight:600;">M2</span> = Manis Puncak</span>
                <span><span style="color:#ffc107;font-weight:600;">M3</span> = Manis Akhir / Pedas Awal</span>
                <span><span style="color:#dc3545;font-weight:600;">P1</span> = Pedas Awal</span>
                <span><span style="color:#dc3545;font-weight:600;">P2</span> = Pedas Puncak</span>
                <span><span style="color:#dc3545;font-weight:600;">P3</span> = Pedas Akhir / Asin Awal</span>
                <span><span style="color:#007bff;font-weight:600;">S1</span> = Asin Awal</span>
                <span><span style="color:#007bff;font-weight:600;">S2</span> = Asin Puncak</span>
                <span><span style="color:#007bff;font-weight:600;">S3</span> = Asin Akhir</span>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="fuzzyChartGabungan"></canvas>
                <div class="x-labels" id="x-labels-gabungan" style="position: absolute; left: 0; right: 0; bottom: -32px; width: 100%; pointer-events: none; height: 22px;"></div>
            </div>
            <div class="text-center mt-4 fw-bold" style="color:#3b3b3b; letter-spacing:0.5px;">Gabungan</div>
        </div>
        @csrf
        <h5 class="mb-3">Tentukan Batas Fuzzy Rasa (0-100)</h5>
        
        {{-- Kurva Asam (Bahu Kiri) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Asam (Bahu Kiri)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="batas_asam_puncak" class="form-label">Batas Puncak (A1)</label>
                    <input type="number" class="form-control" id="batas_asam_puncak" name="batas_asam_puncak" required value="{{ $boundaries->batas_asam_puncak ?? 10 }}" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label for="batas_asam_akhir" class="form-label">Batas Akhir (A2)</label>
                    <input type="number" class="form-control" id="batas_asam_akhir" name="batas_asam_akhir" required value="{{ $boundaries->batas_asam_akhir ?? 30 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartAsam"></canvas>
            </div>
        </div>

        {{-- Kurva Manis (Segitiga) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Manis (Segitiga)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_manis_awal" class="form-label">Batas Awal (M1)</label>
                    <input type="number" class="form-control" id="batas_manis_awal" name="batas_manis_awal" required value="{{ $boundaries->batas_manis_awal ?? 20 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_manis_puncak" class="form-label">Batas Puncak (M2)</label>
                    <input type="number" class="form-control" id="batas_manis_puncak" name="batas_manis_puncak" required value="{{ $boundaries->batas_manis_puncak ?? 40 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_manis_akhir" class="form-label">Batas Akhir (M3)</label>
                    <input type="number" class="form-control" id="batas_manis_akhir" name="batas_manis_akhir" required value="{{ $boundaries->batas_manis_akhir ?? 60 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartManis"></canvas>
            </div>
        </div>

        {{-- Kurva Pedas (Segitiga) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Pedas (Segitiga)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_pedas_awal" class="form-label">Batas Awal (P1)</label>
                    <input type="number" class="form-control" id="batas_pedas_awal" name="batas_pedas_awal" required value="{{ $boundaries->batas_pedas_awal ?? 50 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_pedas_puncak" class="form-label">Batas Puncak (P2)</label>
                    <input type="number" class="form-control" id="batas_pedas_puncak" name="batas_pedas_puncak" required value="{{ $boundaries->batas_pedas_puncak ?? 70 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_pedas_akhir" class="form-label">Batas Akhir (P3)</label>
                    <input type="number" class="form-control" id="batas_pedas_akhir" name="batas_pedas_akhir" required value="{{ $boundaries->batas_pedas_akhir ?? 90 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartPedas"></canvas>
            </div>
        </div>
        
        {{-- Kurva Asin (Bahu Kanan) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Asin (Bahu Kanan)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="batas_asin_awal" class="form-label">Batas Awal (S1)</label>
                    <input type="number" class="form-control" id="batas_asin_awal" name="batas_asin_awal" required value="{{ $boundaries->batas_asin_awal ?? 80 }}" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label for="batas_asin_puncak" class="form-label">Batas Puncak (S2)</label>
                    <input type="number" class="form-control" id="batas_asin_puncak" name="batas_asin_puncak" required value="{{ $boundaries->batas_asin_puncak ?? 100 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartAsin"></canvas>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success">Simpan Batas</button>
        </div>
    </form>
    
    <hr>
    
    <form action="{{ route('fuzzy.rasa.calculate') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="rasa" class="form-label" style="font-weight: bold;">Masukkan Nilai Rasa (0-100):</label>
            <input type="number" class="form-control @error('rasa') is-invalid @enderror" 
                id="rasa" name="rasa" value="{{ old('rasa') }}" required min="0" max="100">
            @error('rasa')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Hitung Derajat Keanggotaan</button>
        </div>
    </form>
    
    @if(!empty($results))
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            Hasil Perhitungan untuk Rasa {{ $results['rasa'] }}
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Derajat Keanggotaan Asam: <span class="fw-bold">{{ number_format($results['miu_asam'], 3) }}</span></li>
            <li class="list-group-item">Derajat Keanggotaan Manis: <span class="fw-bold">{{ number_format($results['miu_manis'], 3) }}</span></li>
            <li class="list-group-item">Derajat Keanggotaan Pedas: <span class="fw-bold">{{ number_format($results['miu_pedas'], 3) }}</span></li>
            <li class="list-group-item">Derajat Keanggotaan Asin: <span class="fw-bold">{{ number_format($results['miu_asin'], 3) }}</span></li>
        </ul>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const batasAsamPuncak = document.getElementById('batas_asam_puncak');
        const batasAsamAkhir = document.getElementById('batas_asam_akhir');
        
        const batasManisAwal = document.getElementById('batas_manis_awal');
        const batasManisPuncak = document.getElementById('batas_manis_puncak');
        const batasManisAkhir = document.getElementById('batas_manis_akhir');
        
        const batasPedasAwal = document.getElementById('batas_pedas_awal');
        const batasPedasPuncak = document.getElementById('batas_pedas_puncak');
        const batasPedasAkhir = document.getElementById('batas_pedas_akhir');
        
        const batasAsinAwal = document.getElementById('batas_asin_awal');
        const batasAsinPuncak = document.getElementById('batas_asin_puncak');

        const ctxGabungan = document.getElementById('fuzzyChartGabungan').getContext('2d');
        const ctxAsam = document.getElementById('fuzzyChartAsam').getContext('2d');
        const ctxManis = document.getElementById('fuzzyChartManis').getContext('2d');
        const ctxPedas = document.getElementById('fuzzyChartPedas').getContext('2d');
        const ctxAsin = document.getElementById('fuzzyChartAsin').getContext('2d');

        let chartGabungan;

        const createCombinedChart = (asamPuncak, asamAkhir, manisAwal, manisPuncak, manisAkhir, pedasAwal, pedasPuncak, pedasAkhir, asinAwal, asinPuncak) => {
            const datasets = [
                {
                    label: 'Asam',
                    data: [{ x: 0, y: 1 }, { x: asamPuncak, y: 1 }, { x: asamAkhir, y: 0 }],
                    borderColor: '#28a745',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                },
                {
                    label: 'Manis',
                    data: [{ x: manisAwal, y: 0 }, { x: manisPuncak, y: 1 }, { x: manisAkhir, y: 0 }],
                    borderColor: '#ffc107',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                },
                {
                    label: 'Pedas',
                    data: [{ x: pedasAwal, y: 0 }, { x: pedasPuncak, y: 1 }, { x: pedasAkhir, y: 0 }],
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                },
                {
                    label: 'Asin',
                    data: [{ x: asinAwal, y: 0 }, { x: asinPuncak, y: 1 }, { x: 100, y: 1 }],
                    borderColor: '#007bff',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                }
            ];

            let tickValues = [0, asamPuncak, asamAkhir, manisAwal, manisPuncak, manisAkhir, pedasAwal, pedasPuncak, pedasAkhir, asinAwal, asinPuncak, 100].filter(v => !isNaN(v));
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
                            title: { display: true, text: 'Rasa (0-100)' },
                            min: 0,
                            max: 100,
                            ticks: {
                                autoSkip: false,
                                callback: function(value) {
                                    if (value === 0) return 0;
                                    if (value === asamPuncak) return 'A1: ' + asamPuncak;
                                    if (value === asamAkhir) return 'A2: ' + asamAkhir;
                                    if (value === manisAwal) return 'M1: ' + manisAwal;
                                    if (value === manisPuncak) return 'M2: ' + manisPuncak;
                                    if (value === manisAkhir) return 'M3: ' + manisAkhir;
                                    if (value === pedasAwal) return 'P1: ' + pedasAwal;
                                    if (value === pedasPuncak) return 'P2: ' + pedasPuncak;
                                    if (value === pedasAkhir) return 'P3: ' + pedasAkhir;
                                    if (value === asinAwal) return 'S1: ' + asinAwal;
                                    if (value === asinPuncak) return 'S2: ' + asinPuncak;
                                    if (value === 100) return 100;
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
            
            const labelContainer = document.getElementById('x-labels-gabungan');
            if (labelContainer) {
                labelContainer.innerHTML = '';
                const chartArea = chartGabungan.chartArea;
                const xScale = chartGabungan.scales.x;
                if (chartArea && xScale) {
                    const points = [
                        { val: 0, label: '0', pLabel: '' },
                        { val: asamPuncak, label: asamPuncak, pLabel: 'A1' },
                        { val: asamAkhir, label: asamAkhir, pLabel: 'A2' },
                        { val: manisAwal, label: manisAwal, pLabel: 'M1' },
                        { val: manisPuncak, label: manisPuncak, pLabel: 'M2' },
                        { val: manisAkhir, label: manisAkhir, pLabel: 'M3' },
                        { val: pedasAwal, label: pedasAwal, pLabel: 'P1' },
                        { val: pedasPuncak, label: pedasPuncak, pLabel: 'P2' },
                        { val: pedasAkhir, label: pedasAkhir, pLabel: 'P3' },
                        { val: asinAwal, label: asinAwal, pLabel: 'S1' },
                        { val: asinPuncak, label: asinPuncak, pLabel: 'S2' },
                        { val: 100, label: '100', pLabel: '' },
                    ];
                    
                    points.forEach(function({ val, label, pLabel }) {
                        if (val !== null && !isNaN(val)) {
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

        const createChart = (ctx, dataset, label, color, minX, maxX) => {
            if (ctx.chart) {
                ctx.chart.destroy();
            }
            
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
                            title: { display: true, text: 'Rasa (0-100)' },
                            min: 0,
                            max: 100,
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
            const asamPuncak = parseFloat(batasAsamPuncak.value);
            const asamAkhir = parseFloat(batasAsamAkhir.value);
            const manisAwal = parseFloat(batasManisAwal.value);
            const manisPuncak = parseFloat(batasManisPuncak.value);
            const manisAkhir = parseFloat(batasManisAkhir.value);
            const pedasAwal = parseFloat(batasPedasAwal.value);
            const pedasPuncak = parseFloat(batasPedasPuncak.value);
            const pedasAkhir = parseFloat(batasPedasAkhir.value);
            const asinAwal = parseFloat(batasAsinAwal.value);
            const asinPuncak = parseFloat(batasAsinPuncak.value);
            
            createCombinedChart(asamPuncak, asamAkhir, manisAwal, manisPuncak, manisAkhir, pedasAwal, pedasPuncak, pedasAkhir, asinAwal, asinPuncak);
            
            const datasetAsam = [{ x: 0, y: 1 }, { x: asamPuncak, y: 1 }, { x: asamAkhir, y: 0 }];
            createChart(ctxAsam, datasetAsam, 'Asam', '#28a745', 0, 100);
            
            const datasetManis = [{ x: manisAwal, y: 0 }, { x: manisPuncak, y: 1 }, { x: manisAkhir, y: 0 }];
            createChart(ctxManis, datasetManis, 'Manis', '#ffc107', 0, 100);

            const datasetPedas = [{ x: pedasAwal, y: 0 }, { x: pedasPuncak, y: 1 }, { x: pedasAkhir, y: 0 }];
            createChart(ctxPedas, datasetPedas, 'Pedas', '#dc3545', 0, 100);
            
            const datasetAsin = [{ x: asinAwal, y: 0 }, { x: asinPuncak, y: 1 }, { x: 100, y: 1 }];
            createChart(ctxAsin, datasetAsin, 'Asin', '#007bff', 0, 100);
        };

        [batasAsamPuncak, batasAsamAkhir,
        batasManisAwal, batasManisPuncak, batasManisAkhir,
        batasPedasAwal, batasPedasPuncak, batasPedasAkhir,
        batasAsinAwal, batasAsinPuncak].forEach(input => {
            input.addEventListener('input', function() {
                if (parseInt(this.value) > 100) this.value = 100;
                if (parseInt(this.value) < 0) this.value = 0;
                updateCharts();
            });
        });
        
        updateCharts();
    });
</script>
@endpush