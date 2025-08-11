<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuzzyBoundary extends Model
{
    use HasFactory;
    protected $fillable = [
        'batas1', 'batas2', 'batas3', 'batas4', 'batas5',
        'batas_murah_awal', 'batas_murah_puncak', 'batas_murah_akhir',
        'batas_sedang_awal', 'batas_sedang_puncak', 'batas_sedang_akhir',
        'batas_mahal_awal', 'batas_mahal_puncak', 'batas_mahal_akhir',
    ];
}
