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
        Schema::create('type_retenues', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('retenues', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->foreignId('type_retenue_id')->nullable()->constrained('type_retenues')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branche_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->integer('base')->nullable();
            $table->integer('taux')->nullable();
            $table->integer('amount');
            $table->string('month_paie')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retenues');
        Schema::dropIfExists('type_retenues');
    }
};
