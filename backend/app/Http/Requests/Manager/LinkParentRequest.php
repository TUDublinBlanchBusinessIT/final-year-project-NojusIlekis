<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class LinkParentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id'         => ['required', 'exists:users,id'],
            'relationship_type' => ['required', 'in:mother,father,guardian,grandparent,other'],
            'legal_guardian'    => ['boolean'],
        ];
    }
}
