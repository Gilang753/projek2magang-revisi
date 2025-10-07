@extends('layouts.app')

@section('title', 'Pengaturan Fuzzy Rekomendasi')

@section('content')
<div class="container" style="max-width: 1200px; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <h2 class="text-center mb-4">Pengaturan Batas Output Rekomendasi</h2>

    <form action="{{ route('fuzzy.rekomendasi.boundaries.store') }}" method="POST" class="mb-5">
        @csrf
        
        <div style="width: 100%; max-width: 900px; margin: 0 auto 32px auto; background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.09); padding: 24px 16px 32px 16px;">
            <div class="mb-2" style="font-size:13px; color:#555; display:flex; flex-wrap:wrap; justify-content:center; gap:16px;">
                <span><span style="color:#dc3545;font-weight:600;">T1</span> = Tidak Rekomendasi Puncak</span>
                <span><span style="color:#dc3545;font-weight:600;">T2</span> = Tidak Rekomendasi Akhir / Rekomendasi Awal</span>
                <span><span style="color:#28a745;font-weight:600;">R1</span> = Rekomendasi Awal</span>
                <span><span style="color:#28a745;font-weight:600;">R2</span> = Rekomendasi Puncak</span>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="fuzzyChartGabungan"></canvas>
                <div class="x-labels" id="x-labels-gabungan" style="position: absolute; left: 0; right: 0; bottom: -32px; width: 100%; pointer-events: none; height: 22px;"></div>
            </div>
            <div class="text-center mt-4 fw-bold" style="color:#3b3b3b; letter-spacing:0.5px;">Gabungan</div>
        </div>
        
        <h5 class="mb-3">Tentukan Batas Output Rekomendasi (0-100)</h5>
        
        {{-- Kurva Tidak Rekomendasi (Bahu Kiri) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Tidak Rekomendasi (Bahu Kiri)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="batas_tidak_puncak" class="form-label">Batas Puncak (T1)</label>
                    <input type="number" class="form-control" id="batas_tidak_puncak" name="batas_tidak_puncak" required value="{{ $boundaries->batas_tidak_puncak ?? 30 }}" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label for="batas_tidak_akhir" class="form-label">Batas Akhir (T2)</label>
                    <input type="number" class="form-control" id="batas_tidak_akhir" name="batas_tidak_akhir" required value="{{ $boundaries->batas_tidak_akhir ?? 50 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartTidak"></canvas>
            </div>
        </div>

        {{-- Kurva Rekomendasi (Bahu Kanan) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Rekomendasi (Bahu Kanan)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="batas_rekomendasi_awal" class="form-label">Batas Awal (R1)</label>
                    <input type="number" class="form-control" id="batas_rekomendasi_awal" name="batas_rekomendasi_awal" required value="{{ $boundaries->batas_rekomendasi_awal ?? 50 }}" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label for="batas_rekomendasi_puncak" class="form-label">Batas Puncak (R2)</label>
                    <input type="number" class="form-control" id="batas_rekomendasi_puncak" name="batas_rekomendasi_puncak" required value="{{ $boundaries->batas_rekomendasi_puncak ?? 70 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartRekomendasi"></canvas>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success">Simpan Batas</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const batasTidakPuncak = document.getElementById('batas_tidak_puncak');
        const batasTidakAkhir = document.getElementById('batas_tidak_akhir');
        const batasRekomendasiAwal = document.getElementById('batas_rekomendasi_awal');
        const batasRekomendasiPuncak = document.getElementById('batas_rekomendasi_puncak');

        const ctxGabungan = document.getElementById('fuzzyChartGabungan').getContext('2d');
        const ctxTidak = document.getElementById('fuzzyChartTidak').getContext('2d');
        const ctxRekomendasi = document.getElementById('fuzzyChartRekomendasi').getContext('2d');

        let chartGabungan;

        const createCombinedChart = (tidakPuncak, tidakAkhir, rekomendasiAwal, rekomendasiPuncak) => {
            const datasets = [
                {
                    label: 'Tidak Rekomendasi',
                    data: [{ x: 0, y: 1 }, { x: tidakPuncak, y: 1 }, { x: tidakAkhir, y: 0 }],
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                },
                {
                    label: 'Rekomendasi',
                    data: [{ x: rekomendasiAwal, y: 0 }, { x: rekomendasiPuncak, y: 1 }, { x: 100, y: 1 }],
                    borderColor: '#28a745',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                }
            ];

            let tickValues = [0, tidakPuncak, tidakAkhir, rekomendasiAwal, rekomendasiPuncak, 100].filter(v => !isNaN(v));
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
                            title: { display: true, text: 'Nilai Rekomendasi (0-100)' },
                            min: 0,
                            max: 100,
                            ticks: {
                                autoSkip: false,
                                callback: function(value) {
                                    if (value === 0) return 0;
                                    if (value === tidakPuncak) return 'T1: ' + tidakPuncak;
                                    if (value === tidakAkhir) return 'T2: ' + tidakAkhir;
                                    if (value === rekomendasiAwal) return 'R1: ' + rekomendasiAwal;
                                    if (value === rekomendasiPuncak) return 'R2: ' + rekomendasiPuncak;
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
                        { val: tidakPuncak, label: tidakPuncak, pLabel: 'T1' },
                        { val: tidakAkhir, label: tidakAkhir, pLabel: 'T2' },
                        { val: rekomendasiAwal, label: rekomendasiAwal, pLabel: 'R1' },
                        { val: rekomendasiPuncak, label: rekomendasiPuncak, pLabel: 'R2' },
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
                            title: { display: true, text: 'Nilai Rekomendasi (0-100)' },
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
            const tidakPuncak = parseFloat(batasTidakPuncak.value);
            const tidakAkhir = parseFloat(batasTidakAkhir.value);
            const rekomendasiAwal = parseFloat(batasRekomendasiAwal.value);
            const rekomendasiPuncak = parseFloat(batasRekomendasiPuncak.value);
            
            createCombinedChart(tidakPuncak, tidakAkhir, rekomendasiAwal, rekomendasiPuncak);
            
            const datasetTidak = [{ x: 0, y: 1 }, { x: tidakPuncak, y: 1 }, { x: tidakAkhir, y: 0 }];
            createChart(ctxTidak, datasetTidak, 'Tidak Rekomendasi', '#dc3545', 0, 100);
            
            const datasetRekomendasi = [{ x: rekomendasiAwal, y: 0 }, { x: rekomendasiPuncak, y: 1 }, { x: 100, y: 1 }];
            createChart(ctxRekomendasi, datasetRekomendasi, 'Rekomendasi', '#28a745', 0, 100);
        };

        [batasTidakPuncak, batasTidakAkhir, batasRekomendasiAwal, batasRekomendasiPuncak].forEach(input => {
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