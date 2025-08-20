<?php

namespace App\Http\Controllers;

use App\Models\RatingBoundary;
use App\Models\RatingHistory;
use Illuminate\Http\Request;

class RatingFuzzyController extends Controller
{
    public function index()
    {
        $boundaries = RatingBoundary::firstOrNew();
        $histories = RatingHistory::latest()->get();
        
        return view('fuzzy.inputRating', compact('boundaries', 'histories'));
        
    }

    public function storeBoundaries(Request $request)
    {
        $validated = $request->validate([
            'batas_rendah_puncak' => 'required|numeric|min:0|max:100',
            'batas_rendah_akhir' => 'required|numeric|min:0|max:100',
            'batas_sedang_awal' => 'required|numeric|min:0|max:100',
            'batas_sedang_puncak' => 'required|numeric|min:0|max:100',
            'batas_sedang_akhir' => 'required|numeric|min:0|max:100',
            'batas_tinggi_awal' => 'required|numeric|min:0|max:100',
            'batas_tinggi_puncak' => 'required|numeric|min:0|max:100',
            'batas_tinggi_akhir' => 'required|numeric|min:0|max:100',
        ]);

        $validated['batas_rendah_awal'] = 0;

        RatingBoundary::updateOrCreate(['id' => 1], $validated);

        return redirect()->back()->with('success', 'Batas rating fuzzy berhasil disimpan!');
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

        return redirect()->back()->with([
            'results' => [
                'rating' => $rating,
                'miu_rendah' => $miu_rendah,
                'miu_sedang' => $miu_sedang,
                'miu_tinggi' => $miu_tinggi
            ]
        ]);
    }
}