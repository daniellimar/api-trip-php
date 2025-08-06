<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Enums\TravelRequestStatus;
use App\Notifications\TravelRequestStatusChanged;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TravelRequestResource;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\{StoreTravelRequest, UpdateTravelRequest};

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="TravelRequest API"
 * )
 *
 * @OA\Tag(
 *     name="Travel Requests",
 *     description="API para gerenciar solicitações de viagem"
 * )
 */
class TravelRequestController extends Controller
{
    private $user;
    private $isAdmin;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->isAdmin = $this->user?->hasRole('admin');
    }

    /**
     * Listar todas as solicitações de viagem.
     *
     * @OA\Get(
     *     path="/api/travel-requests",
     *     summary="Listar todas as solicitações de viagem",
     *     tags={"Travel Requests"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status da solicitação",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="destination",
     *         in="query",
     *         description="Filtrar por destino",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_range",
     *         in="query",
     *         description="Data inicial para filtro",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_range",
     *         in="query",
     *         description="Data final para filtro",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Termo de busca (nome do requerente, destino ou ID)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de itens por página (default 10)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de solicitações de viagem retornada com sucesso",
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = TravelRequest::query();

        if (!$this->isAdmin) {
            $query->where('user_id', $this->user->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('destination')) {
            $query->where('destination', $request->destination);
        }
        if ($request->filled('start_range') && $request->filled('end_range')) {
            $query->whereDate('start_date', '>=', $request->start_range);
            $query->whereDate('end_date', '<=', $request->end_range);
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'LIKE', "%{$search}%")
                    ->orWhere('destination', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);

        return TravelRequestResource::collection($query->paginate($perPage));
    }

    /**
     * Criar uma nova solicitação de viagem.
     *
     * @OA\Post(
     *     path="/api/travel-requests",
     *     summary="Criar uma nova solicitação de viagem",
     *     tags={"Travel Requests"},
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitação criada com sucesso",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validação falhou"
     *     )
     * )
     */
    public function store(StoreTravelRequest $request)
    {
        $validated = $request->validated();
        $validated['status'] = TravelRequestStatus::SOLICITADO;

        $travelRequest = $this->user->travelRequests()->create($validated);

        return new TravelRequestResource($travelRequest);
    }

    /**
     * Exibir solicitação específica.
     *
     * @OA\Get(
     *     path="/api/travel-requests/{travelRequest}",
     *     summary="Exibir os detalhes de uma solicitação de viagem",
     *     tags={"Travel Requests"},
     *     @OA\Parameter(
     *         name="travelRequest",
     *         in="path",
     *         description="ID da solicitação de viagem",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação retornada com sucesso",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Não autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Solicitação não encontrada"
     *     )
     * )
     */
    public function show(TravelRequest $travelRequest): TravelRequestResource
    {
        $this->authorizeUserAccess($travelRequest);

        return new TravelRequestResource($travelRequest);
    }

    /**
     * Atualizar solicitação de viagem.
     *
     * @OA\Put(
     *     path="/api/travel-requests/{travelRequest}",
     *     summary="Atualizar uma solicitação de viagem",
     *     tags={"Travel Requests"},
     *     @OA\Parameter(
     *         name="travelRequest",
     *         in="path",
     *         description="ID da solicitação",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação atualizada com sucesso",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Não autorizado para mudar status"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Status inválido"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor"
     *     )
     * )
     */
    public function update(UpdateTravelRequest $request, TravelRequest $travelRequest)
    {
        $validated = $request->validated();

        if (isset($validated['status'])) {
            if (!$this->isAdmin) {
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

        $this->authorizeUserAccess($travelRequest);

        if ($travelRequest->update($validated)) {
            if (isset($validated['status']) && in_array($validated['status'], ['aprovado', 'cancelado'])) {
                $travelRequest->user->notify(
                    new TravelRequestStatusChanged($validated['status'], $travelRequest)
                );
            }
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

    /**
     * Remover solicitação de viagem.
     *
     * @OA\Delete(
     *     path="/api/travel-requests/{travelRequest}",
     *     summary="Remover uma solicitação de viagem",
     *     tags={"Travel Requests"},
     *     @OA\Parameter(
     *         name="travelRequest",
     *         in="path",
     *         description="ID da solicitação",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação removida com sucesso",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Não é possível remover solicitação aprovada"
     *     )
     * )
     */
    public function destroy(TravelRequest $travelRequest): JsonResponse
    {
        $this->authorizeUserAccess($travelRequest);

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

    /**
     * Cancelar uma solicitação de viagem.
     *
     * @OA\Post(
     *     path="/api/travel-requests/{travelRequest}/cancel",
     *     summary="Cancelar uma solicitação de viagem",
     *     tags={"Travel Requests"},
     *     @OA\Parameter(
     *         name="travelRequest",
     *         in="path",
     *         description="ID da solicitação a ser cancelada",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação cancelada com sucesso",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Não é possível cancelar solicitação aprovada"
     *     )
     * )
     */
    public function cancel(TravelRequest $travelRequest): JsonResponse
    {
        $this->authorizeUserAccess($travelRequest);

        if ($travelRequest->status === TravelRequestStatus::APROVADO) {
            return response()->json([
                'message' => 'Não é possível cancelar um pedido que já foi aprovado.'
            ], 403);
        }

        $travelRequest->update(['status' => TravelRequestStatus::CANCELADO]);

        $travelRequest->user->notify(
            new TravelRequestStatusChanged('cancelado', $travelRequest)
        );

        return response()->json([
            'id' => $travelRequest->id,
            'status' => TravelRequestStatus::CANCELADO,
            'message' => 'Solicitação de viagem cancelada com sucesso.'
        ]);
    }

    /**
     * Verifica se o usuário autenticado pode manipular a solicitação.
     */
    private function authorizeUserAccess(TravelRequest $travelRequest): void
    {
        if (!$this->isAdmin && $travelRequest->user_id !== $this->user->id) {
            abort(403, 'Você não tem permissão para acessar esta solicitação.');
        }
    }
}
