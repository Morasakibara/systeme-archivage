<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'description',
        'statut',
        'entreprise_id',
        'classeur_id',
        'created_by',
        'assigned_to',
        'archived_at',
        'cloud_path',
        'is_encrypted',
    ];
    protected $casts = [
        'archived_at' => 'datetime',
        'is_encrypted' => 'boolean',
    ];
    
    //Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }
    public function classeur()
    {
        return $this->belongsTo(Classeur::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function historiqueActions()
    {
        return $this->hasMany(HistoriqueAction::class);
    }

    //Scopes

    public function scopeActive(Builder $query)
    {
        return $query->where('statut', '!=', 'ARCHIVE');
    }
    public function scopeAssignedTo(Builder $query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
    public function scopeByStatus(Builder $query, $status)
    {
        return $query->where('statut', $status);
    }

    //Methodes utilitaires
    public function isArchived()
    {
        return $this->statut === 'ARCHIVE';
    }
    public function canBeArchived()
    {
        return in_array($this->statut, ['TERMINE']);
    }
    public function getTotalDocumentsAttribute()
    {
        return $this->documents()->count();
    }
    public function getTotalSizeAttribute()
    {
        return $this->documents()->sum('taille');
    }
}
