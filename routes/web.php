<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Plat;
use App\Models\User;
use Faker\Factory as FakerFactory;
use FakerRestaurant\Provider\fr_FR\Restaurant as FakerRestaurant;
use App\Http\Controllers\PlatController;
use App\Http\Controllers\FavorisController;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/plats/topcrea', [PlatController::class, 'topCreators'])->name('topcrea');

    // Utilisation de resource controller pour PlatController
    Route::resource('plats', PlatController::class);

    Route::post('/plats/{plat}/favori', [FavorisController::class, 'toggleFavori'])->name('toggle.favori');
});

