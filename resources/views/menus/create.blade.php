@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tambah Menu Baru</h1>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Menu</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi Menu</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    <div class="form-text">Maksimal 500 karakter</div>
                </div>
                
                <div class="mb-3">
                    <label for="harga_seporsi" class="form-label">Harga per Porsi</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga_seporsi" name="harga_seporsi" min="0" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="nilai_rasa" class="form-label">Nilai Rasa (0-100)</label>
                    <input type="number" class="form-control" id="nilai_rasa" name="nilai_rasa" min="0" max="100" required oninput="updateRasaBox()">
                    <div class="form-text">Masukkan nilai antara 0 sampai 100</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hasil Rasa</label>
                    <input type="text" class="form-control" id="hasil_rasa" readonly>
                </div>
                <script>
                function updateRasaBox() {
                    const nilai = parseFloat(document.getElementById('nilai_rasa').value);
                    let rasa = '';
                    if (!isNaN(nilai)) {
                        if (nilai >= 0 && nilai <= 40) {
                            rasa = 'Asam';
                        } else if (nilai > 20 && nilai <= 60) {
                            rasa = 'Manis';
                        } else if (nilai > 40 && nilai <= 80) {
                            rasa = 'Pedas';
                        } else if (nilai > 60 && nilai <= 100) {
                            rasa = 'Asin';
                        } else {
                            rasa = 'Tidak diketahui';
                        }
                    }
                    document.getElementById('hasil_rasa').value = rasa;
                }
                </script>
                
                <div class="mb-3">
                    <label for="nilai_rating" class="form-label">Nilai Rating (0-100)</label>
                    <input type="number" class="form-control" id="nilai_rating" name="nilai_rating" min="0" max="100" required oninput="updateRatingBox()">
                    <div class="form-text">Masukkan nilai antara 0 sampai 100</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hasil Rating</label>
                    <input type="text" class="form-control" id="hasil_rating" readonly>
                </div>
                <script>
                function updateRatingBox() {
                    const nilai = parseFloat(document.getElementById('nilai_rating').value);
                    let rating = '';
                    if (!isNaN(nilai)) {
                        if (nilai >= 0 && nilai < 20) {
                            rating = '★';
                        } else if (nilai >= 20 && nilai < 40) {
                            rating = '★★';
                        } else if (nilai >= 40 && nilai < 60) {
                            rating = '★★★';
                        } else if (nilai >= 60 && nilai < 80) {
                            rating = '★★★★';
                        } else if (nilai >= 80 && nilai <= 100) {
                            rating = '★★★★★';
                        } else {
                            rating = 'Tidak diketahui';
                        }
                    }
                    document.getElementById('hasil_rating').value = rating;
                }
                </script>
                
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar Menu</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                    <div class="form-text">Ukuran maksimal 2MB (JPEG, PNG, JPG, GIF)</div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection