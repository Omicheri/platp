<?php

use App\Http\Middleware\CheckPlatOwnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Plat;
use App\Models\User;
use Faker\Factory as FakerFactory;
use FakerRestaurant\Provider\fr_FR\Restaurant as FakerRestaurant;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\FavorisController;

Auth::routes();

Route::middleware(['auth'])->group(function () {
        Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(CheckPlatOwnership::class)->group(function () {
        Route::get('/plats/create', [PlatController::class, 'create'])->name('plats.create');
        Route::post('/plats', [PlatController::class, 'store'])->name('plats.store');
        Route::delete('/plats/{plat}', [PlatController::class, 'destroy'])->name('plats.destroy');
        Route::get('/plats/{plat}/edit', [PlatController::class, 'edit'])->name('plats.edit');
        Route::put('/plats/{plat}', [PlatController::class, 'update'])->name('plats.update');
});
    Route::get('/plats/topcrea', [PlatController::class, 'topCreators'])->name('topcrea');
    Route::resource('/plats', PlatController::class)->except('destroy', 'edit', 'create', 'update', 'store');
    Route::post('/plats/{plat}/favori', [FavorisController::class, 'toggleFavori'])->name('toggle.favori');
});

