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
        Schema::create('avantages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branche_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->enum('type_avantage', ['avantage_en_nature', 'avantage_en_argent', 'assurance_sante', 'assurance_vie_complementaire'])->default('avantage_en_nature');
            $table->string('libelle');
            $table->integer('montant_reel');
            $table->integer('amount')->nullable();
            $table->json('details')->nullable();
            $table->integer('taxe_its')->nullable();
            $table->integer('taxe_cnps')->nullable();
            $table->enum('traitement', ['mensuelle','annuelle'])->default('mensuelle');
            $table->enum('status', ['pending','approved','reject','actif','stop'])->default('pending');
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avantages');
    }
};
