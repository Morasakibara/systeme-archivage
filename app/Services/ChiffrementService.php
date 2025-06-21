<?php
// app/Services/ChiffrementService.php

namespace App\Services;

use App\Models\Dossier;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ChiffrementService
{
    public function chiffrerDossier(Dossier $dossier)
    {
        $data = [
            'dossier' => $dossier->toArray(),
            'documents' => $dossier->documents->map(function ($document) {
                $fileContent = null;
                if (Storage::disk('private')->exists($document->chemin)) {
                    $fileContent = base64_encode(Storage::disk('private')->get($document->chemin));
                }

                return [
                    'metadata' => $document->toArray(),
                    'content' => $fileContent
                ];
            }),
            'historique' => $dossier->historiqueActions->toArray(),
            'timestamp' => now()->toISOString()
        ];

        return Crypt::encryptString(json_encode($data));
    }

    public function dechiffrerDossier($encryptedData)
    {
        try {
            $decryptedData = Crypt::decryptString($encryptedData);
            return json_decode($decryptedData, true);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors du déchiffrement des données');
        }
    }

    public function chiffrerDocument($filePath)
    {
        if (!Storage::disk('private')->exists($filePath)) {
            throw new \Exception('Fichier non trouvé');
        }

        $fileContent = Storage::disk('private')->get($filePath);
        $encryptedContent = Crypt::encryptString($fileContent);

        $encryptedPath = $filePath . '.encrypted';
        Storage::disk('private')->put($encryptedPath, $encryptedContent);

        return $encryptedPath;
    }

    public function dechiffrerDocument($encryptedPath)
    {
        if (!Storage::disk('private')->exists($encryptedPath)) {
            throw new \Exception('Fichier chiffré non trouvé');
        }

        $encryptedContent = Storage::disk('private')->get($encryptedPath);
        return Crypt::decryptString($encryptedContent);
    }
}
