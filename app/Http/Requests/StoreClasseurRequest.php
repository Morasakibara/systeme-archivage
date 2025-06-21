<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClasseurRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'couleur' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'est_prive' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du classeur est obligatoire',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'description.max' => 'La description ne peut pas dépasser 500 caractères',
            'couleur.regex' => 'La couleur doit être au format hexadécimal (#RRGGBB)',
            'est_prive.boolean' => 'Le champ privé doit être vrai ou faux',
        ];
    }
}

