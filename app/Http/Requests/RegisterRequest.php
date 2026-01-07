<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'role' => ['required', 'in:client,specialist'],

            'criminal_record_file_url' => [
                'required_if:role,specialist',
                'string',
                'max:255'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            // Email
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            // Phone
            'phone.string' => 'El teléfono debe ser un texto válido.',
            'phone.max' => 'El teléfono no puede superar los 20 caracteres.',

            // Password
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            // Role
            'role.required' => 'El tipo de usuario es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',

            // Criminal Record
            'criminal_record_file_url.required_if' =>
            'El documento de antecedentes penales es obligatorio para especialistas.',
            'criminal_record_file_url.string' =>
            'El documento de antecedentes debe ser un texto válido.',
            'criminal_record_file_url.max' =>
            'La URL del documento no puede superar los 255 caracteres.',
        ];
    }
}
