<?php
// app/Http/Requests/BulkActionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:dossiers,id',
            'action' => 'required|in:archive,delete,assign,change_status',
            'assigned_to' => 'required_if:action,assign|exists:users,id',
            'status' => 'required_if:action,change_status|in:EN_COURS,EN_REVISION,TERMINE,ARCHIVE',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Veuillez sélectionner au moins un élément',
            'ids.array' => 'La sélection doit être un tableau',
            'ids.min' => 'Veuillez sélectionner au moins un élément',
            'ids.*.exists' => 'Un ou plusieurs éléments sélectionnés n\'existent pas',
            'action.required' => 'L\'action est obligatoire',
            'action.in' => 'Action non valide',
            'assigned_to.required_if' => 'Veuillez sélectionner un utilisateur',
            'assigned_to.exists' => 'L\'utilisateur sélectionné n\'existe pas',
            'status.required_if' => 'Le statut est obligatoire',
            'status.in' => 'Statut non valide',
        ];
    }
}
