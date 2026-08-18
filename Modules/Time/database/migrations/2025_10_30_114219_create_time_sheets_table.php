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
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->date('arrival_date')->nullable();
            $table->float('hours')->default(0.00);
            $table->string('type_permis')->nullable();
            $table->string('motif_justify')->nullable();
            $table->text('remark')->nullable();
            $table->integer('retenue')->nullable();
            $table->tinyInteger('deduc_abs')->nullable();
            $table->string('approbation')->nullable();
            $table->enum('statut', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('document')->nullable();
            $table->string('monthpaie');
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
        Schema::dropIfExists('time_sheets');
    }
};
