<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'sometimes|required|in:PDG,SECRETAIRE,EMPLOYE',
            'telephone' => 'nullable|string|max:20',
            'poste' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'L\'adresse email doit être valide',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'role.required' => 'Le rôle est obligatoire',
            'role.in' => 'Le rôle doit être : PDG, SECRETAIRE ou EMPLOYE',
            'telephone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'poste.max' => 'Le poste ne peut pas dépasser 255 caractères',
            'is_active.boolean' => 'Le statut actif doit être vrai ou faux',
        ];
    }
}
