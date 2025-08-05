<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Enums\TravelRequestStatus;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TravelRequestResource;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\{StoreTravelRequest, UpdateTravelRequest};

class TravelRequestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = TravelRequest::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('destination')) {
            $query->where('destination', $request->destination);
        }
        if ($request->filled('start_range')) {
            $query->whereDate('start_date', '>=', $request->start_range);
        }
        if ($request->filled('end_range')) {
            $query->whereDate('end_date', '<=', $request->end_range);
        }

        $travelRequests = $query->get();

        return TravelRequestResource::collection($travelRequests);
    }

    public function store(StoreTravelRequest $request): TravelRequestResource
    {
        $validated = $request->validated();

        $travelRequest = TravelRequest::create($validated);

        return new TravelRequestResource($travelRequest);
    }

    public function show(TravelRequest $travelRequest): TravelRequestResource
    {
        return new TravelRequestResource($travelRequest);
    }

    public function update(UpdateTravelRequest $request, TravelRequest $travelRequest)
    {
        $validated = $request->validated();

        if (isset($validated['status'])) {
            if (!Auth::user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Você não tem permissão para alterar o status da solicitação.'
                ], 403);
            }

            if ($validated['status'] === 'cancelado') {
                return $this->cancel($travelRequest);
            }

            if (!in_array($validated['status'], ['aprovado', 'cancelado'])) {
                return response()->json([
                    'message' => 'Status inválido. Use "aprovado" ou "cancelado".'
                ], 422);
            }
        }

        if ($travelRequest->update($validated)) {
            return response()->json([
                'id' => $travelRequest->id,
                'status' => $travelRequest->status,
                'message' => 'Solicitação de viagem atualizada com sucesso.'
            ]);
        }

        return response()->json([
            'message' => 'Erro ao atualizar a solicitação de viagem.'
        ], 500);
    }

    public function destroy(TravelRequest $travelRequest): JsonResponse
    {
        if ($travelRequest->status->isApproved()) {
            return response()->json([
                'message' => 'Não é possível remover um pedido que já foi aprovado.'
            ], 403);
        }

        $travelRequest->delete();

        return response()->json([
            'id' => $travelRequest->id,
            'deleted' => true,
            'message' => 'Solicitação de viagem removida com sucesso.'
        ]);
    }

    public function cancel(TravelRequest $travelRequest): JsonResponse
    {
        if ($travelRequest->status === TravelRequestStatus::APROVADO) {
            return response()->json([
                'message' => 'Não é possível cancelar um pedido que já foi aprovado.'
            ], 403);
        }

        $travelRequest->update(['status' => TravelRequestStatus::CANCELADO]);

        return response()->json([
            'id' => $travelRequest->id,
            'status' => TravelRequestStatus::CANCELADO,
            'message' => 'Solicitação de viagem cancelada com sucesso.'
        ]);
    }
}
