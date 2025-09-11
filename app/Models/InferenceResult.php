<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InferenceResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_id',
        'menu_id',
        'miu_harga',
        'miu_rating',
        'miu_rasa',
        'alpha',
        'rekomendasi',
        'z_user',
    ];
}
