<?php

namespace App\Http\Controllers;

use App\Models\RekomendasiBoundary;
use Illuminate\Http\Request;

class RekomendasiFuzzyController extends Controller
{
    /**
     * Display the fuzzy rekomendasi settings.
     */
    public function index()
    {
        $boundaries = RekomendasiBoundary::firstOrNew();
        
        return view('fuzzy.inputRekomendasi', compact('boundaries'));
    }

    /**
     * Store/update fuzzy rekomendasi boundaries from form input.
     */
    public function storeBoundaries(Request $request)
    {
        $request->validate([
            'batas_tidak_puncak' => 'required|numeric|min:0|max:100',
            'batas_tidak_akhir' => 'required|numeric|min:0|max:100',
            'batas_rekomendasi_awal' => 'required|numeric|min:0|max:100',
            'batas_rekomendasi_puncak' => 'required|numeric|min:0|max:100',
        ]);

        $boundaries = RekomendasiBoundary::firstOrNew();
        $boundaries->batas_tidak_puncak = $request->batas_tidak_puncak;
        $boundaries->batas_tidak_akhir = $request->batas_tidak_akhir;
        $boundaries->batas_rekomendasi_awal = $request->batas_rekomendasi_awal;
        $boundaries->batas_rekomendasi_puncak = $request->batas_rekomendasi_puncak;
        $boundaries->save();

        return redirect()->route('fuzzy.inputRekomendasi')->with('success', 'Batas fuzzy rekomendasi berhasil disimpan!');
    }
}