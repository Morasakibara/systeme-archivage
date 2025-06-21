<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Services\NotificationService;
use App\Services\HistoriqueService;
use App\Services\ChiffrementService;
use App\Services\CloudStorageService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDossierRequest;
use App\Http\Requests\UpdateDossierRequest;

class DossierController extends Controller
{
    protected $notificationService;
    protected $historiqueService;
    protected $chiffrementService;
    protected $cloudStorageService;

    public function __construct(
        NotificationService $notificationService,
        HistoriqueService $historiqueService,
        ChiffrementService $chiffrementService,
        CloudStorageService $cloudStorageService
    ) {
        $this->middleware('auth:sanctum');
        $this->notificationService = $notificationService;
        $this->historiqueService = $historiqueService;
        $this->chiffrementService = $chiffrementService;
        $this->cloudStorageService = $cloudStorageService;
    }

    public function index(Request $request)
    {
        $query = Dossier::with(['entreprise', 'classeur', 'creator', 'assignedUser']);

        // Filtres
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('classeur_id')) {
            $query->where('classeur_id', $request->classeur_id);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Restriction selon le rôle
        $user = $request->user();
        if ($user->isEmploye()) {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        $dossiers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($dossiers);
    }

    public function store(StoreDossierRequest $request)
    {
        $dossier = Dossier::create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()->id]
        ));

        // Log de l'action
        $this->historiqueService->logAction(
            $dossier,
            $request->user(),
            'CREATE',
            'Création du dossier'
        );

        // Notification au PDG
        $this->notificationService->notifyPDG($dossier, 'Nouveau dossier créé');

        return response()->json($dossier->load(['entreprise', 'classeur']), 201);
    }

    public function show(Dossier $dossier)
    {
        $this->authorize('view', $dossier);

        return response()->json($dossier->load([
            'entreprise',
            'classeur',
            'creator',
            'assignedUser',
            'documents',
            'historiqueActions.user'
        ]));
    }

    public function update(UpdateDossierRequest $request, Dossier $dossier)
    {
        $this->authorize('update', $dossier);

        $oldStatus = $dossier->statut;
        $dossier->update($request->validated());

        // Log si changement de statut
        if ($oldStatus !== $dossier->statut) {
            $this->historiqueService->logAction(
                $dossier,
                $request->user(),
                'STATUS_CHANGE',
                "Statut changé de {$oldStatus} à {$dossier->statut}"
            );
        }

        return response()->json($dossier);
    }

    public function assign(Request $request, Dossier $dossier)
    {
        $this->authorize('assign', $dossier);

        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $dossier->update(['assigned_to' => $request->assigned_to]);

        // Log de l'action
        $this->historiqueService->logAction(
            $dossier,
            $request->user(),
            'ASSIGN',
            'Dossier attribué'
        );

        // Notification à l'employé
        $this->notificationService->notifyEmployee($dossier, 'Nouveau dossier attribué');

        return response()->json($dossier);
    }

    public function archive(Request $request, Dossier $dossier)
    {
        $this->authorize('archive', $dossier);

        if (!$dossier->canBeArchived()) {
            return response()->json([
                'message' => 'Ce dossier ne peut pas être archivé dans son état actuel'
            ], 422);
        }

        // Chiffrement des données
        $encryptedData = $this->chiffrementService->chiffrerDossier($dossier);

        // Upload vers le cloud
        $cloudPath = $this->cloudStorageService->uploadToCloud($encryptedData, $dossier);

        // Mise à jour du dossier
        $dossier->update([
            'statut' => 'ARCHIVE',
            'archived_at' => now(),
            'cloud_path' => $cloudPath,
            'is_encrypted' => true
        ]);

        // Log de l'action
        $this->historiqueService->logAction(
            $dossier,
            $request->user(),
            'ARCHIVE',
            'Dossier archivé et transféré vers le cloud'
        );

        return response()->json([
            'message' => 'Dossier archivé avec succès',
            'dossier' => $dossier
        ]);
    }

    public function destroy(Dossier $dossier)
    {
        $this->authorize('delete', $dossier);

        $dossier->delete();

        return response()->json([
            'message' => 'Dossier supprimé avec succès'
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_dossiers' => Dossier::count(),
            'dossiers_en_cours' => Dossier::where('statut', 'EN_COURS')->count(),
            'dossiers_archives' => Dossier::where('statut', 'ARCHIVE')->count(),
            'mes_dossiers' => $user->isEmploye()
                ? Dossier::where('assigned_to', $user->id)->count()
                : null
        ];

        $recentDossiers = Dossier::with(['entreprise', 'assignedUser'])
            ->when($user->isEmploye(), function($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_dossiers' => $recentDossiers
        ]);
    }
}

