<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Dossier;

class NotificationService
{
    public function notifyPDG(Dossier $dossier, $message, $type = 'INFO')
    {
        $pdg = User::where('role', 'PDG')->first();

        if ($pdg) {
            return $this->createNotification(
                $pdg->id,
                'Nouveau dossier',
                $message . ': ' . $dossier->nom,
                $type
            );
        }

        return null;
    }

    public function notifyEmployee(Dossier $dossier, $message, $type = 'INFO')
    {
        if ($dossier->assigned_to) {
            return $this->createNotification(
                $dossier->assigned_to,
                'Attribution de dossier',
                $message . ': ' . $dossier->nom,
                $type
            );
        }

        return null;
    }

    public function notifyUser($userId, $titre, $message, $type = 'INFO')
    {
        return $this->createNotification($userId, $titre, $message, $type);
    }

    public function notifyMultipleUsers(array $userIds, $titre, $message, $type = 'INFO')
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = $this->createNotification($userId, $titre, $message, $type);
        }

        return $notifications;
    }

    public function notifyByRole($role, $titre, $message, $type = 'INFO')
    {
        $users = User::where('role', $role)->pluck('id')->toArray();
        return $this->notifyMultipleUsers($users, $titre, $message, $type);
    }

    protected function createNotification($userId, $titre, $message, $type)
    {
        return Notification::create([
            'user_id' => $userId,
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'is_read' => false
        ]);
    }

    public function markAsRead($notificationId, $userId = null)
    {
        $query = Notification::where('id', $notificationId);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->update(['is_read' => true]);
    }

    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function getUserNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function deleteOldNotifications($days = 30)
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }
}

