<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambia a false si quieres control de permisos
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente') ? $this->route('cliente')->id : null;

        return [
            'ci' => 'required|string|max:20|unique:clientes,ci,' . $clienteId,
            'NIT' => 'nullable|string|max:20|unique:clientes,NIT,' . $clienteId,
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'sexo' => 'required|in:Masculino,Femenino',
            'telefono' => 'nullable|string|max:15',
            'celular' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ci.required' => 'El CI es obligatorio.',
            'ci.unique' => 'Este CI ya está registrado.',
            'nombres.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'sexo.required' => 'El sexo es obligatorio.',
            'sexo.in' => 'El sexo debe ser Masculino o Femenino.',
            'correo.email' => 'El correo debe ser un email válido.',
        ];
    }
}


