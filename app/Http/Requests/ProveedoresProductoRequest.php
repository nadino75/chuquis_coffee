<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedoresProductoRequest extends FormRequest
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
			'proveedore_id' => 'required',
			'producto_id' => 'required',
			'cantidad' => 'required',
			'precio' => 'required',
			'fecha_compra' => 'required',
			'marca_id' => 'required',
        ];
    }
}
