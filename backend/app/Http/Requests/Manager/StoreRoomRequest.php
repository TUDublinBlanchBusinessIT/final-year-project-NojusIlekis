<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:rooms,name'],
            'age_band'    => ['nullable', 'string', 'max:50'],
            'capacity'    => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
