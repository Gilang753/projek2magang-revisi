<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomendasiBoundary extends Model
{
    use HasFactory;

    protected $fillable = [
        'batas_tidak_puncak',
        'batas_tidak_akhir',
        'batas_rekomendasi_awal',
        'batas_rekomendasi_puncak',
    ];
}