<?php
// app/Policies/ClasseurPolicy.php

namespace App\Policies;

use App\Models\Classeur;
use App\Models\User;

class ClasseurPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Classeur $classeur)
    {
        // Si le classeur est privé, seul le créateur peut le voir
        if ($classeur->est_prive) {
            return $user->id === $classeur->created_by;
        }

        // Sinon, tout le monde peut voir
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Classeur $classeur)
    {
        // PDG et Secrétaire peuvent modifier tous les classeurs
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Le créateur peut modifier son classeur
        return $user->id === $classeur->created_by;
    }

    public function delete(User $user, Classeur $classeur)
    {
        // PDG et Secrétaire peuvent supprimer tous les classeurs
        if ($user->isPDG() || $user->isSecretaire()) {
            return true;
        }

        // Le créateur peut supprimer son classeur s'il est vide
        return $user->id === $classeur->created_by && $classeur->dossiers()->count() === 0;
    }

    public function makePrivate(User $user, Classeur $classeur)
    {
        // Seul le créateur peut rendre son classeur privé
        return $user->id === $classeur->created_by;
    }
}
