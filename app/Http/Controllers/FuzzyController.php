<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyInput;

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

        // Simpan ke database
        FuzzyInput::create([
            'harga' => $harga
        ]);

        // Hitung derajat keanggotaan
        $miu = [
            'murah' => $this->getMiuMurah($harga),
            'sedang' => $this->getMiuSedang($harga),
            'mahal' => $this->getMiuMahal($harga),
        ];

        // Tampilkan hasil ke view
        return view('fuzzy.result', compact('harga', 'miu'));
    }

    // Menampilkan data yang tersimpan
    public function showData()
    {
        $data = FuzzyInput::orderBy('created_at', 'desc')->get();
        return view('fuzzy.data', compact('data'));
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
        $miu = (12000 - $x) / (12000 - 8000);
        return max(0, min(1, $miu));
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
        $miu = ($x - 10000) / (14000 - 10000);
        return max(0, min(1, $miu));
    } elseif ($x > 14000 && $x <= 18000) {
        $miu = (18000 - $x) / (18000 - 14000);
        return max(0, min(1, $miu));
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
        $miu = ($x - 16000) / (20000 - 16000);
        return max(0, min(1, $miu));
    } else {
        return 1.0;
    }
}
// Penutup class FuzzyController
}
