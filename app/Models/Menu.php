<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'tb_menu';


    protected $fillable = [
        'nama',
        'deskripsi',
        'harga_seporsi',
        'nilai_rasa',
        'cita_rasa',
        'nilai_rating',
        'rating',
        'gambar',
        'miu_harga_murah',
        'miu_harga_sedang',
        'miu_harga_mahal',
        'miu_rating_rendah',
        'miu_rating_sedang',
        'miu_rating_tinggi',
        'miu_rasa_asam',
        'miu_rasa_manis',
        'miu_rasa_pedas',
        'miu_rasa_asin'
    ];


    protected $casts = [
        'harga_seporsi' => 'decimal:2',
        'nilai_rasa' => 'float',
        'nilai_rating' => 'float',
        'rating' => 'integer',
        'miu_harga_murah' => 'float',
        'miu_harga_sedang' => 'float',
        'miu_harga_mahal' => 'float',
        'miu_rating_rendah' => 'float',
        'miu_rating_sedang' => 'float',
        'miu_rating_tinggi' => 'float',
        'miu_rasa_asam' => 'float',
        'miu_rasa_manis' => 'float',
        'miu_rasa_pedas' => 'float',
        'miu_rasa_asin' => 'float',
    ];
    /**
     * Mapping nilai_rating ke rating bintang 1-5.
     */
    public static function mapNilaiToRating($nilai_rating)
    {
        if ($nilai_rating >= 0 && $nilai_rating < 20) {
            return 1;
        } elseif ($nilai_rating >= 20 && $nilai_rating < 40) {
            return 2;
        } elseif ($nilai_rating >= 40 && $nilai_rating < 60) {
            return 3;
        } elseif ($nilai_rating >= 60 && $nilai_rating < 80) {
            return 4;
        } elseif ($nilai_rating >= 80 && $nilai_rating <= 100) {
            return 5;
        }
        return 1;
    }

    protected $attributes = [
        'rating' => 3,
        'deskripsi' => null, // Default value for deskripsi
    ];

    // Mengakses id sebagai kode_makanan
    public function getKodeMakananAttribute()
    {
        return $this->id;
    }


    /**
     * Mapping nilai ke cita rasa sesuai range.
     */
    public static function mapNilaiToRasa($nilai_rasa)
    {
        if ($nilai_rasa > 0 && $nilai_rasa <= 25) {
            return 'asam';
        } elseif ($nilai_rasa > 25 && $nilai_rasa <= 50) {
            return 'manis';
        } elseif ($nilai_rasa > 50 && $nilai_rasa <= 75) {
            return 'pedas';
        } elseif ($nilai_rasa > 75 && $nilai_rasa <= 100) {
            return 'asin';
        }
        return 'tidak diketahui';
    }

    public static function getRatingOptions()
    {
        return [
            1 => '⭐',
            2 => '⭐⭐',
            3 => '⭐⭐⭐',
            4 => '⭐⭐⭐⭐',
            5 => '⭐⭐⭐⭐⭐',
        ];
    }

    // Accessor untuk deskripsi yang kosong
    public function getDeskripsiAttribute($value)
    {
        return $value ?? 'Tidak ada deskripsi tersedia';
    }
}