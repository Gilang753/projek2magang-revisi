<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\Menu;
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
}