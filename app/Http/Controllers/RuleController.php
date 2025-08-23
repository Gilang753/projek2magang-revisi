<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Menu;
use App\Models\FuzzyInput;
use App\Models\RatingHistory;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    /**
     * Tampilkan halaman daftar aturan.
     */
    public function index()
    {
        $menus = Menu::all(); 
        $rules = Rule::with('menu')->get();
        
        return view('rules.index', compact('menus', 'rules'));
    }

    /**
     * Simpan aturan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'harga_fuzzy' => 'required',
            'rating_fuzzy' => 'required',
            'menu_id' => 'required|exists:tb_menu,id',
        ]);
        
        // Cek apakah aturan duplikat sudah ada di database
        $existingRule = Rule::where('harga_fuzzy', $request->harga_fuzzy)
                            ->where('rating_fuzzy', $request->rating_fuzzy)
                            ->where('menu_id', $request->menu_id)
                            ->first();

        if ($existingRule) {
            // Jika aturan duplikat ditemukan, kembalikan dengan pesan error
            return redirect()->route('rules.index')->with('error', 'Aturan dengan kombinasi yang sama sudah ada!');
        }

        // Jika tidak duplikat, buat aturan baru
        Rule::create([
            'harga_fuzzy' => $request->harga_fuzzy,
            'rating_fuzzy' => $request->rating_fuzzy,
            'menu_id' => $request->menu_id,
        ]);

        return redirect()->route('rules.index')->with('sukses', 'Aturan baru berhasil ditambahkan!');
    }
    
    public function edit(Rule $rule)
{
    $menus = Menu::all();
    return view('rules.edit', compact('rule', 'menus'));
}

public function update(Request $request, Rule $rule)
{
    $request->validate([
        'harga_fuzzy' => 'required',
        'rating_fuzzy' => 'required',
        'menu_id' => 'required|exists:tb_menu,id',
    ]);
    
    // Periksa duplikasi (kecuali untuk rule yang sedang diedit)
    $existingRule = Rule::where('harga_fuzzy', $request->harga_fuzzy)
                        ->where('rating_fuzzy', $request->rating_fuzzy)
                        ->where('menu_id', $request->menu_id)
                        ->where('id', '!=', $rule->id)
                        ->first();

    if ($existingRule) {
        return redirect()->route('rules.index')->with('error', 'Aturan dengan kombinasi yang sama sudah ada!');
    }

    $rule->update([
        'harga_fuzzy' => $request->harga_fuzzy,
        'rating_fuzzy' => $request->rating_fuzzy,
        'menu_id' => $request->menu_id,
    ]);

    return redirect()->route('rules.index')->with('sukses', 'Aturan berhasil diperbarui!');
}

    public function destroy(Rule $rule)
    {
        $rule->delete();
        
        return redirect()->route('rules.index')->with('sukses', 'Aturan berhasil dihapus.');
    }

    // RuleController.php - tambahkan method ini
public function execute()
{
    // Ambil data derajat keanggotaan terbaru
    $lastHarga = FuzzyInput::latest()->first();
    $lastRating = RatingHistory::latest()->first();
    
    // Jika tidak ada data, redirect dengan pesan error
    if (!$lastHarga || !$lastRating) {
        return redirect()->route('rules.index')->with('error', 'Data derajat keanggotaan belum tersedia. Silakan hitung terlebih dahulu di menu Fuzzy.');
    }
    
    // Ambil semua aturan
    $rules = Rule::with('menu')->get();
    
    // Simpan hasil inferensi
    $inferenceResults = [];
    
    foreach ($rules as $rule) {
        // Tentukan nilai alpha berdasarkan kombinasi harga dan rating
        $alpha = 0;
        
        // Logika inferensi: ambil nilai minimum dari kedua derajat keanggotaan
        switch ($rule->harga_fuzzy) {
            case 'Murah':
                $miuHarga = $lastHarga->miu_murah;
                break;
            case 'Sedang':
                $miuHarga = $lastHarga->miu_sedang;
                break;
            case 'Mahal':
                $miuHarga = $lastHarga->miu_mahal;
                break;
            default:
                $miuHarga = 0;
        }
        
        switch ($rule->rating_fuzzy) {
            case 'Rendah':
                $miuRating = $lastRating->miu_rendah;
                break;
            case 'Sedang':
                $miuRating = $lastRating->miu_sedang;
                break;
            case 'Tinggi':
                $miuRating = $lastRating->miu_tinggi;
                break;
            default:
                $miuRating = 0;
        }
        
        // Nilai alpha adalah minimum dari kedua derajat keanggotaan
        $alpha = min($miuHarga, $miuRating);
        
        // Simpan hasil inferensi
        $inferenceResults[] = [
            'rule' => $rule,
            'miu_harga' => $miuHarga,
            'miu_rating' => $miuRating,
            'alpha' => $alpha,
            'menu' => $rule->menu
        ];
    }
    
    // Urutkan hasil berdasarkan alpha tertinggi (descending)
    usort($inferenceResults, function($a, $b) {
        return $b['alpha'] <=> $a['alpha'];
    });
    
    // Ambil data menu untuk dropdown
    $menus = Menu::all();
    $rules = Rule::with('menu')->get();
    
    return view('rules.index', compact('menus', 'rules', 'inferenceResults', 'lastHarga', 'lastRating'));
}
}