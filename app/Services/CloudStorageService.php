<?php
// app/Services/CloudStorageService.php

namespace App\Services;

use App\Models\Dossier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudStorageService
{
    protected $defaultDisk;

    public function __construct()
    {
        $this->defaultDisk = config('filesystems.cloud', 's3');
    }

    public function uploadToCloud($encryptedData, Dossier $dossier, $disk = null)
    {
        $disk = $disk ?: $this->defaultDisk;

        $fileName = 'archives/' . date('Y/m/d') . '/' . $dossier->id . '_' . Str::random(10) . '.encrypted';

        try {
            Storage::disk($disk)->put($fileName, $encryptedData);

            return [
                'disk' => $disk,
                'path' => $fileName,
                'url' => Storage::disk($disk)->url($fileName),
                'size' => strlen($encryptedData)
            ];
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'upload vers le cloud: ' . $e->getMessage());
        }
    }

    public function downloadFromCloud($cloudPath, $disk = null)
    {
        $disk = $disk ?: $this->defaultDisk;

        try {
            if (!Storage::disk($disk)->exists($cloudPath)) {
                throw new \Exception('Fichier non trouvé dans le cloud');
            }

            return Storage::disk($disk)->get($cloudPath);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors du téléchargement depuis le cloud: ' . $e->getMessage());
        }
    }

    public function deleteFromCloud($cloudPath, $disk = null)
    {
        $disk = $disk ?: $this->defaultDisk;

        try {
            if (Storage::disk($disk)->exists($cloudPath)) {
                Storage::disk($disk)->delete($cloudPath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la suppression du cloud: ' . $e->getMessage());
        }
    }

    public function getAvailableDisks()
    {
        return [
            's3' => 'AWS S3',
            'gcs' => 'Google Cloud Storage',
            'dropbox' => 'Dropbox'
        ];
    }

    public function testConnection($disk = null)
    {
        $disk = $disk ?: $this->defaultDisk;

        try {
            $testFile = 'test_connection_' . time() . '.txt';
            Storage::disk($disk)->put($testFile, 'Test de connexion');

            $exists = Storage::disk($disk)->exists($testFile);

            if ($exists) {
                Storage::disk($disk)->delete($testFile);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

