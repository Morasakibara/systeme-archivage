<?php
// app/Observers/DocumentObserver.php

namespace App\Observers;

use App\Models\Document;
use App\Services\NotificationService;

class DocumentObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(Document $document): void
    {
        // Log d'ajout de document
        $document->dossier->activites()->create([
            'action' => 'AJOUT_DOCUMENT',
            'description' => "Document '{$document->nom}' ajouté",
            'user_id' => auth()->id(),
            'metadata' => [
                'document_id' => $document->id,
                'document_nom' => $document->nom,
            ],
        ]);

        // Notification
        $this->notificationService->notifyDocumentAdded($document->dossier, $document);
    }

    public function deleting(Document $document): void
    {
        // Log de suppression de document
        $document->dossier->activites()->create([
            'action' => 'SUPPRESSION_DOCUMENT',
            'description' => "Document '{$document->nom}' supprimé",
            'user_id' => auth()->id(),
            'metadata' => [
                'document_id' => $document->id,
                'document_nom' => $document->nom,
            ],
        ]);
    }
}
