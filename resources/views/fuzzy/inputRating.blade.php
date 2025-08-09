@extends('layouts.app')

@section('title', 'Setting Fuzzy Rating')

@section('content')
<div class="container" style="max-width: 800px; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h2 class="text-center mb-4">Hitung Derajat Keanggotaan Fuzzy Rating</h2>
    <form id="ratingForm" action="{{ route('fuzzy.calculateRating') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <h5 class="mb-3">Rating Bintang (1-5)</h5>
            <div class="star-rating text-center mb-3">
                @for($i = 1; $i <= 5; $i++)
                    <i class="far fa-star rating-star" data-rating="{{ $i }}" 
                       style="font-size: 2rem; cursor: pointer; margin: 0 5px;"></i>
                @endfor
                <input type="hidden" name="rating_bintang" id="rating_bintang" value="">
            </div>
        </div>

        <hr>

        <h5 class="text-center mb-3">Grafik Fungsi Keanggotaan Fuzzy Rating</h5>
        <div class="mb-4 d-flex flex-column align-items-center gap-4">
            <div style="width: 100%; max-width: 900px; margin-bottom: 32px;">
                <div style="height: 350px; position: relative;">
                    <canvas id="fuzzyChartRendah"></canvas>
                    <div class="x-labels" id="x-labels-rendah"></div>
                </div>
                <div class="text-center mt-2">Rendah</div>
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
                    <canvas id="fuzzyChartTinggi"></canvas>
                    <div class="x-labels" id="x-labels-tinggi"></div>
                </div>
                <div class="text-center mt-2">Tinggi</div>
            </div>
        </div>

        <hr>

        <div id="calculationResult" class="mt-4 p-3 bg-light rounded" style="display:none;">
            <h6>Hasil Perhitungan:</h6>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card mb-3 border-danger">
                        <div class="card-body text-center">
                            <h6>Rendah</h6>
                            <h4 id="lowValue">0</h4>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-danger" id="lowBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3 border-warning">
                        <div class="card-body text-center">
                            <h6>Sedang</h6>
                            <h4 id="mediumValue">0</h4>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" id="mediumBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3 border-success">
                        <div class="card-body text-center">
                            <h6>Tinggi</h6>
                            <h4 id="highValue">0</h4>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" id="highBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert mt-3 mb-0" id="dominantCategory">
                <strong>Kategori Dominan:</strong> <span id="categoryText">-</span>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary">Hitung Derajat Keanggotaan</button>
        </div>
    </form>
</div>

<div class="container mt-5" style="max-width: 800px;">
    <h3 class="text-center mb-4">Histori Perhitungan Rating</h3>
    @if ($dataRating->isEmpty())
        <div class="alert alert-info text-center">Belum ada data perhitungan rating.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Waktu</th>
                        <th>Rating</th>
                        <th>Nilai</th>
                        <th>Rendah</th>
                        <th>Sedang</th>
                        <th>Tinggi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataRating as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                        <td>
                            @for($i = 1; $i <= $item->rating_bintang; $i++)
                                <i class="fas fa-star text-warning"></i>
                            @endfor
                        </td>
                        <td>{{ $item->nilai_rating }}</td>
                        <td>{{ number_format($item->keanggotaan_rendah, 3) }}</td>
                        <td>{{ number_format($item->keanggotaan_sedang, 3) }}</td>
                        <td>{{ number_format($item->keanggotaan_tinggi, 3) }}</td>
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
    // Boundaries rating (bisa diubah sesuai kebutuhan)
    const b1 = 20, b2 = 40, b3 = 60, b4 = 80, b5 = 100;
    const ctxRendah = document.getElementById('fuzzyChartRendah').getContext('2d');
    const ctxSedang = document.getElementById('fuzzyChartSedang').getContext('2d');
    const ctxTinggi = document.getElementById('fuzzyChartTinggi').getContext('2d');

    function getRatingValue() {
        const val = $("#rating_bintang").val();
        if (!val) return NaN;
        return parseInt(val) * 20;
    }

    function createChart(ctx, dataset, ratingValue, minX, maxX, warna, a, b, c, d, labelContainerId) {
        if (ctx.chart) ctx.chart.destroy();
        const datasets = [dataset];
        if (!isNaN(ratingValue)) {
            datasets.push({
                label: 'Input Rating',
                data: [{ x: ratingValue, y: 0 }, { x: ratingValue, y: 1.0 }],
                borderColor: '#0d6efd',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 0,
            });
        }
        let tickValues = [a, b, c, d];
        if (!isNaN(ratingValue) && !tickValues.includes(ratingValue)) tickValues.push(ratingValue);
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
                        title: { display: true, text: 'Nilai Rating' },
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
                                if (!isNaN(ratingValue) && value === ratingValue) return 'Rating: ' + ratingValue;
                                return '';
                            },
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
                    if (!isNaN(ratingValue) && val === ratingValue) label = ratingValue;
                    else label = '';
                    const div = document.createElement('div');
                    div.style.position = 'absolute';
                    div.style.transform = 'translateX(-50%)';
                    div.style.left = ((val - Math.min(a, b, c, d, ratingValue)) / (Math.max(a, b, c, d, ratingValue) - Math.min(a, b, c, d, ratingValue)) * 100) + '%';
                    div.style.textAlign = 'center';
                    div.style.minWidth = '60px';
                    div.style.fontWeight = 'bold';
                    div.style.color = '#dc3545';
                    div.innerText = label;
                    labelContainer.appendChild(div);
                });
            }
        }
    }

    function updateCharts() {
        const a = b1, b = b2, c = b3, d = b4;
        const ratingValue = getRatingValue();
        // Dataset untuk masing-masing grafik
        const datasetRendah = {
            label: 'Rendah',
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
        const datasetTinggi = {
            label: 'Tinggi',
            data: [{ x: c, y: 0 }, { x: d, y: 1 }, { x: d + 20, y: 1 }],
            borderColor: '#dc3545',
            borderWidth: 2,
            fill: false,
            tension: 0,
            pointRadius: 5,
        };
        createChart(ctxRendah, datasetRendah, ratingValue, 0, d + 20, '#28a745', a, b, c, d, 'x-labels-rendah');
        createChart(ctxSedang, datasetSedang, ratingValue, 0, d + 20, '#ffc107', a, b, c, d, 'x-labels-sedang');
        createChart(ctxTinggi, datasetTinggi, ratingValue, 0, d + 20, '#dc3545', a, b, c, d, 'x-labels-tinggi');
    }

    // Star rating selection
    $('.rating-star').hover(function() {
        const rating = $(this).data('rating');
        highlightStars(rating);
    });
    $('.rating-star').click(function() {
        const rating = $(this).data('rating');
        $('#rating_bintang').val(rating);
        highlightStars(rating);
        updateCharts();
        calculateFuzzy(rating);
    });
    function highlightStars(rating) {
        $('.rating-star').removeClass('fas').addClass('far');
        $('.rating-star').each(function() {
            if ($(this).data('rating') <= rating) {
                $(this).removeClass('far').addClass('fas');
            }
        });
    }
    // Calculate fuzzy function (AJAX tetap sama)
    function calculateFuzzy(rating) {
        if (rating >= 1 && rating <= 5) {
            $.ajax({
                url: "{{ route('fuzzy.calculateRatingPreview') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    rating_bintang: rating
                },
                success: function(response) {
                    if (response.success) {
                        $('#lowValue').text(response.result.keanggotaan_rendah);
                        $('#mediumValue').text(response.result.keanggotaan_sedang);
                        $('#highValue').text(response.result.keanggotaan_tinggi);
                        $('#lowBar').css('width', (response.result.keanggotaan_rendah * 100) + '%');
                        $('#mediumBar').css('width', (response.result.keanggotaan_sedang * 100) + '%');
                        $('#highBar').css('width', (response.result.keanggotaan_tinggi * 100) + '%');
                        $('#categoryText').text(response.kategori);
                        const alertClass = response.kategori === 'Tinggi' ? 'alert-success' : 
                                          response.kategori === 'Sedang' ? 'alert-warning' : 'alert-danger';
                        $('#dominantCategory').removeClass('alert-success alert-warning alert-danger')
                                             .addClass(alertClass);
                        $('#calculationResult').fadeIn();
                    }
                }
            });
        }
    }
    // Inisialisasi grafik saat halaman dimuat
    updateCharts();
});
</script>
@endpush