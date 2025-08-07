<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuzzyRating extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'fuzzy_ratings';

    /**
     * Kolom yang dapat diisi massal.
     *
     * @var array
     */
    protected $fillable = [
        'rating_bintang',
        'nilai_rating',
        'rendah_min',
        'rendah_max',
        'sedang_min',
        'sedang_max',
        'tinggi_min',
        'tinggi_max',
        'keanggotaan_rendah',
        'keanggotaan_sedang',
        'keanggotaan_tinggi'
    ];

    /**
     * Nilai default untuk atribut model.
     *
     * @var array
     */
    protected $attributes = [
        'rendah_min' => 0,
        'rendah_max' => 40,
        'sedang_min' => 30,
        'sedang_max' => 70,
        'tinggi_min' => 60,
        'tinggi_max' => 100
    ];

    /**
     * Hitung nilai keanggotaan fuzzy berdasarkan rating bintang.
     *
     * @param int $ratingBintang
     * @return array
     */
    public static function hitungKeanggotaan(int $ratingBintang): array
    {
        $nilaiRating = $ratingBintang * 20;
        
        $rendahMin = 0;
        $rendahMax = 40;
        $sedangMin = 30;
        $sedangMax = 70;
        $tinggiMin = 60;
        $tinggiMax = 100;
        
        $keanggotaanRendah = max(0, min(1, ($rendahMax - $nilaiRating) / ($rendahMax - $rendahMin)));
        $keanggotaanSedang = max(0, min(1, min(
            ($nilaiRating - $sedangMin) / ($sedangMax - $sedangMin),
            ($sedangMax - $nilaiRating) / ($sedangMax - $sedangMin)
        )));
        $keanggotaanTinggi = max(0, min(1, ($nilaiRating - $tinggiMin) / ($tinggiMax - $tinggiMin)));
        
        return [
            'rating_bintang' => $ratingBintang,
            'nilai_rating' => $nilaiRating,
            'keanggotaan_rendah' => round($keanggotaanRendah, 2),
            'keanggotaan_sedang' => round($keanggotaanSedang, 2),
            'keanggotaan_tinggi' => round($keanggotaanTinggi, 2)
        ];
    }

    /**
     * Buat dan simpan record baru berdasarkan rating bintang.
     *
     * @param int $ratingBintang
     * @return FuzzyRating
     */
    public static function buatDariRating(int $ratingBintang): FuzzyRating
    {
        $data = self::hitungKeanggotaan($ratingBintang);
        return self::create($data);
    }

    /**
     * Aksesor untuk menentukan kategori dominan.
     *
     * @return string
     */
    public function getKategoriDominanAttribute(): string
    {
        $max = max(
            $this->keanggotaan_rendah,
            $this->keanggotaan_sedang,
            $this->keanggotaan_tinggi
        );
        
        switch ($max) {
            case $this->keanggotaan_tinggi:
                return 'Tinggi';
            case $this->keanggotaan_sedang:
                return 'Sedang';
            default:
                return 'Rendah';
        }
    }
}