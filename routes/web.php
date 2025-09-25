<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken; // aplikacioni CSRF middleware

use App\Http\Controllers\Api\AuthKontroler;
use App\Http\Controllers\Api\MestoKontroler;
use App\Http\Controllers\Api\DogadjajKontroler;
use App\Http\Controllers\Api\UlaznicaKontroler;
use App\Http\Controllers\Api\KupovinaKontroler;


// ===== API (sve pod /api/v1), bez CSRF-a =====
Route::prefix('api/v1')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {

        // Health
        Route::get('/ping', fn() => response()->json(['ok' => true]));

        // Debug echo – za proveru šta Laravel prima
        Route::post('/_debug/echo', function (\Illuminate\Http\Request $request) {
            return response()->json([
                'method'  => $request->method(),
                'raw'     => $request->getContent(),
                'parsed'  => $request->all(),
            ]);
        });

        // ---------- AUTH ----------
        Route::post('/auth/register', [AuthKontroler::class, 'register']);
        Route::post('/auth/login',    [AuthKontroler::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get ('/auth/me',     [AuthKontroler::class, 'me']);
            Route::post('/auth/logout', [AuthKontroler::class, 'logout']);
        });

        // ---------- MESTA ----------
        Route::apiResource('mesta', MestoKontroler::class);

        // ---------- DOGAĐAJI ----------
        Route::controller(DogadjajKontroler::class)->group(function () {
            Route::get   ('/dogadjaji',                        'index');
            Route::post  ('/dogadjaji',                        'store');
            Route::get   ('/dogadjaji/{dogadjaj}',             'show');
            Route::match (['put','patch'], '/dogadjaji/{dogadjaj}', 'update');
            Route::delete('/dogadjaji/{dogadjaj}',             'destroy');

            // dodatna pomoćna ruta: ulaznice za određeni događaj
            Route::get('/dogadjaji/{dogadjaj}/ulaznice', [UlaznicaKontroler::class, 'spisakZaDogadjaj']);
        });

        // Pretraga po gradu (opciono)
        Route::get('/pretraga/dogadjaji/{grad?}', [DogadjajKontroler::class, 'pretragaPoGradu'])
            ->whereAlpha('grad')
            ->name('dogadjaji.pretraga');

        // ---------- ULAZNICE ----------
        Route::get   ('/ulaznice',                 [UlaznicaKontroler::class, 'index']);
        Route::post  ('/ulaznice',                 [UlaznicaKontroler::class, 'store']);
        Route::get   ('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'show']);
        Route::match (['put','patch'], '/ulaznice/{ulaznica}', [UlaznicaKontroler::class, 'update']);
        Route::delete('/ulaznice/{ulaznica}',      [UlaznicaKontroler::class, 'destroy']); // paži: destroy (ne "destoy")

        // ---------- KUPOVINE ----------
        Route::get   ('/kupovine',                      [KupovinaKontroler::class, 'index']);
        Route::post  ('/kupovine',                      [KupovinaKontroler::class, 'store']);
        Route::get   ('/kupovine/{kupovina}',           [KupovinaKontroler::class, 'show']);
        Route::match (['put','patch'], '/kupovine/{kupovina}', [KupovinaKontroler::class, 'update']);
        Route::delete('/kupovine/{kupovina}',           [KupovinaKontroler::class, 'destroy']);
        Route::post  ('/kupovine/{kupovina}/ulaznice',  [KupovinaKontroler::class, 'dodeliUlaznice']);
        Route::get   ('/kupovine/{kupovina}/ulaznice',  [KupovinaKontroler::class, 'spisakUlaznica']);

        // Fallback za nepostojeće API rute
        Route::fallback(fn() => response()->json(['message' => 'Ruta nije pronađena.'], 404));
    });
