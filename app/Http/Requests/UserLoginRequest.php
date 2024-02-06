<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'name' => ['required', 'string']
        ];
    }

    public function messages()
    {
        return [
            "email.required" => "El correo electrónico es obligatorio.",
            "email.email" => "El correo electrónico no es válido",
            "password.required" => "La contraseña es obligatoria.",
            "name.required" => "El nombre es obligatorio.",
            "name.string" => "El nombre debe ser un texto",
            "password.string" => "La contraseña debe ser un texto",
        ];
    }
}
