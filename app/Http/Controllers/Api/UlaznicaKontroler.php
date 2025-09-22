<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ulaznica;
use Illuminate\Http\Request;
use App\Models\Dogadjaj;

class UlaznicaKontroler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $upit = Ulaznica::with(['dogadjaj','kupovina']);

        if ($request->filled('status')) {
            $upit->where('status', $request->query('status'));
        }
        if ($request->filled('dogadjaj_id')) {
            $upit->where('dogadjaj_id', $request->query('dogadjaj_id'));
        }

        return response()->json(
            $upit->orderBy('id','desc')->paginate($request->query('po_strani', 15))
        );
    }

    public function indexZaDogadjaj(Dogadjaj $dogadjaj, Request $zahtev)
    {
        $upit = $dogadjaj->ulaznice()->with('kupovina');

        if ($zahtev->filled('status')) {
            $upit->where('status', $zahtev->query('status'));
        }

        return response()->json(
            $upit->orderBy('id','asc')->paginate($zahtev->query('po_strani', 15))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $podaci = $request->validate([
            'dogadjaj_id' => ['required','exists:dogadjaji,id'],
            'kupovina_id' => ['nullable','exists:kupovine,id'],
            'kod'         => ['required','string','max:255','unique:ulaznice,kod'],
            'tip'         => ['required','string','max:50'],
            'sediste'     => ['nullable','string','max:50'],
            'cena'        => ['required','numeric','min:0'],
            'status'      => ['required','in:dostupna,rezervisana,prodata'],
        ]);

        $ulaznica = Ulaznica::create($podaci);

        return response()->json($ulaznica->load('dogadjaj','kupovina'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ulaznica $ulaznica)
    {
        return response()->json($ulaznica->load('dogadjaj','kupovina'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ulaznica $ulaznica)
    {
        $podaci = $request->validate([
            'dogadjaj_id' => ['sometimes','exists:dogadjaji,id'],
            'kupovina_id' => ['sometimes','nullable','exists:kupovine,id'],
            'kod'         => ['sometimes','string','max:255',"unique:ulaznice,kod,{$ulaznica->id}"],
            'tip'         => ['sometimes','string','max:50'],
            'sediste'     => ['sometimes','nullable','string','max:50'],
            'cena'        => ['sometimes','numeric','min:0'],
            'status'      => ['sometimes','in:dostupna,rezervisana,prodata'],
        ]);

        $ulaznica->update($podaci);

        return response()->json($ulaznica->load('dogadjaj','kupovina'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ulaznica $ulaznica)
    {
        $ulaznica->delete();
        return response()->noContent();
    }
}
