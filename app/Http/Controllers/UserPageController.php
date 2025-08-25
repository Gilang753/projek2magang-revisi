<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyBoundary;
use App\Models\FuzzyInput;
use App\Models\RatingBoundary;
use App\Models\RatingHistory;
use App\Models\Rule;
use App\Models\Menu;

class UserPageController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('user.index', compact('menus'));
    }

    public function executeRule(Request $request)
    {
        $request->validate([
            'harga' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:100',
        ]);

        // Proses fuzzy harga
        $harga = (float) $request->input('harga');
        $boundaries = FuzzyBoundary::first();
        if (!$boundaries) {
            return back()->with('error', 'Batas fuzzy harga belum diatur.');
        }
        // Hitung miu harga
        $miu_murah = $miu_sedang = $miu_mahal = 0;
        if ($harga <= $boundaries->batas_murah_puncak) {
            $miu_murah = 1;
        } elseif ($harga > $boundaries->batas_murah_puncak && $harga < $boundaries->batas_murah_akhir) {
            $miu_murah = ($boundaries->batas_murah_akhir - $harga) / ($boundaries->batas_murah_akhir - $boundaries->batas_murah_puncak);
        }
        if ($harga >= $boundaries->batas_sedang_awal && $harga <= $boundaries->batas_sedang_puncak) {
            $miu_sedang = ($harga - $boundaries->batas_sedang_awal) / ($boundaries->batas_sedang_puncak - $boundaries->batas_sedang_awal);
        } elseif ($harga > $boundaries->batas_sedang_puncak && $harga < $boundaries->batas_sedang_akhir) {
            $miu_sedang = ($boundaries->batas_sedang_akhir - $harga) / ($boundaries->batas_sedang_akhir - $boundaries->batas_sedang_puncak);
        }
        if ($harga >= $boundaries->batas_mahal_awal && $harga <= $boundaries->batas_mahal_puncak) {
            $miu_mahal = ($harga - $boundaries->batas_mahal_awal) / ($boundaries->batas_mahal_puncak - $boundaries->batas_mahal_awal);
        } elseif ($harga > $boundaries->batas_mahal_puncak) {
            $miu_mahal = 1;
        }
        $fuzzyInput = FuzzyInput::create([
            'harga' => $harga,
            'p1' => 0,
            'p2' => 0,
            'p3' => 0,
            'p4' => 0,
            'p5' => 0,
            'miu_murah' => $miu_murah,
            'miu_sedang' => $miu_sedang,
            'miu_mahal' => $miu_mahal,
        ]);

        // Proses fuzzy rating
        $rating = (float) $request->input('rating');
        $ratingBoundaries = RatingBoundary::first();
        if (!$ratingBoundaries) {
            return back()->with('error', 'Batas fuzzy rating belum diatur.');
        }
        $miu_rendah = $miu_sedang = $miu_tinggi = 0;
        if ($rating <= $ratingBoundaries->batas_rendah_puncak) {
            $miu_rendah = 1;
        } elseif ($rating > $ratingBoundaries->batas_rendah_puncak && $rating < $ratingBoundaries->batas_rendah_akhir) {
            $miu_rendah = ($ratingBoundaries->batas_rendah_akhir - $rating) / ($ratingBoundaries->batas_rendah_akhir - $ratingBoundaries->batas_rendah_puncak);
        }
        if ($rating >= $ratingBoundaries->batas_sedang_awal && $rating <= $ratingBoundaries->batas_sedang_puncak) {
            $miu_sedang = ($rating - $ratingBoundaries->batas_sedang_awal) / ($ratingBoundaries->batas_sedang_puncak - $ratingBoundaries->batas_sedang_awal);
        } elseif ($rating > $ratingBoundaries->batas_sedang_puncak && $rating < $ratingBoundaries->batas_sedang_akhir) {
            $miu_sedang = ($ratingBoundaries->batas_sedang_akhir - $rating) / ($ratingBoundaries->batas_sedang_akhir - $ratingBoundaries->batas_sedang_puncak);
        }
        if ($rating >= $ratingBoundaries->batas_tinggi_awal && $rating <= $ratingBoundaries->batas_tinggi_puncak) {
            $miu_tinggi = ($rating - $ratingBoundaries->batas_tinggi_awal) / ($ratingBoundaries->batas_tinggi_puncak - $ratingBoundaries->batas_tinggi_awal);
        } elseif ($rating > $ratingBoundaries->batas_tinggi_puncak) {
            $miu_tinggi = 1;
        }
        $ratingHistory = RatingHistory::create([
            'rating' => $rating,
            'p1' => 0,
            'p2' => 0,
            'p3' => 0,
            'p4' => 0,
            'p5' => 0,
            'miu_rendah' => $miu_rendah,
            'miu_sedang' => $miu_sedang,
            'miu_tinggi' => $miu_tinggi,
        ]);

        // Eksekusi rule
        $rules = Rule::with('menu')->get();
        $inferenceResults = [];
        foreach ($rules as $rule) {
            switch ($rule->harga_fuzzy) {
                case 'Murah': $miuHarga = $fuzzyInput->miu_murah; break;
                case 'Sedang': $miuHarga = $fuzzyInput->miu_sedang; break;
                case 'Mahal': $miuHarga = $fuzzyInput->miu_mahal; break;
                default: $miuHarga = 0;
            }
            switch ($rule->rating_fuzzy) {
                case 'Rendah': $miuRating = $ratingHistory->miu_rendah; break;
                case 'Sedang': $miuRating = $ratingHistory->miu_sedang; break;
                case 'Tinggi': $miuRating = $ratingHistory->miu_tinggi; break;
                default: $miuRating = 0;
            }
            $alpha = min($miuHarga, $miuRating);
            $inferenceResults[] = [
                'rule' => $rule,
                'miu_harga' => $miuHarga,
                'miu_rating' => $miuRating,
                'alpha' => $alpha,
                'menu' => $rule->menu
            ];
        }
        usort($inferenceResults, function($a, $b) {
            return $b['alpha'] <=> $a['alpha'];
        });

    $menus = Menu::all();
    return view('user.index', compact('inferenceResults', 'menus'));
    }
}
