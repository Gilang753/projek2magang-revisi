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

    // Ambil batas fuzzy detail
    $murah_awal = (float) $boundaries->batas_murah_awal;
    $murah_puncak = (float) $boundaries->batas_murah_puncak;
    $murah_akhir = (float) $boundaries->batas_murah_akhir;
    $sedang_awal = (float) $boundaries->batas_sedang_awal;
    $sedang_puncak = (float) $boundaries->batas_sedang_puncak;
    $sedang_akhir = (float) $boundaries->batas_sedang_akhir;
    $mahal_awal = (float) $boundaries->batas_mahal_awal;
    $mahal_puncak = (float) $boundaries->batas_mahal_puncak;
    $mahal_akhir = (float) $boundaries->batas_mahal_akhir;

    // Hitung miu dengan fungsi dan parameter yang benar
    // Perbaiki urutan parameter agar sesuai logika fuzzy
    $miu = [
        // Murah: puncak, akhir
        'murah' => $this->getMiuMurah($harga, $murah_puncak, $murah_akhir),
        // Sedang: awal, puncak, akhir
        'sedang' => $this->getMiuSegitiga($harga, $sedang_awal, $sedang_puncak, $sedang_akhir),
        // Mahal: awal, puncak
        'mahal' => $this->getMiuMahal($harga, $mahal_awal, $mahal_puncak),
    ];

    // Hapus semua data sebelumnya
    FuzzyInput::truncate();

    // Simpan data baru ke database
    FuzzyInput::create([
        'harga' => $harga,
        'p1' => $murah_awal,
        'p2' => $murah_puncak,
        'p3' => $sedang_puncak,
        'p4' => $sedang_akhir,
        'p5' => $mahal_akhir,
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