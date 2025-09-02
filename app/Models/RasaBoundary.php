<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RasaBoundary extends Model
{
    use HasFactory;

    protected $fillable = [
        'batas_asam_puncak',
        'batas_asam_akhir',
        'batas_manis_awal',
        'batas_manis_puncak',
        'batas_manis_akhir',
        'batas_pedas_awal',
        'batas_pedas_puncak',
        'batas_pedas_akhir',
        'batas_asin_awal',
        'batas_asin_puncak',
    ];
}