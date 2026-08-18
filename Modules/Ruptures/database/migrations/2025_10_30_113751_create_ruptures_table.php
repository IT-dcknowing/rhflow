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
        Schema::create('ruptures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('rupture_type_id')->constrained('rupture_types')->onDelete('cascade');
            $table->foreignId('contrat_id')->constrained('contracts')->onDelete('cascade');
            $table->date('notice_date');
            $table->date('termination_date');
            $table->string('indem_comp')->nullable();
            $table->string('indem_comp_cong')->nullable();
            $table->string('imdem_prea')->nullable();
            $table->string('indem_licence')->nullable();
            $table->string('aggravation')->nullable();
            $table->string('dom_inter')->nullable();
            $table->integer('amount_cnps')->nullable();
            $table->integer('amount_its')->nullable();
            $table->integer('amount_loan')->nullable();
            $table->integer('solde')->nullable();
            $table->text('description')->nullable();
            $table->integer('statut')->nullable();
            $table->integer('traiter')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruptures');
    }
};
