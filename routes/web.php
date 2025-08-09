
<?php
use App\Http\Controllers\FuzzyBoundaryController;
// Route untuk menyimpan boundaries
Route::post('/fuzzy/boundaries', [FuzzyBoundaryController::class, 'store'])->name('fuzzy.boundaries.store');

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FuzzyController;
use App\Http\Controllers\FuzzyRatingController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('menus', MenuController::class);

// Fuzzy Harga (tetap seperti sebelumnya)
Route::get('/Fuzzy', function () {
    return redirect()->route('fuzzy.input');
});
Route::get('/fuzzy/input', [FuzzyController::class, 'showInputForm'])->name('fuzzy.input');
Route::post('/fuzzy/calculate', [FuzzyController::class, 'calculateMiu'])->name('fuzzy.calculate');

// Fuzzy Rating (tambahan baru)
Route::prefix('fuzzy')->group(function() {
    // Rating Routes
    Route::get('rating', [FuzzyRatingController::class, 'inputRating'])->name('fuzzy.inputRating');
    Route::post('rating/preview', [FuzzyRatingController::class, 'calculateRatingPreview'])->name('fuzzy.calculateRatingPreview');
    Route::post('rating/calculate', [FuzzyRatingController::class, 'calculateRating'])->name('fuzzy.calculateRating');
    Route::get('rating/result/{id}', [FuzzyRatingController::class, 'resultRating'])->name('fuzzy.resultRating');
});