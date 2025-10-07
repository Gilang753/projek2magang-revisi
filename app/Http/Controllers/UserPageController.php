<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuzzyBoundary;
use App\Models\FuzzyInput;
use App\Models\RatingBoundary;
use App\Models\RatingHistory;
use App\Models\RasaBoundary;
use App\Models\RasaHistory;
use App\Models\Rule;
use App\Models\Menu;
use App\Models\InferenceResult;

class UserPageController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('user.index', compact('menus'));
    }

    public function showMenuDetail($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Ambil menu serupa berdasarkan rasa yang sama
        $similarMenus = Menu::where('cita_rasa', $menu->cita_rasa)
                            ->where('id', '!=', $menu->id)
                            ->limit(4)
                            ->get();
        
        return view('user.show', compact('menu', 'similarMenus'));
    }
    

    public function executeRule(Request $request)
    {
        $request->validate([
            'harga' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:100',
            'rasa' => 'required|numeric|min:0|max:100',
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

        // Proses fuzzy rasa
        $rasa = (float) $request->input('rasa');
        $rasaBoundaries = RasaBoundary::first();
        if (!$rasaBoundaries) {
            return back()->with('error', 'Batas fuzzy rasa belum diatur.');
        }
        
        $miu_asam = $miu_manis = $miu_pedas = $miu_asin = 0;
        if ($rasa <= $rasaBoundaries->batas_asam_puncak) {
            $miu_asam = 1;
        } elseif ($rasa > $rasaBoundaries->batas_asam_puncak && $rasa < $rasaBoundaries->batas_asam_akhir) {
            $miu_asam = ($rasaBoundaries->batas_asam_akhir - $rasa) / ($rasaBoundaries->batas_asam_akhir - $rasaBoundaries->batas_asam_puncak);
        }
        if ($rasa > $rasaBoundaries->batas_manis_awal && $rasa <= $rasaBoundaries->batas_manis_puncak) {
            $miu_manis = ($rasa - $rasaBoundaries->batas_manis_awal) / ($rasaBoundaries->batas_manis_puncak - $rasaBoundaries->batas_manis_awal);
        } elseif ($rasa > $rasaBoundaries->batas_manis_puncak && $rasa < $rasaBoundaries->batas_manis_akhir) {
            $miu_manis = ($rasaBoundaries->batas_manis_akhir - $rasa) / ($rasaBoundaries->batas_manis_akhir - $rasaBoundaries->batas_manis_puncak);
        } elseif ($rasa == $rasaBoundaries->batas_manis_puncak) {
            $miu_manis = 1;
        }
        if ($rasa > $rasaBoundaries->batas_pedas_awal && $rasa <= $rasaBoundaries->batas_pedas_puncak) {
            $miu_pedas = ($rasa - $rasaBoundaries->batas_pedas_awal) / ($rasaBoundaries->batas_pedas_puncak - $rasaBoundaries->batas_pedas_awal);
        } elseif ($rasa > $rasaBoundaries->batas_pedas_puncak && $rasa < $rasaBoundaries->batas_pedas_akhir) {
            $miu_pedas = ($rasaBoundaries->batas_pedas_akhir - $rasa) / ($rasaBoundaries->batas_pedas_akhir - $rasaBoundaries->batas_pedas_puncak);
        } elseif ($rasa == $rasaBoundaries->batas_pedas_puncak) {
            $miu_pedas = 1;
        }
        if ($rasa > $rasaBoundaries->batas_asin_awal && $rasa < $rasaBoundaries->batas_asin_puncak) {
            $miu_asin = ($rasa - $rasaBoundaries->batas_asin_awal) / ($rasaBoundaries->batas_asin_puncak - $rasaBoundaries->batas_asin_awal);
        } elseif ($rasa >= $rasaBoundaries->batas_asin_puncak) {
            $miu_asin = 1;
        }
        $rasaHistory = RasaHistory::create([
            'rasa' => $rasa,
            'miu_asam' => $miu_asam,
            'miu_manis' => $miu_manis,
            'miu_pedas' => $miu_pedas,
            'miu_asin' => $miu_asin,
        ]);

        // Hapus semua data inference_results agar selalu fresh untuk setiap eksekusi user
        \App\Models\InferenceResult::truncate();

        $alphaArr = [];
        $zArr = [];
        $createdInferenceResults = [];
        
        // Eksekusi rule
        $rules = Rule::all();
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
            switch ($rule->rasa_fuzzy) {
                case 'Asam': $miuRasa = $rasaHistory->miu_asam; break;
                case 'Manis': $miuRasa = $rasaHistory->miu_manis; break;
                case 'Pedas': $miuRasa = $rasaHistory->miu_pedas; break;
                case 'Asin': $miuRasa = $rasaHistory->miu_asin; break;
                default: $miuRasa = 0;
            }
            $alpha = min($miuHarga, $miuRating, $miuRasa);
            $z_crisp = optional(\App\Models\RuleExecution::where('rule_id', $rule->id)->first())->z_crisp;
            $alphaArr[] = $alpha;
            $zArr[] = $z_crisp;
            $inferenceResults[] = [
                'rule' => $rule,
                'miu_harga' => $miuHarga,
                'miu_rating' => $miuRating,
                'miu_rasa' => $miuRasa,
                'alpha' => $alpha,
                'z_crisp' => $z_crisp,
                'rekomendasi' => $rule->rekomendasi
            ];
            $created = InferenceResult::create([
                'rule_id' => $rule->id,
                'menu_id' => null,
                'miu_harga' => $miuHarga,
                'miu_rating' => $miuRating,
                'miu_rasa' => $miuRasa,
                'alpha' => $alpha,
                'rekomendasi' => $rule->rekomendasi,
            ]);
            $createdInferenceResults[] = $created;
        }

        $sumAlphaZ = 0;
        $sumAlpha = 0;
        foreach ($alphaArr as $i => $alpha) {
            if ($zArr[$i] !== null) {
                $sumAlphaZ += $alpha * $zArr[$i];
                $sumAlpha += $alpha;
            }
        }
        $z_user = $sumAlpha > 0 ? $sumAlphaZ / $sumAlpha : 0;
        foreach ($createdInferenceResults as $created) {
            $created->z_user = $z_user;
            $created->save();
        }
        
        usort($inferenceResults, function($a, $b) {
            return $b['alpha'] <=> $a['alpha'];
        });

        // Filter menu dengan harga dalam rentang ±20% dari input user
        $maxHarga = $harga * 1.2;
        $minHarga = $harga * 0.8;
        
        // Tentukan cita rasa user berdasarkan input
        $citaRasaUser = null;
        if ($rasa > 0 && $rasa <= 25) {
            $citaRasaUser = 'asam';
        } elseif ($rasa > 25 && $rasa <= 50) {
            $citaRasaUser = 'manis';
        } elseif ($rasa > 51 && $rasa <= 75) {
            $citaRasaUser = 'pedas';
        }  elseif ($rasa > 76 && $rasa <= 100) {
            $citaRasaUser = 'asin';
        }

        // Filter menu dengan harga dalam rentang ±20% dan cita rasa sesuai input user
        $menus = Menu::whereBetween('harga_seporsi', [$minHarga, $maxHarga])
            ->where('cita_rasa', $citaRasaUser)
            ->get();

        $hargaInput = $harga;
        $ratingInput = $rating;
        $rasaInput = $rasa;

        // Cari hingga 10 menu dengan z_admin terdekat ke z_user
        $menuDiffs = [];
        foreach ($menus as $menu) {
            $z_admin = \App\Models\RuleExecution::where('menu_id', $menu->id)->value('z_admin');
            if ($z_admin !== null && $z_user !== null) {
                $diff = abs($z_user - $z_admin);
                $menuDiffs[] = [
                    'menu' => $menu,
                    'z_admin' => $z_admin,
                    'diff' => $diff
                ];
            }
        }
        usort($menuDiffs, function($a, $b) {
            return $a['diff'] <=> $b['diff'];
        });
        $recommendedMenus = array_slice($menuDiffs, 0, 10);

        return view('user.result', compact('inferenceResults', 'menus', 'hargaInput', 'ratingInput', 'rasaInput', 'recommendedMenus', 'z_user'));
    }
}