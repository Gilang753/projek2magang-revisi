<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyBoundary;

class FuzzyBoundaryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'batas_murah_awal' => 'required|numeric',
            'batas_murah_puncak' => 'required|numeric',
            'batas_murah_akhir' => 'required|numeric',
            'batas_sedang_awal' => 'required|numeric',
            'batas_sedang_puncak' => 'required|numeric',
            'batas_sedang_akhir' => 'required|numeric',
            'batas_mahal_awal' => 'required|numeric',
            'batas_mahal_puncak' => 'required|numeric',
            'batas_mahal_akhir' => 'required|numeric',
        ]);


    $boundaries = FuzzyBoundary::firstOrNew();
    // Simpan semua input batas ke database
    $boundaries->batas_murah_awal = $request->input('batas_murah_awal');
    $boundaries->batas_murah_puncak = $request->input('batas_murah_puncak');
    $boundaries->batas_murah_akhir = $request->input('batas_murah_akhir');
    $boundaries->batas_sedang_awal = $request->input('batas_sedang_awal');
    $boundaries->batas_sedang_puncak = $request->input('batas_sedang_puncak');
    $boundaries->batas_sedang_akhir = $request->input('batas_sedang_akhir');
    $boundaries->batas_mahal_awal = $request->input('batas_mahal_awal');
    $boundaries->batas_mahal_puncak = $request->input('batas_mahal_puncak');
    $boundaries->batas_mahal_akhir = $request->input('batas_mahal_akhir');

    // Untuk kompatibilitas dengan logika lama (P1-P5)
    $boundaries->batas1 = $request->input('batas_murah_puncak');
    $boundaries->batas2 = $request->input('batas_murah_akhir');
    $boundaries->batas3 = $request->input('batas_sedang_puncak');
    $boundaries->batas4 = $request->input('batas_mahal_awal');
    $boundaries->batas5 = $request->input('batas_mahal_puncak');

    $boundaries->save();

        return redirect()->route('fuzzy.input')->with('success', 'Batas fuzzy berhasil disimpan!');
    }
}