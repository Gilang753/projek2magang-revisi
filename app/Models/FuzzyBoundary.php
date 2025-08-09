<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuzzyBoundary extends Model
{
    use HasFactory;
    protected $fillable = [
        'batas1', 'batas2', 'batas3', 'batas4'
    ];
}
