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
        Schema::create('pack_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade'); // FK vers plans (packs)
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade'); // FK vers modules
            $table->boolean('is_active')->default(true); // Si le module est actif pour ce pack
            $table->timestamps();

            // Index unique pour éviter les doublons plan-module
            $table->unique(['plan_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pack_modules');
    }
};
