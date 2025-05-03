<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\FavoriteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', [StoreController::class, 'index'])->name('stores.index');

Route::get('/stores/{store}', [StoreController::class, 'show'])->name('stores.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/stores/{store}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/stores/{store}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/import/csv', [ImportController::class, 'showCsvForm'])->name('import.csv.form');
        Route::post('/import/csv', [ImportController::class, 'importCsv'])->name('import.csv');
    });

    Route::post('/favorites/{store}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{store}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

Route::get('/stores/{store}/reviews-api', [StoreController::class, 'reviewsApi'])->name('stores.reviews.api');

require __DIR__.'/auth.php';