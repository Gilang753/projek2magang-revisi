<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\FuzzyInput;
use App\Models\RatingHistory;
use App\Models\RasaHistory;
use App\Models\RuleExecution;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index()
    {
        $rules = Rule::all();
        
        return view('rules.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'harga_fuzzy' => 'required',
            'rating_fuzzy' => 'required',
            'rasa_fuzzy' => 'required',
            'rekomendasi' => 'required|in:Rekomendasi,Tidak Rekomendasi',
        ]);
        
        // Cek apakah aturan duplikat sudah ada
        $existingRule = Rule::where('harga_fuzzy', $request->harga_fuzzy)
                            ->where('rating_fuzzy', $request->rating_fuzzy)
                            ->where('rasa_fuzzy', $request->rasa_fuzzy)
                            ->where('rekomendasi', $request->rekomendasi)
                            ->first();

        if ($existingRule) {
            return redirect()->route('rules.index')->with('error', 'Aturan dengan kombinasi yang sama sudah ada!');
        }

        Rule::create([
            'harga_fuzzy' => $request->harga_fuzzy,
            'rating_fuzzy' => $request->rating_fuzzy,
            'rasa_fuzzy' => $request->rasa_fuzzy,
            'rekomendasi' => $request->rekomendasi,
        ]);

        return redirect()->route('rules.index')->with('sukses', 'Aturan baru berhasil ditambahkan!');
    }
    
    public function edit(Rule $rule)
    {
        return view('rules.edit', compact('rule'));
    }

    public function update(Request $request, Rule $rule)
    {
        $request->validate([
            'harga_fuzzy' => 'required',
            'rating_fuzzy' => 'required',
            'rasa_fuzzy' => 'required',
            'rekomendasi' => 'required|in:Rekomendasi,Tidak Rekomendasi',
        ]);
        
        // Periksa duplikasi
        $existingRule = Rule::where('harga_fuzzy', $request->harga_fuzzy)
                        ->where('rating_fuzzy', $request->rating_fuzzy)
                        ->where('rasa_fuzzy', $request->rasa_fuzzy)
                        ->where('rekomendasi', $request->rekomendasi)
                        ->where('id', '!=', $rule->id)
                        ->first();

        if ($existingRule) {
            return redirect()->route('rules.index')->with('error', 'Aturan dengan kombinasi yang sama sudah ada!');
        }

        $rule->update([
            'harga_fuzzy' => $request->harga_fuzzy,
            'rating_fuzzy' => $request->rating_fuzzy,
            'rasa_fuzzy' => $request->rasa_fuzzy,
            'rekomendasi' => $request->rekomendasi,
        ]);

        return redirect()->route('rules.index')->with('sukses', 'Aturan berhasil diperbarui!');
    }

    public function destroy(Rule $rule)
    {
        $rule->delete();
        return redirect()->route('rules.index')->with('sukses', 'Aturan berhasil dihapus.');
    }

    public function execute()
    {
        // Hapus semua data lama di tabel rule_executions agar hasil selalu terbaru
        RuleExecution::truncate();
        // Ambil semua menu
        $menus = \App\Models\Menu::all();
        $rules = Rule::all();
        $inferenceResults = [];

        // Ambil batas fuzzy output dari rekomendasi_boundaries
        $boundaries = \App\Models\RekomendasiBoundary::first();
        if (!$boundaries) {
            return redirect()->route('rules.index')->with('error', 'Batas fuzzy rekomendasi belum tersedia. Silakan input batas terlebih dahulu.');
        }

        // Ambil nilai batas
        $T1 = $boundaries->batas_tidak_puncak;
        $T2 = $boundaries->batas_tidak_akhir;
        $R1 = $boundaries->batas_rekomendasi_awal;
        $R2 = $boundaries->batas_rekomendasi_puncak;

        if ($menus->isEmpty()) {
            return redirect()->route('rules.index')->with('error', 'Data menu belum tersedia. Silakan input menu terlebih dahulu.');
        }

        // Simpan semua rule execution dan hitung defuzzifikasi per menu
        $ruleExecutionsByMenu = [];
        foreach ($menus as $menu) {
            $sumAlphaZ = 0;
            $sumAlpha = 0;
            foreach ($rules as $rule) {
                // Nilai derajat keanggotaan untuk harga
                switch ($rule->harga_fuzzy) {
                    case 'Murah': $miuHarga = $menu->miu_harga_murah; break;
                    case 'Sedang': $miuHarga = $menu->miu_harga_sedang; break;
                    case 'Mahal': $miuHarga = $menu->miu_harga_mahal; break;
                    default: $miuHarga = 0;
                }
                // Nilai derajat keanggotaan untuk rating
                switch ($rule->rating_fuzzy) {
                    case 'Rendah': $miuRating = $menu->miu_rating_rendah; break;
                    case 'Sedang': $miuRating = $menu->miu_rating_sedang; break;
                    case 'Tinggi': $miuRating = $menu->miu_rating_tinggi; break;
                    default: $miuRating = 0;
                }
                // Nilai derajat keanggotaan untuk rasa
                switch ($rule->rasa_fuzzy) {
                    case 'Asam': $miuRasa = $menu->miu_rasa_asam; break;
                    case 'Manis': $miuRasa = $menu->miu_rasa_manis; break;
                    case 'Pedas': $miuRasa = $menu->miu_rasa_pedas; break;
                    case 'Asin': $miuRasa = $menu->miu_rasa_asin; break;
                    default: $miuRasa = 0;
                }
                // Nilai alpha adalah minimum dari ketiga derajat keanggotaan
                $alpha = min($miuHarga, $miuRating, $miuRasa);

                // Hitung z_crisp sesuai rumus Tsukamoto
                if ($rule->rekomendasi === 'Rekomendasi') {
                    // Output monoton naik
                    $z_crisp = $alpha * ($R2 - $R1) + $R1;
                } else {
                    // Output monoton turun
                    $z_crisp = $T2 - $alpha * ($T2 - $T1);
                }

                $sumAlphaZ += $alpha * $z_crisp;
                $sumAlpha += $alpha;

                $ruleExecutionsByMenu[$menu->id][] = [
                    'menu_id' => $menu->id,
                    'rule_id' => $rule->id,
                    'miu_harga' => $miuHarga,
                    'miu_rating' => $miuRating,
                    'miu_rasa' => $miuRasa,
                    'alpha_predikat' => $alpha,
                    'z_crisp' => $z_crisp,
                ];
            }
            // Hitung defuzzifikasi (z_admin) untuk menu ini
            $z_admin = $sumAlpha > 0 ? $sumAlphaZ / $sumAlpha : 0;
            // Simpan ke database
            if (isset($ruleExecutionsByMenu[$menu->id])) {
                foreach ($ruleExecutionsByMenu[$menu->id] as $exec) {
                    $exec['z_admin'] = $z_admin;
                    \App\Models\RuleExecution::create($exec);
                    $inferenceResults[] = array_merge($exec, [
                        'menu' => $menu,
                        'rule' => $rules->where('id', $exec['rule_id'])->first(),
                        'rekomendasi' => $rules->where('id', $exec['rule_id'])->first()->rekomendasi ?? null,
                        // pastikan key alpha_predikat tersedia, hapus key alpha
                    ]);
                }
            }
        }

        // Hasil eksekusi akan mengikuti urutan list rule di database, tidak diurutkan berdasarkan alpha.

        return view('rules.index', compact('rules', 'inferenceResults', 'menus'));
    }
}