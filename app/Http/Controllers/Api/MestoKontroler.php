<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mesto;
use Illuminate\Http\Request;

class MestoKontroler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $upit = Mesto::query();

        if ($grad = $request->query('grad')) {
            $upit->where('grad', 'like', "%{$grad}%");
        }

        return response()->json(
            $upit->orderBy('naziv')->paginate($request->query('po_strani', 15))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $podaci = $request->validate([
            'naziv'     => ['required','string','max:255'],
            'adresa'    => ['nullable','string','max:255'],
            'grad'      => ['required','string','max:255'],
            'kapacitet' => ['required','integer','min:0'],
        ]);

        $mesto = Mesto::create($podaci);

        return response()->json($mesto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mesto $mesto)
    {
        return response()->json($mesto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mesto $mesto)
    {
         $podaci = $request->validate([
            'naziv'     => ['sometimes','string','max:255'],
            'adresa'    => ['sometimes','nullable','string','max:255'],
            'grad'      => ['sometimes','string','max:255'],
            'kapacitet' => ['sometimes','integer','min:0'],
        ]);

        $mesto->update($podaci);

        return response()->json($mesto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mesto $mesto)
    {
        $mesto->delete(); // cascade briše događaje ako je tako podešeno u migraciji
        return response()->noContent();
    }
}
