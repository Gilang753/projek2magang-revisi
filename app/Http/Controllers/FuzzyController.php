<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyInput;

class FuzzyController extends Controller
{
    // Fungsi ini tetap menampilkan halaman input beserta historinya
    public function showInputForm()
    {
        $data = FuzzyInput::orderBy('created_at', 'desc')->get();
        $boundaries = \App\Models\FuzzyBoundary::first();
        return view('fuzzy.input', compact('data', 'boundaries'));
    }

    // ⭐ Fungsi ini diubah agar langsung mengarahkan ke halaman hasil
    public function calculateMiu(Request $request)
    {

        $request->validate([
            'harga' => 'required|numeric|min:0',
        ]);

        $harga = (float) $request->input('harga');

        // Ambil boundaries dari database
        $boundaries = \App\Models\FuzzyBoundary::first();
        if (!$boundaries) {
            return redirect()->route('fuzzy.input')->with('error', 'Batas fuzzy belum diatur.');
        }
        $a = (float) $boundaries->batas1;
        $b = (float) $boundaries->batas2;
        $c = (float) $boundaries->batas3;
        $d = (float) $boundaries->batas4;

        $miu = [
            'murah' => $this->getMiuMurah($harga, $a, $b),
            'sedang' => $this->getMiuSedang($harga, $a, $b, $c, $d),
            'mahal' => $this->getMiuMahal($harga, $c, $d),
        ];

        // Simpan semua data ke database
        FuzzyInput::create([
            'harga' => $harga,
            'p1' => $a,
            'p2' => $b,
            'p3' => $c,
            'p4' => $d,
            'miu_murah' => $miu['murah'],
            'miu_sedang' => $miu['sedang'],
            'miu_mahal' => $miu['mahal'],
        ]);

    // Redirect ke halaman histori agar hasil langsung terlihat di tabel
    return redirect()->route('fuzzy.input')->with('success', 'Data berhasil dihitung dan disimpan!');
    }

    // Fungsi-fungsi keanggotaan tetap sama
    private function getMiuMurah($x, $a, $b)
    {
        if ($x <= $a) { return 1.0; } 
        elseif ($x > $a && $x <= $b) {
            if (($b - $a) == 0) return 1.0;
            return ($b - $x) / ($b - $a);
        } else { return 0.0; }
    }

    private function getMiuSedang($x, $a, $b, $c, $d)
    {
        if ($x >= $b && $x <= $c) { return 1.0; }
        elseif ($x > $a && $x < $b) {
            if (($b - $a) == 0) return 1.0;
            return ($x - $a) / ($b - $a);
        } elseif ($x > $c && $x < $d) {
            if (($d - $c) == 0) return 1.0;
            return ($d - $x) / ($d - $c);
        } else { return 0.0; }
    }

    private function getMiuMahal($x, $c, $d)
    {
        if ($x >= $d) { return 1.0; }
        elseif ($x > $c && $x < $d) {
            if (($d - $c) == 0) return 1.0;
            return ($x - $c) / ($d - $c);
        } else { return 0.0; }
    }
}