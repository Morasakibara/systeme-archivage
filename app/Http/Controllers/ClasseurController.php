<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClasseurController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $query = Classeur::with('creator');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $classeurs = $query->withCount('dossiers')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($classeurs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classeur = Classeur::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($classeur->load('creator'), 201);
    }

    public function show(Classeur $classeur)
    {
        return response()->json($classeur->load([
            'creator',
            'dossiers.entreprise',
            'dossiers.assignedUser'
        ]));
    }

    public function update(Request $request, Classeur $classeur)
    {
        $this->authorize('update', $classeur);

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $classeur->update($request->only(['nom', 'description']));

        return response()->json($classeur);
    }

    public function destroy(Classeur $classeur)
    {
        $this->authorize('delete', $classeur);

        if ($classeur->dossiers()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer un classeur contenant des dossiers'
            ], 422);
        }

        $classeur->delete();

        return response()->json(['message' => 'Classeur supprimé avec succès']);
    }

}
