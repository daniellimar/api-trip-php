<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'applicant_name' => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'sometimes|in:solicitado,aprovado,cancelado',
        ];
    }

    public function messages(): array
    {
        return [
            'applicant_name.string' => 'O campo "Nome do Solicitante" deve ser um texto.',
            'applicant_name.max' => 'O campo "Nome do Solicitante" não pode ter mais de 255 caracteres.',

            'destination.string' => 'O campo "Destino" deve ser um texto.',
            'destination.max' => 'O campo "Destino" não pode ter mais de 255 caracteres.',

            'start_date.date' => 'O campo "Data de Início" deve ser uma data válida.',
            'start_date.after_or_equal' => 'A "Data de Início" deve ser igual ou posterior à data de hoje.',

            'end_date.date' => 'O campo "Data de Término" deve ser uma data válida.',
            'end_date.after_or_equal' => 'A "Data de Término" deve ser igual ou posterior à "Data de Início".',

            'status.in' => 'O campo "Status" precisa ser um dos seguintes valores: solicitado, aprovado ou cancelado.',
        ];
    }
}
