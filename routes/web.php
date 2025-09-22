<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MestoKontroler;
use App\Http\Controllers\Api\DogadjajKontroler;
use App\Http\Controllers\Api\UlaznicaKontroler;
use App\Http\Controllers\Api\KupovinaKontroler;
use App\Http\Middleware\HandleAppearance;

// SVE API rute kroz 'api' middleware i 'api/v1' prefiks
Route::middleware('api')->prefix('api/v1')->withoutMiddleware([HandleAppearance::class])->group(function () {

    // test
    Route::get('/proba', fn () => response()->json(['ok' => true]));

    // Mesta
    Route::get   ('/mesta',                 [MestoKontroler::class, 'index']);
    Route::post  ('/mesta',                 [MestoKontroler::class, 'store']);
    Route::get   ('/mesta/{mesto}',         [MestoKontroler::class, 'show']);
    Route::match (['put','patch'],'/mesta/{mesto}', [MestoKontroler::class, 'update']);
    Route::delete('/mesta/{mesto}',         [MestoKontroler::class, 'destroy']);

    // Događaji
    Route::get   ('/dogadjaji',                 [DogadjajKontroler::class, 'index']);
    Route::post  ('/dogadjaji',                 [DogadjajKontroler::class, 'store']);
    Route::get   ('/dogadjaji/{dogadjaj}',      [DogadjajKontroler::class, 'show']);
    Route::match (['put','patch'],'/dogadjaji/{dogadjaj}', [DogadjajKontroler::class, 'update']);
    Route::delete('/dogadjaji/{dogadjaj}',      [DogadjajKontroler::class, 'destory']);

    // Ulaznice
    Route::get   ('/ulaznice',                 [UlaznicaKontroler::class, 'index']);
    Route::post  ('/ulaznice',                 [UlaznicaKontroler::class, 'store']);
    Route::get   ('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'show']);
    Route::match (['put','patch'],'/ulaznice/{ulaznica}', [UlaznicaKontroler::class, 'update']);
    Route::delete('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'destoy']);

    // Ugnježdeno
    Route::get('/dogadjaji/{dogadjaj}/ulaznice', [UlaznicaKontroler::class, 'spisakZaDogadjaj']);

    // Kupovine
    Route::get   ('/kupovine',                [KupovinaKontroler::class, 'index']);
    Route::post  ('/kupovine',                [KupovinaKontroler::class, 'store']);
    Route::get   ('/kupovine/{kupovina}',     [KupovinaKontroler::class, 'show']);
    Route::match (['put','patch'],'/kupovine/{kupovina}', [KupovinaKontroler::class, 'update']);
    Route::delete('/kupovine/{kupovina}',     [KupovinaKontroler::class, 'destroy']);
    Route::post  ('/kupovine/{kupovina}/ulaznice', [KupovinaKontroler::class, 'dodeliUlaznice']);
    Route::get   ('/kupovine/{kupovina}/ulaznice', [KupovinaKontroler::class, 'spisakUlaznica']);
});
