<?php
use App\Http\Controllers\FuzzyBoundaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FuzzyController;
use App\Http\Controllers\RatingFuzzyController;
use App\Http\Controllers\RuleController; 

// Route untuk halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Route untuk mengelola menu
Route::resource('menus', MenuController::class);

// Route untuk mengelola rules
Route::resource('rules', RuleController::class); 
Route::post('/rules/execute', [RuleController::class, 'execute'])->name('rules.execute');

// Route untuk Fuzzy Harga
Route::post('/fuzzy/boundaries', [FuzzyBoundaryController::class, 'store'])->name('fuzzy.boundaries.store');
Route::get('/Fuzzy', function () {
    return redirect()->route('fuzzy.input');
});
Route::get('/fuzzy/input', [FuzzyController::class, 'showInputForm'])->name('fuzzy.input');
Route::post('/fuzzy/calculate', [FuzzyController::class, 'calculateMiu'])->name('fuzzy.calculate');

// Route untuk Fuzzy Rating
Route::prefix('fuzzy')->group(function () {
    Route::get('/fuzzy/inputRating', [RatingFuzzyController::class, 'index'])->name('fuzzy.inputRating');
    Route::post('/fuzzy/store-boundaries', [RatingFuzzyController::class, 'storeBoundaries'])->name('fuzzy.rating.boundaries.store');
    Route::post('/fuzzy/calculate-rating', [RatingFuzzyController::class, 'calculate'])->name('fuzzy.rating.calculate');
});

// Route untuk halaman user

use App\Http\Controllers\UserPageController;
Route::get('/user', [UserPageController::class, 'index'])->name('user.index');
Route::post('/user/execute-rule', [UserPageController::class, 'executeRule'])->name('user.executeRule');