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
        Schema::create('paie_periodes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->foreignId('exercice_id')->constrained('paie_exercices')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('date_paiement');
            $table->enum('type_periode', ['mensuelle', 'quinzaine', 'hebdomadaire', 'autre']);
            $table->enum('statut', ['brouillon', 'en_cours', 'validee', 'payee', 'annulee'])->default('brouillon');
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paie_periodes');
    }
};
