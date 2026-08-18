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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du module (ex. : 'Core', 'Finance')
            $table->string('alias')->unique(); // Alias unique (ex. : 'core', 'finance')
            $table->text('description')->nullable(); // Description du module
            $table->boolean('is_active')->default(true); // Si le module est actif globalement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
