<?php

namespace App\Http\Controllers;

use App\Models\RasaBoundary;
use App\Models\RasaHistory;
use Illuminate\Http\Request;

class RasaFuzzyController extends Controller
{
    /**
     * Display the fuzzy rasa settings and results.
     */
    public function index()
    {
        $boundaries = RasaBoundary::firstOrNew();
        
        // Fetch the most recent result from the database
        $lastResult = RasaHistory::latest()->first();
        $results = $lastResult ? [
            'rasa' => $lastResult->rasa,
            'miu_asam' => $lastResult->miu_asam,
            'miu_manis' => $lastResult->miu_manis,
            'miu_pedas' => $lastResult->miu_pedas,
            'miu_asin' => $lastResult->miu_asin,
        ] : null;
        
        return view('fuzzy.inputRasa', compact('boundaries', 'results'));
    }

    /**
     * Calculate the fuzzy membership degrees for a given taste value.
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'rasa' => 'required|numeric|min:0|max:100'
        ]);

        $boundaries = RasaBoundary::firstOrNew();
        $rasa = $request->rasa;
        
        // Calculate miu_asam (Sour) - Left Shoulder Curve
        $miu_asam = 0;
        if ($rasa <= $boundaries->batas_asam_puncak) {
            $miu_asam = 1;
        } elseif ($rasa > $boundaries->batas_asam_puncak && $rasa < $boundaries->batas_asam_akhir) {
            $miu_asam = ($boundaries->batas_asam_akhir - $rasa) / 
                        ($boundaries->batas_asam_akhir - $boundaries->batas_asam_puncak);
        }

        // Calculate miu_manis (Sweet) - Triangular Curve
        $miu_manis = 0;
        if ($rasa > $boundaries->batas_manis_awal && $rasa <= $boundaries->batas_manis_puncak) {
            $miu_manis = ($rasa - $boundaries->batas_manis_awal) / 
                         ($boundaries->batas_manis_puncak - $boundaries->batas_manis_awal);
        } elseif ($rasa > $boundaries->batas_manis_puncak && $rasa < $boundaries->batas_manis_akhir) {
            $miu_manis = ($boundaries->batas_manis_akhir - $rasa) / 
                         ($boundaries->batas_manis_akhir - $boundaries->batas_manis_puncak);
        } elseif ($rasa == $boundaries->batas_manis_puncak) {
            $miu_manis = 1;
        }

        // Calculate miu_pedas (Spicy) - Triangular Curve
        $miu_pedas = 0;
        if ($rasa > $boundaries->batas_pedas_awal && $rasa <= $boundaries->batas_pedas_puncak) {
            $miu_pedas = ($rasa - $boundaries->batas_pedas_awal) / 
                         ($boundaries->batas_pedas_puncak - $boundaries->batas_pedas_awal);
        } elseif ($rasa > $boundaries->batas_pedas_puncak && $rasa < $boundaries->batas_pedas_akhir) {
            $miu_pedas = ($boundaries->batas_pedas_akhir - $rasa) / 
                         ($boundaries->batas_pedas_akhir - $boundaries->batas_pedas_puncak);
        } elseif ($rasa == $boundaries->batas_pedas_puncak) {
            $miu_pedas = 1;
        }
        
        // Calculate miu_asin (Salty) - Right Shoulder Curve
        $miu_asin = 0;
        if ($rasa > $boundaries->batas_asin_awal && $rasa < $boundaries->batas_asin_puncak) {
            $miu_asin = ($rasa - $boundaries->batas_asin_awal) / 
                        ($boundaries->batas_asin_puncak - $boundaries->batas_asin_awal);
        } elseif ($rasa >= $boundaries->batas_asin_puncak) {
            $miu_asin = 1;
        }
        
        // Clear previous history and save new calculation
        RasaHistory::truncate();
        RasaHistory::create([
            'rasa' => $rasa,
            'miu_asam' => $miu_asam,
            'miu_manis' => $miu_manis,
            'miu_pedas' => $miu_pedas,
            'miu_asin' => $miu_asin,
        ]);

        return redirect()->route('fuzzy.inputRasa')->with('success', 'Perhitungan berhasil disimpan!');
    }

    /**
     * Store/update fuzzy rasa boundaries from form input.
     */
    public function storeBoundaries(Request $request)
    {
        $request->validate([
            'batas_asam_puncak' => 'required|numeric|min:0|max:100',
            'batas_asam_akhir' => 'required|numeric|min:0|max:100',
            'batas_manis_awal' => 'required|numeric|min:0|max:100',
            'batas_manis_puncak' => 'required|numeric|min:0|max:100',
            'batas_manis_akhir' => 'required|numeric|min:0|max:100',
            'batas_pedas_awal' => 'required|numeric|min:0|max:100',
            'batas_pedas_puncak' => 'required|numeric|min:0|max:100',
            'batas_pedas_akhir' => 'required|numeric|min:0|max:100',
            'batas_asin_awal' => 'required|numeric|min:0|max:100',
            'batas_asin_puncak' => 'required|numeric|min:0|max:100',
        ]);

        $boundaries = RasaBoundary::firstOrNew();
        $boundaries->batas_asam_puncak = $request->batas_asam_puncak;
        $boundaries->batas_asam_akhir = $request->batas_asam_akhir;
        $boundaries->batas_manis_awal = $request->batas_manis_awal;
        $boundaries->batas_manis_puncak = $request->batas_manis_puncak;
        $boundaries->batas_manis_akhir = $request->batas_manis_akhir;
        $boundaries->batas_pedas_awal = $request->batas_pedas_awal;
        $boundaries->batas_pedas_puncak = $request->batas_pedas_puncak;
        $boundaries->batas_pedas_akhir = $request->batas_pedas_akhir;
        $boundaries->batas_asin_awal = $request->batas_asin_awal;
        $boundaries->batas_asin_puncak = $request->batas_asin_puncak;
        $boundaries->save();

        return redirect()->route('fuzzy.inputRasa')->with('success', 'Batas fuzzy rasa berhasil disimpan!');
    }
}