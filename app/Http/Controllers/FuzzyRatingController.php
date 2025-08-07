<?php

namespace App\Http\Controllers;

use App\Models\FuzzyRating;
use Illuminate\Http\Request;

class FuzzyRatingController extends Controller
{
    /**
     * Menampilkan form input rating
     */
    public function inputRating()
    {
        $dataRating = FuzzyRating::latest()->paginate(10);
        return view('fuzzy.inputRating', [
            'dataRating' => $dataRating,
            'title' => 'Fuzzy Rating System'
        ]);
    }

    /**
     * Preview perhitungan (AJAX)
     */
    public function calculateRatingPreview(Request $request)
    {
        $request->validate(['rating_bintang' => 'required|integer|between:1,5']);
        
        $result = $this->calculateMembership($request->rating_bintang);
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'kategori' => $this->getDominantCategory($result)
        ]);
    }

    /**
     * Proses perhitungan dan simpan data
     */
    public function calculateRating(Request $request)
    {
        $request->validate(['rating_bintang' => 'required|integer|between:1,5']);
        
        $data = $this->calculateMembership($request->rating_bintang);
        
        $rating = FuzzyRating::create([
            'rating_bintang' => $request->rating_bintang,
            'nilai_rating' => $data['nilai_rating'],
            'keanggotaan_rendah' => $data['keanggotaan_rendah'],
            'keanggotaan_sedang' => $data['keanggotaan_sedang'],
            'keanggotaan_tinggi' => $data['keanggotaan_tinggi']
        ]);

        return redirect()->route('fuzzy.resultRating', $rating->id);
    }

    /**
     * Tampilkan hasil perhitungan
     */
    public function resultRating($id)
    {
        $rating = FuzzyRating::findOrFail($id);
        
        return view('fuzzy.resultRating', [
            'rating_bintang' => $rating->rating_bintang,
            'nilai_rating' => $rating->nilai_rating,
            'keanggotaan_rendah' => $rating->keanggotaan_rendah,
            'keanggotaan_sedang' => $rating->keanggotaan_sedang,
            'keanggotaan_tinggi' => $rating->keanggotaan_tinggi,
            'kategori_dominan' => $this->getDominantCategory($rating)
        ]);
    }

    /**
     * Fungsi perhitungan keanggotaan fuzzy
     */
    private function calculateMembership($ratingBintang)
    {
        $nilaiRating = $ratingBintang * 20; // Konversi ke skala 0-100
        
        // Parameter tetap untuk sistem rating
        $rendahMin = 20;
        $rendahMax = 60;
        $sedangMin = 20;
        $sedangMax = 100;
        $tinggiMin = 60;
        $tinggiMax = 100;
        
        // Hitung derajat keanggotaan (fungsi segitiga)
        $keanggotaanRendah = max(0, min(1, ($rendahMax - $nilaiRating) / ($rendahMax - $rendahMin)));
        $keanggotaanSedang = max(0, min(1, min(
            ($nilaiRating - $sedangMin) / ($sedangMax - $sedangMin),
            ($sedangMax - $nilaiRating) / ($sedangMax - $sedangMin)
        )));
        $keanggotaanTinggi = max(0, min(1, ($nilaiRating - $tinggiMin) / ($tinggiMax - $tinggiMin)));
        
        return [
            'rating_bintang' => $ratingBintang,
            'nilai_rating' => $nilaiRating,
            'keanggotaan_rendah' => round($keanggotaanRendah, 3),
            'keanggotaan_sedang' => round($keanggotaanSedang, 3),
            'keanggotaan_tinggi' => round($keanggotaanTinggi, 3)
        ];
    }

    /**
     * Tentukan kategori dominan
     */
    private function getDominantCategory($data)
    {
        $max = max(
            $data['keanggotaan_rendah'],
            $data['keanggotaan_sedang'],
            $data['keanggotaan_tinggi']
        );
        
        if ($max == $data['keanggotaan_tinggi']) {
            return 'Tinggi';
        }
        if ($max == $data['keanggotaan_sedang']) {
            return 'Sedang';
        }
        return 'Rendah';
    }
}