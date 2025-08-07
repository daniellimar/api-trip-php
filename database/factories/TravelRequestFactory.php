<?php

namespace Database\Factories;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TravelRequestStatus;

class TravelRequestFactory extends Factory
{
    protected $model = TravelRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination' => $this->faker->city,
            'applicant_name' => $this->faker->name,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => TravelRequestStatus::SOLICITADO,
        ];
    }
}
