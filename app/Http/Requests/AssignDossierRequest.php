<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignDossierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'assigned_to' => 'required|exists:users,id',
            'priorite' => 'nullable|in:BASSE,NORMALE,HAUTE,URGENTE',
            'date_limite' => 'nullable|date|after:today',
            'commentaire' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'assigned_to.required' => 'Veuillez sélectionner un utilisateur',
            'assigned_to.exists' => 'L\'utilisateur sélectionné n\'existe pas',
            'priorite.in' => 'La priorité doit être : BASSE, NORMALE, HAUTE ou URGENTE',
            'date_limite.date' => 'La date limite doit être une date valide',
            'date_limite.after' => 'La date limite doit être postérieure à aujourd\'hui',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 500 caractères',
        ];
    }
}
