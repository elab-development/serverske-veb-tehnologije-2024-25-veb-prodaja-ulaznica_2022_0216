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

    // Mesta - resource ruta
   Route::apiResource('mesta', MestoKontroler::class);
    //Redirektovanje - tip 3
   Route::redirect('/pocetna', '/api/v1/mesta');

    // Događaji - rute grupisane preko Route::controller
    Route::controller(DogadjajKontroler::class)->group(function () {
        Route::get   ('/dogadjaji',                'index');
        Route::post  ('/dogadjaji',                'store');
        Route::get   ('/dogadjaji/{dogadjaj}',     'show');
        Route::match (['put','patch'], '/dogadjaji/{dogadjaj}', 'update');
        Route::delete('/dogadjaji/{dogadjaj}',     'destroy');

        // NESTED (već imaš) – lista ulaznica za događaj
        Route::get('/dogadjaji/{dogadjaj}/ulaznice', [UlaznicaKontroler::class, 'spisakZaDogadjaj']);
    });

    //parametrizovana ruta sa constraint-om
    Route::get('/pretraga/dogadjaji/{grad?}', [DogadjajKontroler::class, 'pretragaPoGradu'])
     ->whereAlpha('grad')
     ->name('dogadjaji.pretraga');


    // Ulaznice
    Route::get   ('/ulaznice',                 [UlaznicaKontroler::class, 'index']);
    Route::post  ('/ulaznice',                 [UlaznicaKontroler::class, 'store']);
    Route::get   ('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'show']);
    Route::match (['put','patch'],'/ulaznice/{ulaznica}', [UlaznicaKontroler::class, 'update']);
    Route::delete('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'destoy']);

   

    // Kupovine
    Route::get   ('/kupovine',                [KupovinaKontroler::class, 'index']);
    Route::post  ('/kupovine',                [KupovinaKontroler::class, 'store']);
    Route::get   ('/kupovine/{kupovina}',     [KupovinaKontroler::class, 'show']);
    Route::match (['put','patch'],'/kupovine/{kupovina}', [KupovinaKontroler::class, 'update']);
    Route::delete('/kupovine/{kupovina}',     [KupovinaKontroler::class, 'destroy']);
    Route::post  ('/kupovine/{kupovina}/ulaznice', [KupovinaKontroler::class, 'dodeliUlaznice']);
    Route::get   ('/kupovine/{kupovina}/ulaznice', [KupovinaKontroler::class, 'spisakUlaznica']);

    //tip rute 4
    Route::fallback(fn() => response()->json([
        'message' => 'Ruta nije pronađena.'
    ], 404));
});
