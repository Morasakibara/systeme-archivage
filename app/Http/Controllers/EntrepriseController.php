<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\Request;

class EntrepriseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = Entreprise::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $entreprises = $query->withCount('dossiers')
            ->orderBy('nom')
            ->paginate(15);

        return response()->json($entreprises);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string',
            'telephone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
        ]);

        $entreprise = Entreprise::create($request->validated());

        return response()->json($entreprise, 201);
    }

    public function show(Entreprise $entreprise)
    {
        return response()->json($entreprise->load('dossiers.classeur'));
    }

    public function update(Request $request, Entreprise $entreprise)
    {
        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'adresse' => 'sometimes|required|string',
            'telephone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|string|email|max:255',
        ]);

        $entreprise->update($request->validated());

        return response()->json($entreprise);
    }

    public function destroy(Entreprise $entreprise)
    {
        if ($entreprise->dossiers()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une entreprise ayant des dossiers'
            ], 422);
        }

        $entreprise->delete();

        return response()->json(['message' => 'Entreprise supprimée avec succès']);
    }
}
