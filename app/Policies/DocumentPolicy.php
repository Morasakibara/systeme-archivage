<?php
// app/Policies/DocumentPolicy.php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document)
    {
        // Utilise la politique du dossier parent
        return $user->can('view', $document->dossier);
    }

    public function download(User $user, Document $document)
    {
        // Même règle que pour voir
        return $this->view($user, $document);
    }

    public function update(User $user, Document $document)
    {
        // PDG et Secrétaire peuvent modifier
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Le créateur peut modifier ses documents
        return $user->id === $document->uploaded_by;
    }

    public function delete(User $user, Document $document)
    {
        // PDG et Secrétaire peuvent supprimer
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Le créateur peut supprimer ses documents
        return $user->id === $document->uploaded_by;
    }
}
