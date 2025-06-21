<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'nom',
        'prenom',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getFullNmaeAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
    public function isPDG()
    {
        return $this->role === 'PDG';
    }
    public function isSecretaire()
    {
        return $this->role === 'SECRETAIRE';
    }
    public function isEmploye()
    {
        return $this->role === 'EMPLOYE';
    }
    //Relation
    public function classeursCreated()
    {
        return $this->hasMany(Classeur::class, 'created_by');
    }
    public function dossiersCreated()
    {
        return $this->hasMany(Dossier::class, 'created_by');
    }
    public function dossiersAssigned()
    {
        return $this->hasMany(Dossier::class, 'assigned_to');
    }
    public function documentsUploaded()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }
    public function historiqueActions()
    {
        return $this->hasMany(HistoriqueAction::class);

    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
