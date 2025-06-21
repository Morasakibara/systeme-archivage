<?php

namespace App\Observers;

use App\Models\Dossier;
use App\Services\NotificationService;

class DossierObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function creating(Dossier $dossier): void
    {
        $dossier->created_by = auth()->id();
        $dossier->statut = $dossier->statut ?? 'EN_COURS';
    }

    public function created(Dossier $dossier): void
    {
        // Log de création
        $dossier->activites()->create([
            'action' => 'CREATION',
            'description' => 'Dossier créé',
            'user_id' => auth()->id(),
        ]);
    }

    public function updating(Dossier $dossier): void
    {
        // Détecter les changements importants
        if ($dossier->isDirty('assigned_to') && $dossier->assigned_to) {
            $dossier->date_assignation = now();
        }

        if ($dossier->isDirty('statut')) {
            $dossier->date_modification_statut = now();
        }
    }

    public function updated(Dossier $dossier): void
    {
        // Notifications pour les changements
        if ($dossier->wasChanged('assigned_to') && $dossier->assignedUser) {
            $this->notificationService->notifyDossierAssigned($dossier, $dossier->assignedUser);
        }

        if ($dossier->wasChanged('statut')) {
            $originalStatus = $dossier->getOriginal('statut');
            $this->notificationService->notifyDossierStatusChanged(
                $dossier,
                $originalStatus,
                $dossier->statut
            );
        }
    }

    public function deleting(Dossier $dossier): void
    {
        // Supprimer les documents associés
        foreach ($dossier->documents as $document) {
            app(DocumentService::class)->deleteDocument($document);
        }

        // Log de suppression
        $dossier->activites()->create([
            'action' => 'SUPPRESSION',
            'description' => 'Dossier supprimé',
            'user_id' => auth()->id(),
        ]);
    }
}
