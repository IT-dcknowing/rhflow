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
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('quar_heure')->nullable();
            $table->integer('heure_audd')->nullable();
            $table->integer('heure_nuit_ferie')->nullable();
            $table->integer('heure_dim_ferie')->nullable();
            $table->integer('heure_nuit_dim_ferie')->nullable();
            $table->string('period')->nullable();
            $table->string('taux_hour')->nullable();
            $table->string('montant')->nullable();
            $table->enum('statut', ['pending', 'approved', 'rejected'])->nullable();
            $table->enum('paid', ['pending', 'paid', 'unpaid'])->nullable();
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
        Schema::dropIfExists('overtimes');
    }
};
