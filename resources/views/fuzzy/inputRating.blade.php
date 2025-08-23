@extends('layouts.app')

@section('title', 'Pengaturan Fuzzy Rating')

@section('content')
<div class="container" style="max-width: 1200px; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy Rating</h2>

<form action="{{ route('fuzzy.rating.boundaries.store') }}" method="POST" class="mb-5">
        <div style="width: 100%; max-width: 900px; margin: 0 auto 32px auto; background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.09); padding: 24px 16px 32px 16px;">
            <div class="mb-2" style="font-size:13px; color:#555; display:flex; flex-wrap:wrap; justify-content:center; gap:16px;">
                <span><span style="color:#dc3545;font-weight:600;">P1</span> = Input 1 (Rendah Puncak)</span>
                <span><span style="color:#dc3545;font-weight:600;">P2</span> = Input 2 (Rendah Akhir / Sedang Awal)</span>
                <span><span style="color:#dc3545;font-weight:600;">P3</span> = Input 3 (Sedang Puncak)</span>
                <span><span style="color:#dc3545;font-weight:600;">P4</span> = Input 4 (Sedang Akhir / Tinggi Awal)</span>
                <span><span style="color:#dc3545;font-weight:600;">P5</span> = Input 5 (Tinggi Puncak)</span>
                <span><span style="color:#dc3545;font-weight:600;">P6</span> = Input 6 (Tinggi Akhir)</span>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="fuzzyChartGabungan"></canvas>
                <div class="x-labels" id="x-labels-gabungan" style="position: absolute; left: 0; right: 0; bottom: -32px; width: 100%; pointer-events: none; height: 22px;"></div>
            </div>
            <div class="text-center mt-4 fw-bold" style="color:#3b3b3b; letter-spacing:0.5px;">Gabungan</div>
        </div>
        @csrf
        <h5 class="mb-3">Tentukan Batas Fuzzy Rating (0-100)</h5>
        
        {{-- Kurva Rendah (Bahu Kiri) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Rendah (Bahu Kiri)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_rendah_awal" class="form-label">Input 1 (Awal)</label>
                    <input type="number" class="form-control" id="batas_rendah_awal" name="batas_rendah_awal" value="0" readonly min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_rendah_puncak" class="form-label">Input 2 (Puncak)</label>
                    <input type="number" class="form-control" id="batas_rendah_puncak" name="batas_rendah_puncak" required value="{{ $boundaries->batas_rendah_puncak ?? 20 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_rendah_akhir" class="form-label">Input 3 (Akhir)</label>
                    <input type="number" class="form-control" id="batas_rendah_akhir" name="batas_rendah_akhir" required value="{{ $boundaries->batas_rendah_akhir ?? 40 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartRendah"></canvas>
            </div>
        </div>

        {{-- Kurva Sedang (Segitiga) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Sedang (Segitiga)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_sedang_awal" class="form-label">Input 1 (P2)</label>
                    <input type="number" class="form-control" id="batas_sedang_awal" name="batas_sedang_awal" required value="{{ $boundaries->batas_sedang_awal ?? 30 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_sedang_puncak" class="form-label">Input 2 (P3)</label>
                    <input type="number" class="form-control" id="batas_sedang_puncak" name="batas_sedang_puncak" required value="{{ $boundaries->batas_sedang_puncak ?? 50 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_sedang_akhir" class="form-label">Input 3 (P4)</label>
                    <input type="number" class="form-control" id="batas_sedang_akhir" name="batas_sedang_akhir" required value="{{ $boundaries->batas_sedang_akhir ?? 70 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartSedang"></canvas>
            </div>
        </div>

        {{-- Kurva Tinggi (Bahu Kanan) --}}
        <div class="card p-3 mb-4">
            <h6 class="card-title fw-bold">Kurva Tinggi (Bahu Kanan)</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="batas_tinggi_awal" class="form-label">Input 1 (P4)</label>
                    <input type="number" class="form-control" id="batas_tinggi_awal" name="batas_tinggi_awal" required value="{{ $boundaries->batas_tinggi_awal ?? 60 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_tinggi_puncak" class="form-label">Input 2 (P5)</label>
                    <input type="number" class="form-control" id="batas_tinggi_puncak" name="batas_tinggi_puncak" required value="{{ $boundaries->batas_tinggi_puncak ?? 80 }}" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label for="batas_tinggi_akhir" class="form-label">Input 3 (P6)</label>
                    <input type="number" class="form-control" id="batas_tinggi_akhir" name="batas_tinggi_akhir" required value="{{ $boundaries->batas_tinggi_akhir ?? 100 }}" min="0" max="100">
                </div>
            </div>
            <div style="height: 350px; position: relative; margin-top: 20px;">
                <canvas id="fuzzyChartTinggi"></canvas>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success">Simpan Batas</button>
        </div>
    </form>
    
    <hr>
    
    <form action="{{ route('fuzzy.rating.calculate') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="rating" class="form-label" style="font-weight: bold;">Masukkan Nilai Rating (0-100):</label>
            <input type="number" class="form-control @error('rating') is-invalid @enderror" 
                id="rating" name="rating" value="{{ old('rating') }}" required min="0" max="100">
            @error('rating')
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
            Hasil Perhitungan untuk Rating {{ $results['rating'] }}
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Derajat Keanggotaan Rendah: <span class="fw-bold">{{ number_format($results['miu_rendah'], 3) }}</span></li>
            <li class="list-group-item">Derajat Keanggotaan Sedang: <span class="fw-bold">{{ number_format($results['miu_sedang'], 3) }}</span></li>
            <li class="list-group-item">Derajat Keanggotaan Tinggi: <span class="fw-bold">{{ number_format($results['miu_tinggi'], 3) }}</span></li>
        </ul>
    </div>
@endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const batasRendahAwal = document.getElementById('batas_rendah_awal');
        const batasRendahPuncak = document.getElementById('batas_rendah_puncak');
        const batasRendahAkhir = document.getElementById('batas_rendah_akhir');
        
        const batasSedangAwal = document.getElementById('batas_sedang_awal');
        const batasSedangPuncak = document.getElementById('batas_sedang_puncak');
        const batasSedangAkhir = document.getElementById('batas_sedang_akhir');
        
        const batasTinggiAwal = document.getElementById('batas_tinggi_awal');
        const batasTinggiPuncak = document.getElementById('batas_tinggi_puncak');
        const batasTinggiAkhir = document.getElementById('batas_tinggi_akhir');

        const ctxGabungan = document.getElementById('fuzzyChartGabungan').getContext('2d');
        const ctxRendah = document.getElementById('fuzzyChartRendah').getContext('2d');
        const ctxSedang = document.getElementById('fuzzyChartSedang').getContext('2d');
        const ctxTinggi = document.getElementById('fuzzyChartTinggi').getContext('2d');
        let chartGabungan;

        const createCombinedChart = (p1, p2, p2_sedang, p3_sedang, p4_sedang, p4_tinggi, p5, p6_tinggi) => {
            // Gabungkan semua dataset
            const datasets = [
                {
                    label: 'Rendah',
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
                    label: 'Tinggi',
                    data: [{ x: p4_tinggi, y: 0 }, { x: p5, y: 1 }, { x: p6_tinggi, y: 1 }],
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    fill: false,
                    tension: 0,
                    pointRadius: 5
                }
            ];
            
            // Nilai ticks untuk sumbu X
            let tickValues = [0, p1, p2, p2_sedang, p3_sedang, p4_sedang, p4_tinggi, p5, p6_tinggi].filter(v => !isNaN(v));
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
                            title: { display: true, text: 'Rating (0-100)' },
                            min: 0,
                            max: 100,
                            ticks: {
                                autoSkip: false,
                                callback: function(value) {
                                    if (value === 0) return 0;
                                    if (value === p1) return 'P1: ' + p1;
                                    if (value === p2) return 'P2: ' + p2;
                                    if (value === p2_sedang) return 'P2: ' + p2_sedang;
                                    if (value === p3_sedang) return 'P3: ' + p3_sedang;
                                    if (value === p4_sedang) return 'P4: ' + p4_sedang;
                                    if (value === p4_tinggi) return 'P4: ' + p4_tinggi;
                                    if (value === p5) return 'P5: ' + p5;
                                    if (value === p6_tinggi) return 'P6: ' + p6_tinggi;
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
            
            // Render label di bawah grafik gabungan
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
                        else if (val === p4_tinggi) { label = p4_tinggi; pLabel = 'P4'; }
                        else if (val === p5) { label = p5; pLabel = 'P5'; }
                        else if (val === p6_tinggi) { label = p6_tinggi; pLabel = 'P6'; }
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
                            title: { display: true, text: 'Rating (0-100)' },
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
                parseFloat(batasRendahPuncak.value),
                parseFloat(batasRendahAkhir.value),
                parseFloat(batasSedangAwal.value),
                parseFloat(batasSedangPuncak.value),
                parseFloat(batasSedangAkhir.value),
                parseFloat(batasTinggiAwal.value),
                parseFloat(batasTinggiPuncak.value),
                parseFloat(batasTinggiAkhir.value)
            );
            
            const p1 = parseFloat(batasRendahPuncak.value);
            const p2 = parseFloat(batasRendahAkhir.value);
            
            const p2_sedang = parseFloat(batasSedangAwal.value);
            const p3_sedang = parseFloat(batasSedangPuncak.value);
            const p4_sedang = parseFloat(batasSedangAkhir.value);

            const p4_tinggi = parseFloat(batasTinggiAwal.value);
            const p5 = parseFloat(batasTinggiPuncak.value);
            const p6_tinggi = parseFloat(batasTinggiAkhir.value);

            // Grafik Rendah (Bahu Kiri)
            const datasetRendah = [{ x: 0, y: 1 }, { x: p1, y: 1 }, { x: p2, y: 0 }];
            createChart(ctxRendah, datasetRendah, 'Rendah', '#28a745', 0, 0, p1, p2, 100);
            
            // Grafik Sedang (Segitiga)
            const datasetSedang = [{ x: p2_sedang, y: 0 }, { x: p3_sedang, y: 1 }, { x: p4_sedang, y: 0 }];
            createChart(ctxSedang, datasetSedang, 'Sedang', '#ffc107', 0, p2_sedang, p3_sedang, p4_sedang, 100);

            // Grafik Tinggi (Bahu Kanan)
            const datasetTinggi = [{ x: p4_tinggi, y: 0 }, { x: p5, y: 1 }, { x: p6_tinggi, y: 1 }];
            createChart(ctxTinggi, datasetTinggi, 'Tinggi', '#dc3545', 0, p4_tinggi, p5, p6_tinggi, 100);
        };

        [batasRendahPuncak, batasRendahAkhir,
        batasSedangAwal, batasSedangPuncak, batasSedangAkhir,
        batasTinggiAwal, batasTinggiPuncak, batasTinggiAkhir].forEach(input => {
            input.addEventListener('input', function() {
                // Pastikan nilai tetap dalam range 0-100
                if (parseInt(this.value) > 100) this.value = 100;
                if (parseInt(this.value) < 0) this.value = 0;
                updateCharts();
            });
        });
        
        updateCharts();
    });
</script>
@endpush