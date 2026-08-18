<?php

namespace Modules\Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Settings\Models\LoanType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Settings\Models\LoanType>
 */
class LoanTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => 1, // Will be overridden in tests
            'name' => fake()->randomElement(['Prêt Personnel', 'Prêt Logement', 'Prêt Véhicule', 'Prêt Études', 'Prêt Urgence']),
            'description' => fake()->sentence(10),
            'max_amount' => fake()->numberBetween(100000, 10000000),
            'interest_rate' => fake()->randomFloat(1, 2, 15),
            'repayment_period_min' => fake()->numberBetween(1, 6),
            'repayment_period_max' => fake()->numberBetween(12, 60),
            'requires_guarantor' => fake()->boolean(70), // 70% chance of requiring guarantor
            'guarantor_conditions' => fake()->boolean(50) ? fake()->sentence(15) : null,
            'is_active' => true,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    /**
     * Indicate that the loan type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the loan type requires a guarantor.
     */
    public function withGuarantor(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_guarantor' => true,
            'guarantor_conditions' => fake()->sentence(15),
        ]);
    }

    /**
     * Indicate that the loan type doesn't require a guarantor.
     */
    public function withoutGuarantor(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_guarantor' => false,
            'guarantor_conditions' => null,
        ]);
    }

    /**
     * Create a personal loan type.
     */
    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Prêt Personnel',
            'description' => 'Prêt personnel pour les besoins personnels des employés',
            'max_amount' => 2000000,
            'interest_rate' => 5.5,
            'repayment_period_min' => 6,
            'repayment_period_max' => 24,
        ]);
    }

    /**
     * Create a housing loan type.
     */
    public function housing(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Prêt Logement',
            'description' => 'Prêt pour l\'acquisition ou la construction d\'un logement',
            'max_amount' => 10000000,
            'interest_rate' => 3.5,
            'repayment_period_min' => 12,
            'repayment_period_max' => 120,
            'requires_guarantor' => true,
            'guarantor_conditions' => 'Garantie immobilière requise',
        ]);
    }

    /**
     * Create a vehicle loan type.
     */
    public function vehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Prêt Véhicule',
            'description' => 'Prêt pour l\'acquisition d\'un véhicule',
            'max_amount' => 5000000,
            'interest_rate' => 4.5,
            'repayment_period_min' => 6,
            'repayment_period_max' => 48,
            'requires_guarantor' => true,
            'guarantor_conditions' => 'Véhicule en garantie',
        ]);
    }
}
