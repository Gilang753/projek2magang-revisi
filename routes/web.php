<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FuzzyController;


Route::get('/', function () {
    return view('welcome');
});

Route::resource('menus', MenuController::class);

Route::get('/Fuzzy', function () {
    return redirect()->route('fuzzy.input');
});
Route::get('/fuzzy/input', [FuzzyController::class, 'showInputForm'])->name('fuzzy.input');
Route::post('/fuzzy/calculate', [FuzzyController::class, 'calculateMiu'])->name('fuzzy.calculate');