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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branche_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('loan_option')->nullable()->constrained('loan_options')->cascadeOnDelete();
            $table->string('title');
            $table->integer('amount');
            $table->integer('amount_deduc')->nullable();
            $table->integer('amountpaie')->nullable();
            $table->string('type')->nullable();
            $table->date('deduc_loan')->nullable();
            $table->integer('nbre_mois')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->enum('statut',['pending','running','completed','failed','cancelled'])->default('pending');
            $table->date('prochaine_echeance')->nullable();
            $table->string('month_paie')->nullable();
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
        Schema::dropIfExists('loans');
    }
};
