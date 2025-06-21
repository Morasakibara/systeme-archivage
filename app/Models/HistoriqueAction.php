<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueAction extends Model
{
    use HasFactory;

    protected $table = 'historique_actions';

    protected $fillable = [
        'dossier_id',
        'user_id',
        'action',
        'description',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedActionAttribute()
    {
        $actions = [
            'CREATE' => 'Création',
            'UPDATE' => 'Modification',
            'ASSIGN' => 'Attribution',
            'ARCHIVE' => 'Archivage',
            'DOCUMENT_UPLOAD' => 'Ajout de document',
            'DOCUMENT_DELETE' => 'Suppression de document',
            'STATUS_CHANGE' => 'Changement de statut',
        ];

        return $actions[$this->action] ?? $this->action;
    }

}
