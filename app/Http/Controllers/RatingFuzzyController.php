<?php

namespace App\Http\Controllers;

use App\Models\RatingBoundary;
use App\Models\RatingHistory;
use Illuminate\Http\Request;

class RatingFuzzyController extends Controller
{
// App\Http\Controllers\RatingFuzzyController.php

public function index()
{
    $boundaries = RatingBoundary::firstOrNew();
    
    // Ambil hasil terakhir dari database (data paling baru)
    $lastResult = RatingHistory::latest()->first();
    $results = $lastResult ? [
        'rating' => $lastResult->rating,
        'miu_rendah' => $lastResult->miu_rendah,
        'miu_sedang' => $lastResult->miu_sedang,
        'miu_tinggi' => $lastResult->miu_tinggi
    ] : null;
    
    return view('fuzzy.inputRating', compact('boundaries', 'results'));
}

public function calculate(Request $request)
{
    $request->validate([
        'rating' => 'required|numeric|min:0|max:100'
    ]);

    $boundaries = RatingBoundary::firstOrNew();
    $rating = $request->rating;
    
    // Hitung miu rendah
    $miu_rendah = 0;
    if ($rating <= $boundaries->batas_rendah_puncak) {
        $miu_rendah = 1;
    } elseif ($rating > $boundaries->batas_rendah_puncak && $rating < $boundaries->batas_rendah_akhir) {
        $miu_rendah = ($boundaries->batas_rendah_akhir - $rating) / 
                      ($boundaries->batas_rendah_akhir - $boundaries->batas_rendah_puncak);
    }

    // Hitung miu sedang
    $miu_sedang = 0;
    if ($rating > $boundaries->batas_sedang_awal && $rating < $boundaries->batas_sedang_puncak) {
        $miu_sedang = ($rating - $boundaries->batas_sedang_awal) / 
                      ($boundaries->batas_sedang_puncak - $boundaries->batas_sedang_awal);
    } elseif ($rating == $boundaries->batas_sedang_puncak) {
        $miu_sedang = 1;
    } elseif ($rating > $boundaries->batas_sedang_puncak && $rating < $boundaries->batas_sedang_akhir) {
        $miu_sedang = ($boundaries->batas_sedang_akhir - $rating) / 
                     ($boundaries->batas_sedang_akhir - $boundaries->batas_sedang_puncak);
    }

    // Hitung miu tinggi
    $miu_tinggi = 0;
    if ($rating > $boundaries->batas_tinggi_awal && $rating < $boundaries->batas_tinggi_puncak) {
        $miu_tinggi = ($rating - $boundaries->batas_tinggi_awal) / 
                     ($boundaries->batas_tinggi_puncak - $boundaries->batas_tinggi_awal);
    } elseif ($rating >= $boundaries->batas_tinggi_puncak) {
        $miu_tinggi = 1;
    }

    // Hapus semua data sebelumnya
    RatingHistory::truncate();

    // Simpan ke history
    RatingHistory::create([
        'rating' => $rating,
        'p1' => $boundaries->batas_rendah_puncak,
        'p2' => $boundaries->batas_rendah_akhir,
        'p3' => $boundaries->batas_sedang_puncak,
        'p4' => $boundaries->batas_sedang_akhir,
        'p5' => $boundaries->batas_tinggi_puncak,
        'miu_rendah' => $miu_rendah,
        'miu_sedang' => $miu_sedang,
        'miu_tinggi' => $miu_tinggi
    ]);

    return redirect()->route('fuzzy.inputRating')->with('success', 'Perhitungan berhasil disimpan!');
}
}