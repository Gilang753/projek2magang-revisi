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

        <h5 class="text-center mb-3">Grafik Fungsi Keanggotaan Rating</h5>
        <div style="height: 300px;">
            <canvas id="fuzzyChart"></canvas>
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
    const ctx = document.getElementById('fuzzyChart').getContext('2d');
    let fuzzyChart;

    // Parameter fuzzy rating
    const rendahMin = 20;
    const rendahMax = 60;
    const sedangMin = 20;
    const sedangMax = 100;
    const tinggiMin = 60;
    const tinggiMax = 100;

    // Inisialisasi chart
    function initChart() {
        fuzzyChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'Rendah',
                        data: [{x: 0, y: 1}, {x: rendahMin, y: 1}, {x: rendahMax, y: 0}],
                        borderColor: '#dc3545',
                        borderWidth: 2,
                        fill: false,
                        tension: 0
                    },
                    {
                        label: 'Sedang',
                        data: [{x: sedangMin, y: 0}, {x: (sedangMin+sedangMax)/2, y: 1}, {x: sedangMax, y: 0}],
                        borderColor: '#ffc107',
                        borderWidth: 2,
                        fill: false,
                        tension: 0
                    },
                    {
                        label: 'Tinggi',
                        data: [{x: tinggiMin, y: 0}, {x: tinggiMax, y: 1}, {x: tinggiMax, y: 1}],
                        borderColor: '#28a745',
                        borderWidth: 2,
                        fill: false,
                        tension: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: { display: true, text: 'Nilai Rating' },
                        min: 0,
                        max: 100
                    },
                    y: {
                        min: 0,
                        max: 1.0,
                        title: { display: true, text: 'Derajat Keanggotaan' }
                    }
                }
            }
        });
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
    
    // Calculate fuzzy function
    function calculateFuzzy(rating) {
        if (rating >= 1 && rating <= 5) {
            const nilaiRating = rating * 20;
            
            // Update garis input rating pada chart
            updateChartWithRating(nilaiRating);
            
            // Hitung nilai keanggotaan
            $.ajax({
                url: "{{ route('fuzzy.calculateRatingPreview') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    rating_bintang: rating
                },
                success: function(response) {
                    if (response.success) {
                        // Update values
                        $('#lowValue').text(response.result.keanggotaan_rendah);
                        $('#mediumValue').text(response.result.keanggotaan_sedang);
                        $('#highValue').text(response.result.keanggotaan_tinggi);
                        
                        // Update progress bars
                        $('#lowBar').css('width', (response.result.keanggotaan_rendah * 100) + '%');
                        $('#mediumBar').css('width', (response.result.keanggotaan_sedang * 100) + '%');
                        $('#highBar').css('width', (response.result.keanggotaan_tinggi * 100) + '%');
                        
                        // Update dominant category
                        $('#categoryText').text(response.kategori);
                        const alertClass = response.kategori === 'Tinggi' ? 'alert-success' : 
                                          response.kategori === 'Sedang' ? 'alert-warning' : 'alert-danger';
                        $('#dominantCategory').removeClass('alert-success alert-warning alert-danger')
                                             .addClass(alertClass);
                        
                        // Show result
                        $('#calculationResult').fadeIn();
                    }
                }
            });
        }
    }

    // Update chart dengan garis rating
    function updateChartWithRating(nilaiRating) {
        // Hapus dataset input rating jika sudah ada
        if (fuzzyChart.data.datasets.length > 3) {
            fuzzyChart.data.datasets.pop();
        }
        
        // Tambahkan garis input rating
        fuzzyChart.data.datasets.push({
            label: 'Input Rating',
            data: [{x: nilaiRating, y: 0}, {x: nilaiRating, y: 1}],
            borderColor: '#0d6efd',
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0
        });
        
        fuzzyChart.update();
    }

    // Inisialisasi chart pertama kali
    initChart();
});
</script>
@endpush