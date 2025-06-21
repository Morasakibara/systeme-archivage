<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Dossier;
use App\Services\HistoriqueService;
use App\Services\ChiffrementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreDocumentRequest;


class DocumentController extends Controller
{
    protected $historiqueService;
    protected $chiffrementService;

    public function __construct(
        HistoriqueService $historiqueService,
        ChiffrementService $chiffrementService
        )
            {
                $this->middleware('auth:sanctum');
                $this->historiqueService = $historiqueService;
                 $this->chiffrementService = $chiffrementService;

            }
    public function index(Dossier $dossier)
    {
        $this->authorize('view', $dossier);

        $documents = $dossier->documents()
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($documents);
    }

public function store(StoreDocumentRequest $request, Dossier $dossier)
    {
        $this->authorize('addDocument', $dossier);

        $file = $request->file('document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'private');

        $document = Document::create([
            'nom' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'taille' => $file->getSize(),
            'chemin' => $filePath,
            'dossier_id' => $dossier->id,
            'uploaded_by' => $request->user()->id,
            'is_encrypted' => false
        ]);

        // Log de l'action
        $this->historiqueService->logAction(
            $dossier,
            $request->user(),
            'DOCUMENT_UPLOAD',
            "Document '{$document->nom}' ajouté"
        );

        return response()->json($document->load('uploader'), 201);
    }


    /**
     * Store a newly created resource in storage.
     */
     public function show(Document $document)
    {
        $this->authorize('view', $document->dossier);

        return response()->json($document->load(['dossier', 'uploader']));
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document->dossier);

        if (!Storage::disk('private')->exists($document->chemin)) {
            return response()->json(['message' => 'Fichier introuvable'], 404);
        }

        return Storage::disk('private')->download($document->chemin, $document->nom);
    }


 public function destroy(Document $document)
    {
        $this->authorize('deleteDocument', $document->dossier);

        // Supprimer le fichier physique
        if (Storage::disk('private')->exists($document->chemin)) {
            Storage::disk('private')->delete($document->chemin);
        }

        // Log de l'action
        $this->historiqueService->logAction(
            $document->dossier,
            auth()->user(),
            'DOCUMENT_DELETE',
            "Document '{$document->nom}' supprimé"
        );

        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }
}
