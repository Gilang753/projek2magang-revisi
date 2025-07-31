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
        'deskripsi', // Added deskripsi field
        'harga_seporsi',
        'cita_rasa',
        'rating',
        'gambar'
    ];

    protected $casts = [
        'harga_seporsi' => 'decimal:2',
        'rating' => 'integer'
    ];

    protected $attributes = [
        'rating' => 3,
        'deskripsi' => null, // Default value for deskripsi
    ];

    // Mengakses id sebagai kode_makanan
    public function getKodeMakananAttribute()
    {
        return $this->id;
    }

    public static function getCitaRasaOptions()
    {
        return [
            'asin' => 'Asin',
            'manis' => 'Manis',
            'pedas' => 'Pedas',
            'asam' => 'Asam',
            'gurih' => 'Gurih',
            'pahit' => 'Pahit',
        ];
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