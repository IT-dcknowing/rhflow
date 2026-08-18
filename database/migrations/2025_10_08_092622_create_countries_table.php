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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 3)->unique(); // Code ISO 3 lettres
            $table->string('phone_code', 10)->nullable(); // Indicatif téléphonique
            $table->string('flag', 10)->nullable(); // Emoji ou code du drapeau
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexation pour optimiser les performances
            $table->index(['is_active', 'sort_order']);
            $table->index(['code']);
            $table->index(['name']);
            $table->index(['phone_code']);

            // Index unique composite pour éviter les doublons
            $table->unique(['name', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
