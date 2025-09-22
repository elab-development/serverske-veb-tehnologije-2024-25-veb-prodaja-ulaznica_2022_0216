<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dogadjaj;
use Illuminate\Http\Request;

class DogadjajKontroler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
}
