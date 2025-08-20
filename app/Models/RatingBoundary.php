<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingBoundary extends Model
{
    use HasFactory;

    protected $table = 'rating_boundaries';
    protected $fillable = [
        'batas_rendah_awal',
        'batas_rendah_puncak',
        'batas_rendah_akhir',
        'batas_sedang_awal',
        'batas_sedang_puncak',
        'batas_sedang_akhir',
        'batas_tinggi_awal',
        'batas_tinggi_puncak',
        'batas_tinggi_akhir'
    ];
}