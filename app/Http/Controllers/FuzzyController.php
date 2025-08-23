<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyInput;
use App\Models\FuzzyBoundary;

class FuzzyController extends Controller
{

public function showInputForm()
{
    $boundaries = FuzzyBoundary::first();
    
    // Ambil hasil terakhir dari database (data paling baru)
    $lastResult = FuzzyInput::latest()->first();
    $results = $lastResult ? [
        'harga' => $lastResult->harga,
        'miu_murah' => $lastResult->miu_murah,
        'miu_sedang' => $lastResult->miu_sedang,
        'miu_mahal' => $lastResult->miu_mahal
    ] : null;

    return view('fuzzy.input', compact('boundaries', 'results'));
}

public function calculateMiu(Request $request)
{
    $request->validate([
        'harga' => 'required|numeric|min:0',
    ]);

    $harga = (float) $request->input('harga');

    $boundaries = FuzzyBoundary::first();
    if (!$boundaries) {
        return redirect()->route('fuzzy.input')->with('error', 'Batas fuzzy belum diatur. Silakan simpan batas terlebih dahulu.');
    }

    $p1 = (float) $boundaries->batas1;
    $p2 = (float) $boundaries->batas2;
    $p3 = (float) $boundaries->batas3;
    $p4 = (float) $boundaries->batas4;
    $p5 = (float) $boundaries->batas5;

    // Hitung miu dengan fungsi yang benar
    $miu = [
        'murah' => $this->getMiuMurah($harga, $p1, $p2),
        'sedang' => $this->getMiuSegitiga($harga, $p2, $p3, $p4),
        'mahal' => $this->getMiuMahal($harga, $p4, $p5),
    ];

    // Hapus semua data sebelumnya
    FuzzyInput::truncate();

    // Simpan data baru ke database
    FuzzyInput::create([
        'harga' => $harga,
        'p1' => $p1,
        'p2' => $p2,
        'p3' => $p3,
        'p4' => $p4,
        'p5' => $p5,
        'miu_murah' => $miu['murah'],
        'miu_sedang' => $miu['sedang'],
        'miu_mahal' => $miu['mahal'],
    ]);

    return redirect()->route('fuzzy.input')->with('success', 'Data berhasil dihitung dan disimpan!');
}

    // Fungsi Keanggotaan Bahu Kiri
    private function getMiuMurah($x, $p1, $p2)
    {
        if ($x <= $p1) { return 1.0; }
        elseif ($x > $p1 && $x < $p2) {
            if (($p2 - $p1) == 0) return 1.0;
            return ($p2 - $x) / ($p2 - $p1);
        } else { return 0.0; }
    }

    // Fungsi Keanggotaan Segitiga
    private function getMiuSegitiga($x, $p2, $p3, $p4)
    {
        if ($x > $p2 && $x <= $p3) {
            if (($p3 - $p2) == 0) return 1.0;
            return ($x - $p2) / ($p3 - $p2);
        } elseif ($x > $p3 && $x < $p4) {
            if (($p4 - $p3) == 0) return 1.0;
            return ($p4 - $x) / ($p4 - $p3);
        } else { return 0.0; }
    }

    // Fungsi Keanggotaan Bahu Kanan
    private function getMiuMahal($x, $p4, $p5)
    {
        if ($x >= $p5) { return 1.0; }
        elseif ($x > $p4 && $x < $p5) {
            if (($p5 - $p4) == 0) return 1.0;
            return ($x - $p4) / ($p5 - $p4);
        } else { return 0.0; }
    }
}