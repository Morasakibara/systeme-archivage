<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'entreprise_id',
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function dossiers()
    {
        return $this->hasMany(Dossier::class);
    }
    public function getTotalDocumentsAttribute()
    {
        return $this->dossiers()->count();
    }
    public function getDossiersArchivedAttribute()
    {
        return $this->dossiers()->where('statut', 'ARCHIVE')->count();
    }
}
