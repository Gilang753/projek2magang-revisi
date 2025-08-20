<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingHistory extends Model
{
    use HasFactory;

    protected $table = 'rating_histories';
    protected $fillable = [
        'rating',
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'miu_rendah',
        'miu_sedang',
        'miu_tinggi'
    ];
}