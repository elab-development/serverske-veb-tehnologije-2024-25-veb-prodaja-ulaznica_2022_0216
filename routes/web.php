<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

use App\Http\Controllers\Api\AuthKontroler;
use App\Http\Controllers\Api\MestoKontroler;
use App\Http\Controllers\Api\DogadjajKontroler;
use App\Http\Controllers\Api\UlaznicaKontroler;
use App\Http\Controllers\Api\KupovinaKontroler;

Route::prefix('api/v1')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {


        // Auth
        Route::post('/auth/register', [AuthKontroler::class, 'register']);
        Route::post('/auth/login',    [AuthKontroler::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::get ('/auth/me',     [AuthKontroler::class, 'me']);
            Route::post('/auth/logout', [AuthKontroler::class, 'logout']);
        });

        // Mesta
        // Javno - citanje
        Route::apiResource('mesta', MestoKontroler::class)->only(['index','show']);
        // Zasticeno - upis/izmena/brisanje
        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('mesta', MestoKontroler::class)->only(['store','update','destroy']);
        });

        // Dogadj
        // Javno - citanje
        Route::get   ('/dogadjaji',                        [DogadjajKontroler::class, 'index']);
        Route::get   ('/dogadjaji/{dogadjaj}',             [DogadjajKontroler::class, 'show']);
        Route::get   ('/dogadjaji/{dogadjaj}/ulaznice',    [UlaznicaKontroler::class, 'spisakZaDogadjaj']);
        Route::get('/pretraga/dogadjaji/{grad?}', [DogadjajKontroler::class, 'pretragaPoGradu'])
            ->whereAlpha('grad')
            ->name('dogadjaji.pretraga');
        // Zasticeno - upis/izmena/brisanje
        Route::middleware('auth:sanctum')->group(function () {
            Route::post  ('/dogadjaji',                        [DogadjajKontroler::class, 'store']);
            Route::match (['put','patch'], '/dogadjaji/{dogadjaj}', [DogadjajKontroler::class, 'update']);
            Route::delete('/dogadjaji/{dogadjaj}',             [DogadjajKontroler::class, 'destroy']);
        });

        // ulaznice
        // Javno - citanje
        Route::get   ('/ulaznice',                 [UlaznicaKontroler::class, 'index']);
        Route::get   ('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'show']);
        // Zasticeno - upis/izmena/brisanje
        Route::middleware('auth:sanctum')->group(function () {
            Route::post  ('/ulaznice',                 [UlaznicaKontroler::class, 'store']);
            Route::match (['put','patch'], '/ulaznice/{ulaznica}', [UlaznicaKontroler::class, 'update']);
            Route::delete('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'destroy']);
        });

        // kupovine
        // Javno - citanje
        Route::get   ('/kupovine',                      [KupovinaKontroler::class, 'index']);
        Route::get   ('/kupovine/{kupovina}',           [KupovinaKontroler::class, 'show']);
        Route::get   ('/kupovine/{kupovina}/ulaznice',  [KupovinaKontroler::class, 'spisakUlaznica']);
        // Zasticeno - upis/izmena/brisanje
        Route::middleware('auth:sanctum')->group(function () {
            Route::post  ('/kupovine',                      [KupovinaKontroler::class, 'store']);
            Route::match (['put','patch'], '/kupovine/{kupovina}', [KupovinaKontroler::class, 'update']);
            Route::delete('/kupovine/{kupovina}',           [KupovinaKontroler::class, 'destroy']);
            Route::post  ('/kupovine/{kupovina}/ulaznice',  [KupovinaKontroler::class, 'dodeliUlaznice']);
        });

        // Fallback za nepostojeće API rute
        Route::fallback(fn() => response()->json(['message' => 'Ruta nije pronađena.'], 404));
    });
