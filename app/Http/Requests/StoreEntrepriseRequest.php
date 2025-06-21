<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntrepriseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255|unique:entreprises,nom',
            'email' => 'nullable|email|max:255|unique:entreprises,email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'secteur_activite' => 'nullable|string|max:255',
            'numero_registre' => 'nullable|string|max:100|unique:entreprises,numero_registre',
            'statut' => 'nullable|in:ACTIVE,INACTIVE,SUSPENDUE',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom de l\'entreprise est obligatoire',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'nom.unique' => 'Une entreprise avec ce nom existe déjà',
            'email.email' => 'L\'adresse email doit être valide',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'adresse.max' => 'L\'adresse ne peut pas dépasser 500 caractères',
            'secteur_activite.max' => 'Le secteur d\'activité ne peut pas dépasser 255 caractères',
            'numero_registre.unique' => 'Ce numéro de registre est déjà utilisé',
            'statut.in' => 'Le statut doit être : ACTIVE, INACTIVE ou SUSPENDUE',
        ];
    }
}
