<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['super_admin', 'company', 'hr', 'paie', 'employee'];
        $languages = ['fr', 'en', 'ar', 'es', 'pt'];
        $attendanceTypes = ['manuel', 'biometrique', 'qr_code', 'carte'];

        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'password_code' => null,
            'type' => fake()->randomElement($types),
            'avatar' => null,
            'lang' => fake()->randomElement($languages),
            'plan' => fake()->numberBetween(1, 5),
            'plan_expire_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'plan_cpte_trait' => fake()->numberBetween(0, 100),
            'requested_plan' => 0,
            'storage_limit' => fake()->randomFloat(2, 0, 100),
            'last_login' => fake()->dateTimeBetween('-1 month', 'now'),
            'is_active' => 1,
            'created_by' => 'system',
            'active_status' => fake()->boolean(90), // 90% de chance d'être actif
            'dark_mode' => fake()->randomElement(['light', 'dark', 'auto']),
            'messenger_color' => '#2180f3',
            'colorone' => fake()->hexColor(),
            'colortwo' => fake()->hexColor(),
            'config_company' => fake()->numberBetween(1, 100),
            'attendance_type' => fake()->randomElement($attendanceTypes),
            'ip_serveur' => fake()->ipv4(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Créer un super administrateur
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'super_admin',
            'name' => 'Super Administrateur',
            'email' => 'admin@rhflow.com',
            'username' => 'superadmin',
            'is_active' => 1,
            'active_status' => 1,
            'plan' => null,
            'plan_expire_date' => null,
            'storage_limit' => 1000.00,
        ]);
    }

    /**
     * Créer une entreprise
     */
    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'company',
            'plan' => fake()->numberBetween(1, 5),
            'plan_expire_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'storage_limit' => fake()->randomFloat(2, 10, 500),
        ]);
    }

    /**
     * Créer un employé
     */
    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'employee',
            'plan' => null,
            'plan_expire_date' => null,
            'storage_limit' => 0.00,
        ]);
    }

    /**
     * Créer un utilisateur RH
     */
    public function hr(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => fake()->randomElement(['hr', 'paie']),
            'plan' => fake()->numberBetween(2, 5),
        ]);
    }

    /**
     * Utilisateur inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => 0,
            'active_status' => 0,
        ]);
    }

    /**
     * Utilisateur avec abonnement expiré
     */
    public function expiredPlan(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_expire_date' => fake()->dateTimeBetween('-1 month', '-1 day')->format('Y-m-d'),
        ]);
    }
}
