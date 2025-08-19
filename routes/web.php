<?php
use App\Http\Controllers\FuzzyBoundaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FuzzyController;
use App\Http\Controllers\FuzzyRatingController;
use App\Http\Controllers\RuleController; // Tambahkan ini

// Route untuk halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Route untuk mengelola menu
Route::resource('menus', MenuController::class);

// Route untuk mengelola rules
Route::resource('rules', RuleController::class); // Tambahkan ini

// Route untuk Fuzzy Harga
Route::post('/fuzzy/boundaries', [FuzzyBoundaryController::class, 'store'])->name('fuzzy.boundaries.store');
Route::get('/Fuzzy', function () {
    return redirect()->route('fuzzy.input');
});
Route::get('/fuzzy/input', [FuzzyController::class, 'showInputForm'])->name('fuzzy.input');
Route::post('/fuzzy/calculate', [FuzzyController::class, 'calculateMiu'])->name('fuzzy.calculate');

// Route untuk Fuzzy Rating
Route::prefix('fuzzy')->group(function() {
    // Rating Routes
    Route::get('rating', [FuzzyRatingController::class, 'inputRating'])->name('fuzzy.inputRating');
    Route::post('rating/preview', [FuzzyRatingController::class, 'calculateRatingPreview'])->name('fuzzy.calculateRatingPreview');
    Route::post('rating/calculate', [FuzzyRatingController::class, 'calculateRating'])->name('fuzzy.calculateRating');
    Route::get('rating/result/{id}', [FuzzyRatingController::class, 'resultRating'])->name('fuzzy.resultRating');
});