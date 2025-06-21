<?php
// app/Http/Requests/StoreDossierRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDossierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'entreprise_id' => 'required|exists:entreprises,id',
            'classeur_id' => 'required|exists:classeurs,id',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du dossier est obligatoire',
            'nom.max' => 'Le nom du dossier ne peut pas dépasser 255 caractères',
            'entreprise_id.required' => 'L\'entreprise est obligatoire',
            'entreprise_id.exists' => 'L\'entreprise sélectionnée n\'existe pas',
            'classeur_id.required' => 'Le classeur est obligatoire',
            'classeur_id.exists' => 'Le classeur sélectionné n\'existe pas',
        ];
    }
}
