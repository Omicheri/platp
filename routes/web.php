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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/plats', [PlatController::class, 'index'])->name('plats.index');
Route::get('/plats/create', [PlatController::class, 'create'])->name('plats.create');
Route::get('/plats/{plat}', [PlatController::class, 'show'])->name('plats.show');

Route::post('/plats', [PlatController::class, 'store'])->name('plats.store');
Route::get('/plats/{plat}/edit', [PlatController::class, 'edit'])->name('plats.edit');
Route::put('/plats/{plat}', [PlatController::class, 'update'])->name('plats.update');
Route::delete('/plats/{plat}', [PlatController::class, 'destroy'])->name('plats.destroy');

Route::post('/plats/{plat}/favori', [FavorisController::class, 'toggleFavori'])->name('toggle.favori');


