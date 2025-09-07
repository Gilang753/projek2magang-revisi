<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\FuzzyInput;
use App\Models\RatingHistory;
use App\Models\RasaHistory;
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
        // Ambil data derajat keanggotaan terbaru
        $lastHarga = FuzzyInput::latest()->first();
        $lastRating = RatingHistory::latest()->first();
        $lastRasa = RasaHistory::latest()->first();
        
        if (!$lastHarga || !$lastRating || !$lastRasa) {
            return redirect()->route('rules.index')->with('error', 'Data derajat keanggotaan belum tersedia. Silakan hitung terlebih dahulu di menu Fuzzy.');
        }
        
        $rules = Rule::all();
        $inferenceResults = [];
        
        foreach ($rules as $rule) {
            // Nilai derajat keanggotaan untuk harga
            switch ($rule->harga_fuzzy) {
                case 'Murah': $miuHarga = $lastHarga->miu_murah; break;
                case 'Sedang': $miuHarga = $lastHarga->miu_sedang; break;
                case 'Mahal': $miuHarga = $lastHarga->miu_mahal; break;
                default: $miuHarga = 0;
            }
            
            // Nilai derajat keanggotaan untuk rating
            switch ($rule->rating_fuzzy) {
                case 'Rendah': $miuRating = $lastRating->miu_rendah; break;
                case 'Sedang': $miuRating = $lastRating->miu_sedang; break;
                case 'Tinggi': $miuRating = $lastRating->miu_tinggi; break;
                default: $miuRating = 0;
            }
            
            // Nilai derajat keanggotaan untuk rasa
            switch ($rule->rasa_fuzzy) {
                case 'Asam': $miuRasa = $lastRasa->miu_asam; break;
                case 'Manis': $miuRasa = $lastRasa->miu_manis; break;
                case 'Pedas': $miuRasa = $lastRasa->miu_pedas; break;
                case 'Asin': $miuRasa = $lastRasa->miu_asin; break;
                default: $miuRasa = 0;
            }
            
            // Nilai alpha adalah minimum dari ketiga derajat keanggotaan
            $alpha = min($miuHarga, $miuRating, $miuRasa);
            
            $inferenceResults[] = [
                'rule' => $rule,
                'miu_harga' => $miuHarga,
                'miu_rating' => $miuRating,
                'miu_rasa' => $miuRasa,
                'alpha' => $alpha,
                'rekomendasi' => $rule->rekomendasi
            ];
        }
        
        // Urutkan hasil berdasarkan alpha tertinggi
        usort($inferenceResults, function($a, $b) {
            return $b['alpha'] <=> $a['alpha'];
        });
        
        $rules = Rule::all();
        
        return view('rules.index', compact('rules', 'inferenceResults', 'lastHarga', 'lastRating', 'lastRasa'));
    }
}