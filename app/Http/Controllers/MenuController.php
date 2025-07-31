<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::orderBy('nama')->get();
        $citaRasaOptions = Menu::getCitaRasaOptions();
        $ratingOptions = Menu::getRatingOptions();
        
        return view('menus.index', compact('menus', 'citaRasaOptions', 'ratingOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $citaRasaOptions = Menu::getCitaRasaOptions();
        $ratingOptions = Menu::getRatingOptions();
        
        return view('menus.create', compact('citaRasaOptions', 'ratingOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'deskripsi' => 'nullable|string|max:500', // Added validation for deskripsi
            'harga_seporsi' => 'required|numeric|min:0',
            'cita_rasa' => 'required|in:'.implode(',', array_keys(Menu::getCitaRasaOptions())),
            'rating' => 'required|integer|between:1,5',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('menu-images', 'public');
        }

        Menu::create($validated);

        return redirect()->route('menus.index')
                         ->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return view('menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $citaRasaOptions = Menu::getCitaRasaOptions();
        $ratingOptions = Menu::getRatingOptions();
        
        return view('menus.edit', compact('menu', 'citaRasaOptions', 'ratingOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'deskripsi' => 'nullable|string|max:500', // Added validation for deskripsi
            'harga_seporsi' => 'required|numeric|min:0',
            'cita_rasa' => 'required|in:'.implode(',', array_keys(Menu::getCitaRasaOptions())),
            'rating' => 'required|integer|between:1,5',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('menu-images', 'public');
        }

        $menu->update($validated);

        return redirect()->route('menus.index')
                         ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Hapus gambar terkait jika ada
        if ($menu->gambar) {
            Storage::disk('public')->delete($menu->gambar);
        }

        $menu->delete();

        return redirect()->route('menus.index')
                         ->with('success', 'Menu berhasil dihapus!');
    }
}