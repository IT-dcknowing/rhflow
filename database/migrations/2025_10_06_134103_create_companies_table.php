<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('France');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('industry')->nullable(); // Secteur d'activité
            $table->enum('size', ['startup', 'small', 'medium', 'large', 'enterprise'])->default('small');
            $table->text('description')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();

            // Informations de travail
            $table->integer('working_hours_per_week')->default(40); // Nombre d'heures de travail par semaine
            $table->integer('working_days_per_week')->default(5); // Nombre de jours travaillés par semaine

            // Documents électroniques
            $table->string('electronic_signature')->nullable(); // Signature électronique
            $table->string('electronic_stamp')->nullable(); // Cachet électronique

            // Statut et abonnement
            $table->boolean('is_active')->default(true);
            $table->enum('subscription_status', ['trial', 'active', 'suspended', 'cancelled', 'expired'])->default('trial');
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();

            // Limites
            $table->integer('max_employees')->nullable(); // null = illimité
            $table->decimal('max_storage_gb', 8, 2)->default(5.00);
            $table->decimal('current_storage_used', 8, 2)->default(0.00);

            // Configuration
            $table->json('settings')->nullable();

            // Audit
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->timestamps();

            // Index pour les performances
            $table->index(['is_active', 'subscription_status']);
            $table->index(['subscription_end_date']);
            $table->index('industry');
            $table->index('size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
