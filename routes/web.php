<?php
use App\Http\Controllers\FuzzyBoundaryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FuzzyController;
use App\Http\Controllers\RatingFuzzyController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPageController;
use App\Http\Controllers\RasaFuzzyController;
use App\Http\Controllers\RekomendasiFuzzyController;

// Route untuk halaman utama
Route::get('/', function () {
    return view('/admin/login');
});

// Route authentication
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Route untuk halaman user (bisa diakses tanpa login)
Route::get('/user', [UserPageController::class, 'index'])->name('user.index');
Route::post('/user/execute-rule', [UserPageController::class, 'executeRule'])->name('user.executeRule');

// Route yang memerlukan authentication admin
Route::middleware(['admin.auth'])->group(function () {
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

    // Route untuk Fuzzy Rasa
    Route::prefix('fuzzy/rasa')->group(function () {
        Route::get('/', [RasaFuzzyController::class, 'index'])->name('fuzzy.inputRasa');
        Route::post('/store-boundaries', [RasaFuzzyController::class, 'storeBoundaries'])->name('fuzzy.rasa.boundaries.store');
        Route::post('/calculate-rasa', [RasaFuzzyController::class, 'calculate'])->name('fuzzy.rasa.calculate');
    });
        

    // Route untuk Fuzzy Rekomendasi
Route::prefix('fuzzy/rekomendasi')->group(function () {
    Route::get('/', [RekomendasiFuzzyController::class, 'index'])->name('fuzzy.inputRekomendasi');
    Route::post('/store-boundaries', [RekomendasiFuzzyController::class, 'storeBoundaries'])->name('fuzzy.rekomendasi.boundaries.store');
});
});