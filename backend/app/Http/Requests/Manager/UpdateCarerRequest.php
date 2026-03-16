<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('carer');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', "unique:users,email,{$id}"],
            'password' => ['nullable', 'min:8', 'confirmed'],
            'room_id'  => ['nullable', 'exists:rooms,id'],
        ];
    }
}
