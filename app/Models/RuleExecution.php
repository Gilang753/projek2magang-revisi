<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'rule_id',
        'miu_harga',
        'miu_rating',
        'miu_rasa',
        'alpha_predikat',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}
