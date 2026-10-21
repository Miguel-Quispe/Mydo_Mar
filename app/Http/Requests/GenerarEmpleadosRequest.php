<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerarEmpleadosRequest extends FormRequest
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
            'empresa_rol_id' => 'required|integer|exists:empresa_roles,id_empresa_rol',
            'prefijo_usuario' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'cantidad' => 'required|integer|min:1|max:20',
            'dominio_email' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'empresa_rol_id.required' => 'Debe seleccionar un subrol operativo de fábrica.',
            'empresa_rol_id.exists' => 'El subrol seleccionado no es válido.',
            'prefijo_usuario.required' => 'El prefijo de usuario es obligatorio.',
            'prefijo_usuario.regex' => 'El prefijo solo puede contener letras, números, guiones y guiones bajos.',
            'cantidad.required' => 'Indique la cantidad de cuentas a generar.',
            'cantidad.min' => 'Debe generar al menos 1 cuenta.',
            'cantidad.max' => 'El límite de generación por lote es de 20 cuentas.',
        ];
    }
}
