<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\FuzzyBoundary;
use App\Models\RatingBoundary;
use App\Models\RasaBoundary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::orderBy('nama')->get();
        $ratingOptions = Menu::getRatingOptions();
        return view('menus.index', compact('menus', 'ratingOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ratingOptions = Menu::getRatingOptions();
        return view('menus.create', compact('ratingOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateMenu($request);
        
        try {
            $validated = $this->calculateFuzzyValues($validated);
            
            // Handle file upload
            if ($request->hasFile('gambar')) {
                $validated['gambar'] = $request->file('gambar')->store('menu-images', 'public');
            }

            Menu::create($validated);

            return redirect()->route('menus.index')
                             ->with('success', 'Menu berhasil ditambahkan!');
                             
        } catch (\Exception $e) {
            Log::error('Error creating menu: ' . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Terjadi kesalahan saat menambahkan menu: ' . $e->getMessage());
        }
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
        $ratingOptions = Menu::getRatingOptions();
        return view('menus.edit', compact('menu', 'ratingOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $this->validateMenu($request);
        
        try {
            $validated = $this->calculateFuzzyValues($validated);

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
                             
        } catch (\Exception $e) {
            Log::error('Error updating menu: ' . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Terjadi kesalahan saat memperbarui menu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        try {
            // Hapus gambar terkait jika ada
            if ($menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }

            $menu->delete();

            return redirect()->route('menus.index')
                             ->with('success', 'Menu berhasil dihapus!');
                             
        } catch (\Exception $e) {
            Log::error('Error deleting menu: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Terjadi kesalahan saat menghapus menu: ' . $e->getMessage());
        }
    }

    /**
     * Validate menu data
     */
    private function validateMenu(Request $request)
    {
        return $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'harga_seporsi' => 'required|numeric|min:0',
            'nilai_rasa' => 'required|numeric|min:0|max:100',
            'nilai_rating' => 'required|numeric|min:0|max:100',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nama.required' => 'Nama menu wajib diisi',
            'nama.max' => 'Nama menu maksimal 100 karakter',
            'harga_seporsi.required' => 'Harga wajib diisi',
            'harga_seporsi.min' => 'Harga tidak boleh negatif',
            'nilai_rasa.required' => 'Nilai rasa wajib diisi',
            'nilai_rasa.min' => 'Nilai rasa minimal 0',
            'nilai_rasa.max' => 'Nilai rasa maksimal 100',
            'nilai_rating.required' => 'Nilai rating wajib diisi',
            'nilai_rating.min' => 'Nilai rating minimal 0',
            'nilai_rating.max' => 'Nilai rating maksimal 100',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Gambar harus berformat jpeg, png, jpg, atau gif',
            'gambar.max' => 'Ukuran gambar maksimal 2MB'
        ]);
    }

    /**
     * Calculate all fuzzy values
     */
    private function calculateFuzzyValues(array $validated)
    {
        // Mapping nilai ke cita rasa dan rating
        $validated['cita_rasa'] = Menu::mapNilaiToRasa($validated['nilai_rasa']);
        $validated['rating'] = Menu::mapNilaiToRating($validated['nilai_rating']);

        // Calculate fuzzy values
        $validated = $this->calculateFuzzyHarga($validated);
        $validated = $this->calculateFuzzyRating($validated);
        $validated = $this->calculateFuzzyRasa($validated);

        return $validated;
    }

    /**
     * Calculate fuzzy harga values
     */
    private function calculateFuzzyHarga(array $validated)
    {
        $harga = $validated['harga_seporsi'];
        $hargaBoundary = FuzzyBoundary::first();

        if (!$hargaBoundary) {
            throw new \Exception('Data boundary harga tidak ditemukan');
        }

        $validated['miu_harga_murah'] = $validated['miu_harga_sedang'] = $validated['miu_harga_mahal'] = 0;

        // Fuzzy logic for harga
        if ($harga <= $hargaBoundary->batas_murah_puncak) {
            $validated['miu_harga_murah'] = 1;
        } elseif ($harga > $hargaBoundary->batas_murah_puncak && $harga < $hargaBoundary->batas_murah_akhir) {
            $validated['miu_harga_murah'] = ($hargaBoundary->batas_murah_akhir - $harga) / ($hargaBoundary->batas_murah_akhir - $hargaBoundary->batas_murah_puncak);
        }

        if ($harga >= $hargaBoundary->batas_sedang_awal && $harga <= $hargaBoundary->batas_sedang_puncak) {
            $validated['miu_harga_sedang'] = ($harga - $hargaBoundary->batas_sedang_awal) / ($hargaBoundary->batas_sedang_puncak - $hargaBoundary->batas_sedang_awal);
        } elseif ($harga > $hargaBoundary->batas_sedang_puncak && $harga < $hargaBoundary->batas_sedang_akhir) {
            $validated['miu_harga_sedang'] = ($hargaBoundary->batas_sedang_akhir - $harga) / ($hargaBoundary->batas_sedang_akhir - $hargaBoundary->batas_sedang_puncak);
        }

        if ($harga >= $hargaBoundary->batas_mahal_awal && $harga <= $hargaBoundary->batas_mahal_puncak) {
            $validated['miu_harga_mahal'] = ($harga - $hargaBoundary->batas_mahal_awal) / ($hargaBoundary->batas_mahal_puncak - $hargaBoundary->batas_mahal_awal);
        } elseif ($harga > $hargaBoundary->batas_mahal_puncak) {
            $validated['miu_harga_mahal'] = 1;
        }

        return $validated;
    }

    /**
     * Calculate fuzzy rating values
     */
    private function calculateFuzzyRating(array $validated)
    {
        $rating = $validated['nilai_rating'];
        $ratingBoundary = RatingBoundary::first();

        if (!$ratingBoundary) {
            throw new \Exception('Data boundary rating tidak ditemukan');
        }

        $validated['miu_rating_rendah'] = $validated['miu_rating_sedang'] = $validated['miu_rating_tinggi'] = 0;

        // Fuzzy logic for rating
        if ($rating <= $ratingBoundary->batas_rendah_puncak) {
            $validated['miu_rating_rendah'] = 1;
        } elseif ($rating > $ratingBoundary->batas_rendah_puncak && $rating < $ratingBoundary->batas_rendah_akhir) {
            $validated['miu_rating_rendah'] = ($ratingBoundary->batas_rendah_akhir - $rating) / ($ratingBoundary->batas_rendah_akhir - $ratingBoundary->batas_rendah_puncak);
        }

        if ($rating >= $ratingBoundary->batas_sedang_awal && $rating <= $ratingBoundary->batas_sedang_puncak) {
            $validated['miu_rating_sedang'] = ($rating - $ratingBoundary->batas_sedang_awal) / ($ratingBoundary->batas_sedang_puncak - $ratingBoundary->batas_sedang_awal);
        } elseif ($rating > $ratingBoundary->batas_sedang_puncak && $rating < $ratingBoundary->batas_sedang_akhir) {
            $validated['miu_rating_sedang'] = ($ratingBoundary->batas_sedang_akhir - $rating) / ($ratingBoundary->batas_sedang_akhir - $ratingBoundary->batas_sedang_puncak);
        }

        if ($rating >= $ratingBoundary->batas_tinggi_awal && $rating <= $ratingBoundary->batas_tinggi_puncak) {
            $validated['miu_rating_tinggi'] = ($rating - $ratingBoundary->batas_tinggi_awal) / ($ratingBoundary->batas_tinggi_puncak - $ratingBoundary->batas_tinggi_awal);
        } elseif ($rating > $ratingBoundary->batas_tinggi_puncak) {
            $validated['miu_rating_tinggi'] = 1;
        }

        return $validated;
    }

    /**
     * Calculate fuzzy rasa values
     */
    private function calculateFuzzyRasa(array $validated)
    {
        $rasa = $validated['nilai_rasa'];
        $rasaBoundary = RasaBoundary::first();

        if (!$rasaBoundary) {
            throw new \Exception('Data boundary rasa tidak ditemukan');
        }

        $validated['miu_rasa_asam'] = $validated['miu_rasa_manis'] = $validated['miu_rasa_pedas'] = $validated['miu_rasa_asin'] = 0;

        // Fuzzy logic for rasa
        if ($rasa <= $rasaBoundary->batas_asam_puncak) {
            $validated['miu_rasa_asam'] = 1;
        } elseif ($rasa > $rasaBoundary->batas_asam_puncak && $rasa < $rasaBoundary->batas_asam_akhir) {
            $validated['miu_rasa_asam'] = ($rasaBoundary->batas_asam_akhir - $rasa) / ($rasaBoundary->batas_asam_akhir - $rasaBoundary->batas_asam_puncak);
        }

        if ($rasa > $rasaBoundary->batas_manis_awal && $rasa <= $rasaBoundary->batas_manis_puncak) {
            $validated['miu_rasa_manis'] = ($rasa - $rasaBoundary->batas_manis_awal) / ($rasaBoundary->batas_manis_puncak - $rasaBoundary->batas_manis_awal);
        } elseif ($rasa > $rasaBoundary->batas_manis_puncak && $rasa < $rasaBoundary->batas_manis_akhir) {
            $validated['miu_rasa_manis'] = ($rasaBoundary->batas_manis_akhir - $rasa) / ($rasaBoundary->batas_manis_akhir - $rasaBoundary->batas_manis_puncak);
        } elseif ($rasa == $rasaBoundary->batas_manis_puncak) {
            $validated['miu_rasa_manis'] = 1;
        }

        if ($rasa > $rasaBoundary->batas_pedas_awal && $rasa <= $rasaBoundary->batas_pedas_puncak) {
            $validated['miu_rasa_pedas'] = ($rasa - $rasaBoundary->batas_pedas_awal) / ($rasaBoundary->batas_pedas_puncak - $rasaBoundary->batas_pedas_awal);
        } elseif ($rasa > $rasaBoundary->batas_pedas_puncak && $rasa < $rasaBoundary->batas_pedas_akhir) {
            $validated['miu_rasa_pedas'] = ($rasaBoundary->batas_pedas_akhir - $rasa) / ($rasaBoundary->batas_pedas_akhir - $rasaBoundary->batas_pedas_puncak);
        } elseif ($rasa == $rasaBoundary->batas_pedas_puncak) {
            $validated['miu_rasa_pedas'] = 1;
        }

        if ($rasa > $rasaBoundary->batas_asin_awal && $rasa < $rasaBoundary->batas_asin_puncak) {
            $validated['miu_rasa_asin'] = ($rasa - $rasaBoundary->batas_asin_awal) / ($rasaBoundary->batas_asin_puncak - $rasaBoundary->batas_asin_awal);
        } elseif ($rasa >= $rasaBoundary->batas_asin_puncak) {
            $validated['miu_rasa_asin'] = 1;
        }

        return $validated;
    }
}