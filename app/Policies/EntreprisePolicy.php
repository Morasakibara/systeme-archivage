<?php

namespace App\Policies;

use App\Models\Entreprise;
use App\Models\User;

class EntreprisePolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Entreprise $entreprise)
    {
        return true;
    }

    public function create(User $user)
    {
        // PDG et Secrétaire peuvent créer des entreprises
        return $user->isPDG() || $user->isSecretaire();
    }

    public function update(User $user, Entreprise $entreprise)
    {
        // PDG et Secrétaire peuvent modifier
        return $user->isPDG() || $user->isSecretaire();
    }

    public function delete(User $user, Entreprise $entreprise)
    {
        // Seul le PDG peut supprimer des entreprises
        return $user->isPDG();
    }

    public function changeStatus(User $user, Entreprise $entreprise)
    {
        // PDG et Secrétaire peuvent changer le statut
        return $user->isPDG() || $user->isSecretaire();
    }
}
