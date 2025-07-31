<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FuzzyController extends Controller
{
    public function showInputForm()
    {
        return view('fuzzy.input');
    }

    public function calculateMiu(Request $request)
    {
        $request->validate([
            'harga' => 'required|numeric|min:5000|max:25000',
        ]);

        $harga = (float) $request->input('harga');

        $miu = [
            'murah' => $this->getMiuMurah($harga),
            'sedang' => $this->getMiuSedang($harga),
            'mahal' => $this->getMiuMahal($harga),
        ];

        return view('fuzzy.result', compact('harga', 'miu'));
    }

   /**
 * Fungsi keanggotaan untuk 'Murah'
 * - Nilai 1 (paling murah) jika harga ≤ 8.000
 * - Turun linear antara 8.000-12.000
 * - Nilai 0 jika harga > 12.000
 */
private function getMiuMurah($x)
{
    if ($x <= 8000) {
        return 1.0;
    } elseif ($x > 8000 && $x <= 12000) {
        return (12000 - $x) / (12000 - 8000); // Turun linear dari 8k ke 12k
    } else {
        return 0.0;
    }
}

/**
 * Fungsi keanggotaan untuk 'Sedang'
 * - Naik linear antara 10.000-14.000
 * - Turun linear antara 14.000-18.000
 * - Nilai 0 di luar range tersebut
 */
private function getMiuSedang($x)
{
    if ($x >= 10000 && $x <= 14000) {
        return ($x - 10000) / (14000 - 10000); // Naik linear dari 10k ke 14k
    } elseif ($x > 14000 && $x <= 18000) {
        return (18000 - $x) / (18000 - 14000); // Turun linear dari 14k ke 18k
    } else {
        return 0.0;
    }
}

/**
 * Fungsi keanggotaan untuk 'Mahal'
 * - Nilai 0 jika harga < 16.000
 * - Naik linear antara 16.000-20.000
 * - Nilai 1 jika harga ≥ 20.000
 */
private function getMiuMahal($x)
{
    if ($x < 16000) {
        return 0.0;
    } elseif ($x >= 16000 && $x < 20000) {
        return ($x - 16000) / (20000 - 16000); // Naik linear dari 16k ke 20k
    } else {
        return 1.0;
    }
}
}