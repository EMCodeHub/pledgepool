<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'owner_id' => \App\Models\User::factory(), // Usamos una fábrica de usuarios para este campo
            'name' => $this->faker->company,
            'amount' => $this->faker->randomFloat(2, 1000, 10000), // Generamos un número decimal para la cantidad
            'contract_fee' => $this->faker->randomFloat(2, 1, 10), // Genera un número de tarifa de contrato
            'interest_rate' => $this->faker->randomFloat(2, 1, 15), // Genera una tasa de interés
            'campaign_type' => $this->faker->randomElement(['normal', 'auction']), // Esto está bien, lo dejas como está
            'deadline' => $this->faker->date(), // Generamos una fecha para el plazo
            'loan_duration' => $this->faker->numberBetween(1, 12), // Duración del préstamo en meses
            'target_amount' => $this->faker->randomFloat(2, 5000, 50000), // Monto objetivo
            'status' => $this->faker->randomElement(['active', 'closed', 'cancelled']), //Estatus de la campaña
            'type' => $this->faker->randomElement(['Normal', 'Subasta']), // Aquí agregamos el tipo de campaña
        ];
    }
}
