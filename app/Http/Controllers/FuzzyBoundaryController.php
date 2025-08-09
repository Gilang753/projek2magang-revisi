<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyInput;
use App\Models\FuzzyBoundary;

class FuzzyBoundaryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'batas1' => 'required|numeric',
            'batas2' => 'required|numeric',
            'batas3' => 'required|numeric',
            'batas4' => 'required|numeric',
        ]);

        // Simpan boundaries baru, atau update jika sudah ada (misal hanya 1 baris)
        $boundary = FuzzyBoundary::first();
        if ($boundary) {
            $boundary->update($request->only(['batas1','batas2','batas3','batas4']));
        } else {
            FuzzyBoundary::create($request->only(['batas1','batas2','batas3','batas4']));
        }
        return back()->with('success', 'Batas berhasil disimpan!');
    }
}
