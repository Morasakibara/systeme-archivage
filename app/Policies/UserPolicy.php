<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        // PDG et Secrétaire peuvent voir tous les utilisateurs
        return $user->isPDG() || $user->isSecretaire();
    }

    public function view(User $user, User $model)
    {
        // Un utilisateur peut voir son propre profil
        if ($user->id === $model->id) {
            return true;
        }

        // PDG et Secrétaire peuvent voir tous les profils
        return $user->isPDG() || $user->isSecretaire();
    }

    public function create(User $user)
    {
        // Seul le PDG peut créer des utilisateurs
        return $user->isPDG();
    }

    public function update(User $user, User $model)
    {
        // Un utilisateur peut modifier son propre profil (limité)
        if ($user->id === $model->id) {
            return true;
        }

        // Seul le PDG peut modifier les autres utilisateurs
        return $user->isPDG();
    }

    public function delete(User $user, User $model)
    {
        // On ne peut pas se supprimer soi-même
        if ($user->id === $model->id) {
            return false;
        }

        // Seul le PDG peut supprimer des utilisateurs
        return $user->isPDG();
    }

    public function changeRole(User $user, User $model)
    {
        // On ne peut pas changer son propre rôle
        if ($user->id === $model->id) {
            return false;
        }

        // Seul le PDG peut changer les rôles
        return $user->isPDG();
    }

    public function activate(User $user, User $model)
    {
        // On ne peut pas se désactiver soi-même
        if ($user->id === $model->id) {
            return false;
        }

        // Seul le PDG peut activer/désactiver
        return $user->isPDG();
    }
}
