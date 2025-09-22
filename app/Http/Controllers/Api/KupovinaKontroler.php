<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kupovina;
use App\Models\Ulaznica;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KupovinaKontroler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $upit = Kupovina::withCount('ulaznice');

        if ($request->filled('stanje')) {
            $upit->where('stanje', $request->query('stanje'));
        }

        return response()->json(
            $upit->orderBy('id','desc')->paginate($request->query('po_strani', 15))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $podaci = $request->validate([
            'broj_porudzbine' => ['nullable','string','max:255','unique:kupovine,broj_porudzbine'],
            'korisnik_id'     => ['nullable','exists:users,id'],
            'email'           => ['nullable','email','max:255'],
            'ukupno'          => ['nullable','numeric','min:0'],
            'valuta'          => ['nullable','string','size:3'],
            'nacin_placanja'  => ['nullable','in:kartica,paypal,gotovina'],
            'stanje'          => ['nullable','in:novo,placeno,otkazano'],
        ]);

        if (empty($podaci['broj_porudzbine'])) {
            $podaci['broj_porudzbine'] = (string) Str::uuid();
        }
        $kupovina = Kupovina::create(array_merge([
            'ukupno' => $podaci['ukupno'] ?? 0,
            'valuta' => $podaci['valuta'] ?? 'RSD',
            'nacin_placanja' => $podaci['nacin_placanja'] ?? 'kartica',
            'stanje' => $podaci['stanje'] ?? 'novo',
        ], $podaci));

        return response()->json($kupovina->fresh()->load('ulaznice'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kupovina $kupovina)
    {
        return response()->json($kupovina->load('ulaznice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kupovina $kupovina)
    {
        $podaci = $request->validate([
            'korisnik_id'     => ['sometimes','nullable','exists:users,id'],
            'email'           => ['sometimes','nullable','email','max:255'],
            'ukupno'          => ['sometimes','numeric','min:0'],
            'valuta'          => ['sometimes','string','size:3'],
            'nacin_placanja'  => ['sometimes','in:kartica,paypal,gotovina'],
            'stanje'          => ['sometimes','in:novo,placeno,otkazano'],
        ]);

        $kupovina->update($podaci);

        return response()->json($kupovina->load('ulaznice'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kupovina $kupovina)
    {
        $kupovina->delete();
        return response()->noContent();
    }

    /** POST /kupovine/{kupovina}/ulaznice  (dodela dostupnih ulaznica kupovini) */
    public function dodeliUlaznice(Request $request, Kupovina $kupovina)
    {
        $podaci = $request->validate([
            'ulaznice_id' => ['required','array','min:1'],
            'ulaznice_id.*' => ['integer','exists:ulaznice,id'],
        ]);

        $rezultat = DB::transaction(function () use ($podaci, $kupovina) {
            // Dohvati samo dostupne ulaznice
            $ulaznice = Ulaznica::whereIn('id', $podaci['ulaznice_id'])
                ->where('status', 'dostupna')
                ->lockForUpdate()
                ->get();

            if ($ulaznice->count() === 0) {
                return ['uspeh' => false, 'poruka' => 'Nema dostupnih ulaznica za tražene ID-jeve.'];
            }

            $ukupno = 0;
            foreach ($ulaznice as $u) {
                $u->kupovina_id = $kupovina->id;
                $u->status = 'prodata';
                $u->save();
                $ukupno += (float) $u->cena;
            }

            // saberi sa postojećim iznosom
            $kupovina->ukupno = round(($kupovina->ukupno ?? 0) + $ukupno, 2);
            $kupovina->stanje = $kupovina->stanje === 'novo' ? 'placeno' : $kupovina->stanje;
            $kupovina->save();

            return ['uspeh' => true, 'ukupno_dodata_vrednost' => $ukupno];
        });

        if (!$rezultat['uspeh']) {
            return response()->json(['greska' => $rezultat['poruka']], 422);
        }

        return response()->json([
            'poruka'  => 'Ulaznice uspešno dodeljene kupovini.',
            'kupovina'=> $kupovina->fresh()->load('ulaznice'),
            'dodata_vrednost' => $rezultat['ukupno_dodata_vrednost'],
        ], 200);
    }

    /** GET /kupovine/{kupovina}/ulaznice */
    public function spisakUlaznica(Kupovina $kupovina)
    {
        return response()->json($kupovina->ulaznice()->orderBy('id')->get());
    }
}
