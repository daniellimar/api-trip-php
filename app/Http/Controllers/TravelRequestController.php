<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Models\TravelRequest;
use App\Http\Resources\TravelRequestResource;
use Illuminate\Http\Request;

class TravelRequestController extends Controller
{
    public function index(Request $request)
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

    public function store(StoreTravelRequest $request)
    {
        $validated = $request->validated();

        $travelRequest = TravelRequest::create($validated);

        return new TravelRequestResource($travelRequest);
    }

    public function show(TravelRequest $travelRequest)
    {
        return new TravelRequestResource($travelRequest);
    }

    public function update(UpdateTravelRequest $request, TravelRequest $travelRequest)
    {
        $validated = $request->validated();

        $travelRequest->update($validated);

        return new TravelRequestResource($travelRequest);
    }

    public function destroy(TravelRequest $travelRequest)
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
        ], 200);
    }
}
