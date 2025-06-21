<?php

namespace App\Services;

use App\Models\HistoriqueAction;
use App\Models\Dossier;
use App\Models\User;

class HistoriqueService
{
    public function logAction(Dossier $dossier, User $user, string $action, string $description = null)
    {
        return HistoriqueAction::create([
            'dossier_id' => $dossier->id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description
        ]);
    }

    public function getDossierHistory(Dossier $dossier, $limit = null)
    {
        $query = HistoriqueAction::where('dossier_id', $dossier->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getUserHistory(User $user, $limit = null)
    {
        $query = HistoriqueAction::where('user_id', $user->id)
            ->with(['dossier.entreprise'])
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getRecentActions($limit = 20)
    {
        return HistoriqueAction::with(['user', 'dossier.entreprise'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getActionsByType(string $action, $limit = null)
    {
        $query = HistoriqueAction::where('action', $action)
            ->with(['user', 'dossier'])
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getActionsByDateRange($startDate, $endDate)
    {
        return HistoriqueAction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'dossier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStatsForPeriod($startDate, $endDate)
    {
        $actions = HistoriqueAction::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get();

        return $actions->pluck('count', 'action')->toArray();
    }

    public function cleanOldHistory($days = 365)
    {
        return HistoriqueAction::where('created_at', '<', now()->subDays($days))
            ->where('action', '!=', 'ARCHIVE') // Garder les actions d'archivage
            ->delete();
    }
}
