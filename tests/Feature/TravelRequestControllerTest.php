<?php

namespace Tests\Feature;

use App\Enums\TravelRequestStatus;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;
use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function user_can_list_own_travel_requests()
    {
        TravelRequest::factory()->count(2)->create(['user_id' => $this->user->id]);
        TravelRequest::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/travel-requests');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function user_can_create_travel_request()
    {
        $payload = [
            'applicant_name' => 'João da Silva',
            'destination' => 'Brasília',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-requests', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('travel_requests', [
            'applicant_name' => 'João da Silva',
            'user_id' => $this->user->id,
            'status' => TravelRequestStatus::SOLICITADO
        ]);
    }

    /** @test */
    public function user_can_view_own_travel_request()
    {
        $request = TravelRequest::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/travel-requests/{$request->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $request->id]);
    }

    /** @test */
    public function admin_can_approve_travel_request()
    {
        $request = TravelRequest::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/travel-requests/{$request->id}", [
                'status' => 'aprovado',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('travel_requests', [
            'id' => $request->id,
            'status' => TravelRequestStatus::APROVADO
        ]);
    }
}
