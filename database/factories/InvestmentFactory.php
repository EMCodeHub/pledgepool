<?php

namespace Database\Factories;

use App\Models\Investment;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvestmentFactory extends Factory
{
    protected $model = Investment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Asegúrate de tener la relación con el usuario
            'campaign_id' => Campaign::factory(), // Asegúrate de tener la relación con la campaña
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'status' => 'reserved', // O el estado adecuado
        ];
    }
}
