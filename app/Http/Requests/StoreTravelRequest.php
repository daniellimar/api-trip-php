<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTravelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'applicant_name' => [
                'required',
                Rule::unique('travel_requests')
                    ->where(function ($query) {
                        return $query
                            ->where('destination', $this->destination)
                            ->where('start_date', $this->start_date)
                            ->where('end_date', $this->end_date);
                    })
            ],
        ];
    }

    public function messages()
    {
        return [
            'destination.required' => 'O campo "Destino" é obrigatório.',
            'destination.string' => 'O campo "Destino" deve ser uma string.',
            'destination.max' => 'O campo "Destino" não pode ter mais de 255 caracteres.',

            'start_date.required' => 'O campo "Data de Início" é obrigatório.',
            'start_date.date' => 'O campo "Data de Início" deve ser uma data válida.',
            'start_date.after_or_equal' => 'A "Data de Início" deve ser igual ou posterior à data de hoje.',

            'end_date.required' => 'O campo "Data de Término" é obrigatório.',
            'end_date.date' => 'O campo "Data de Término" deve ser uma data válida.',
            'end_date.after_or_equal' => 'A "Data de Término" deve ser igual ou posterior à "Data de Início".',

            'status.required' => 'O campo "Status" é obrigatório.',
            'status.in' => 'O campo "Status" deve ser um dos seguintes valores: solicitado, aprovado, cancelado.',

            'applicant_name.required' => 'O campo "Nome do Solicitante" é obrigatório.',
            'applicant_name.unique' => 'Já existe uma solicitação de viagem registrada com os mesmos dados informados.',
        ];
    }
}
