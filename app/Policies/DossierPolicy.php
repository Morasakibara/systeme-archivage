<?php
// app/Policies/DossierPolicy.php

namespace App\Policies;

use App\Models\Dossier;
use App\Models\User;

class DossierPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Dossier $dossier)
    {
        // PDG et Secrétaire peuvent voir tous les dossiers
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Employé peut voir les dossiers qu'il a créés ou qui lui sont attribués
        return $user->id === $dossier->created_by || $user->id === $dossier->assigned_to;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Dossier $dossier)
    {
        // PDG et Secrétaire peuvent modifier tous les dossiers
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Employé peut modifier les dossiers qui lui sont attribués
        return $user->id === $dossier->assigned_to;
    }

    public function delete(User $user, Dossier $dossier)
    {
        // Seuls PDG et Secrétaire peuvent supprimer
        return $user->isPDG() || $user->isSecretaire();
    }

    public function assign(User $user, Dossier $dossier)
    {
        // Seul le PDG peut attribuer des dossiers
        return $user->isPDG();
    }

    public function archive(User $user, Dossier $dossier)
    {
        // PDG et Secrétaire peuvent archiver
        return $user->isPDG() || $user->isSecretaire();
    }

    public function addDocument(User $user, Dossier $dossier)
    {
        // PDG et Secrétaire peuvent toujours ajouter
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Employé peut ajouter aux dossiers qui lui sont attribués et non archivés
        return $user->id === $dossier->assigned_to && !$dossier->isArchived();
    }

    public function deleteDocument(User $user, Dossier $dossier)
    {
        // PDG et Secrétaire peuvent supprimer
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Employé peut supprimer des documents des dossiers qui lui sont attribués
        return $user->id === $dossier->assigned_to && !$dossier->isArchived();
    }

    public function bulkAction(User $user)
    {
        // Seuls PDG et Secrétaire peuvent faire des actions en masse
        return $user->isPDG() || $user->isSecretaire();
    }
}
