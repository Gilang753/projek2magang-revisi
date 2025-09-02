<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RasaHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rasa',
        'miu_asam',
        'miu_manis',
        'miu_pedas',
        'miu_asin',
    ];
}