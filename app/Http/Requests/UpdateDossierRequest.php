<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDossierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'statut' => 'sometimes|required|in:EN_COURS,EN_REVISION,TERMINE,ARCHIVE',
            'entreprise_id' => 'sometimes|required|exists:entreprises,id',
            'classeur_id' => 'sometimes|required|exists:classeurs,id',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du dossier est obligatoire',
            'nom.max' => 'Le nom du dossier ne peut pas dépasser 255 caractères',
            'statut.in' => 'Le statut doit être : EN_COURS, EN_REVISION, TERMINE ou ARCHIVE',
            'entreprise_id.exists' => 'L\'entreprise sélectionnée n\'existe pas',
            'classeur_id.exists' => 'Le classeur sélectionné n\'existe pas',
        ];
    }
}

