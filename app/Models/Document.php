<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'taille',
        'chemin',
        'dossier_id',
        'uploaded_by',
        'is_encrypted',
    ];
    protected $casts = [
        'is_encrypted' => 'boolean',
        'taille' => 'integer',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormatSizeAttriobute()
    {
        $bytes = $this->taille;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->nom, PATHINFO_EXTENSION);
    }
}
