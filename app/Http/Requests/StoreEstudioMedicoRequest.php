<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEstudioMedicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tu lógica de permisos está perfecta aquí
        return Auth::user()->hasAccess('estudios_medicos');
    }

    public function rules(): array
    {
        return [
            // Cambiado a paciente_id y validamos que exista en la tabla pacientes
            'paciente_id'           => ['required', 'integer', 'exists:pacientes,id'],

            'tipo_estudio'          => ['required', 'string', 'max:150'], // Ej: Radiografía, Endoscopia
            'fecha'                 => ['required', 'date'],
            'resultado'             => ['nullable', 'string'],

            // Cambiado a medico_solicitante_id y validamos que exista en la tabla empleados
            'medico_solicitante_id' => ['required', 'integer', 'exists:empleados,id'],
        ];
    }
}