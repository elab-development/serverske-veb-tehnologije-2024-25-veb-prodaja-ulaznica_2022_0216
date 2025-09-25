<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dogadjaj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;


class DogadjajKontroler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
{
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'page'      => ['integer','min:1'],
        'per_page'  => ['integer','min:1'],
        'mesto_id'  => ['integer','min:1'],
        'datum_od'  => ['date'],
        'datum_do'  => ['date','after_or_equal:datum_od'],
        'q'         => ['string','max:255'],
    ]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $cacheKey = 'dogadjaji:' . md5($request->fullUrl());
    $ttl = 60; // sekundi

    $result = \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, function () use ($request) {

        // Uvek max 3 po strani
        $perPage = (int) ($request->integer('per_page') ?: 3);
        if ($perPage > 3) {
            $perPage = 3;
        }

        $pageReq = (int) ($request->integer('page') ?: 1);
        $page    = max(1, $pageReq);

        $q = \App\Models\Dogadjaj::query()->with(['mesto']);

        // FILTRI
        if ($request->filled('mesto_id')) {
            $q->where('mesto_id', (int)$request->mesto_id);
        }
        if ($request->filled('datum_od')) {
            $q->whereDate('datum_pocetka', '>=', $request->datum_od);
        }
        if ($request->filled('datum_do')) {
            $q->whereDate('datum_pocetka', '<=', $request->datum_do);
        }

        // PRETRAGA
        if ($request->filled('q')) {
            $needle = trim($request->q);
            $q->where(function ($qq) use ($needle) {
                $qq->where('naziv', 'like', "%{$needle}%")
                   ->orWhere('opis', 'like', "%{$needle}%");
            });
        }

        // SORT
        $q->orderBy('id', 'desc');

        $paginator = $q->paginate($perPage, ['*'], 'page', $page)
                       ->appends($request->query());

        // Ako korisnik traži stranu veću od poslednje → prebaci na poslednju
        if ($paginator->lastPage() > 0 && $page > $paginator->lastPage()) {
            $page = $paginator->lastPage();
            $paginator = $q->paginate($perPage, ['*'], 'page', $page)
                           ->appends($request->query());
        }

        return [
            'ok'   => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'links'        => [
                    'next' => $paginator->nextPageUrl(),
                    'prev' => $paginator->previousPageUrl(),
                ],
            ],
        ];
    });

    return response()->json($result);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $upit = Dogadjaj::with('mesto');

        if ($request->filled('mesto_id')) {
            $upit->where('mesto_id', $request->query('mesto_id'));
        }
        if ($od = $request->query('od')) {
            $upit->where('datum_pocetka', '>=', $od);
        }
        if ($do = $request->query('do')) {
            $upit->where('datum_pocetka', '<=', $do);
        }

        return response()->json(
            $upit->orderBy('datum_pocetka','asc')->paginate($request->query('po_strani', 15))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Dogadjaj $dogadjaj)
    {
        return response()->json($dogadjaj->load('mesto','ulaznice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dogadjaj $dogadjaj)
    {
        $podaci = $request->validate([
            'naziv'            => ['sometimes','string','max:255'],
            'opis'             => ['sometimes','nullable','string'],
            'mesto_id'         => ['sometimes','exists:mesta,id'],
            'datum_pocetka'    => ['sometimes','date'],
            'datum_zavrsetka'  => ['sometimes','nullable','date','after_or_equal:datum_pocetka'],
        ]);

        $dogadjaj->update($podaci);

        return response()->json($dogadjaj->load('mesto'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dogadjaj $dogadjaj)
    {
        $dogadjaj->delete();
        return response()->noContent();
    }

    public function pretragaPoGradu(?string $grad = null, Request $request)
{
    $upit = \App\Models\Dogadjaj::with('mesto')->orderBy('datum_pocetka');

    if ($grad) {
        $upit->whereHas('mesto', fn($q) => $q->where('grad','like',"%{$grad}%"));
    }

    return response()->json(
        $upit->paginate($request->query('po_strani',15))
    );
}
}
