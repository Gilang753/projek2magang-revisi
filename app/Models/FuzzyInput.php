<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuzzyInput extends Model
{
    use HasFactory;

    protected $fillable = ['harga', 'p1', 'p2', 'p3', 'p4', 'p5', 'miu_murah', 'miu_sedang', 'miu_mahal'];
}