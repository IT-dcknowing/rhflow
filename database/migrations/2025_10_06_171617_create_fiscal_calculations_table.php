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
        Schema::create('fiscal_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->string('calculation_type')->default('monthly'); // monthly, yearly, etc.
            $table->string('period'); // YYYY-MM or YYYY
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('taxable_income', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('social_contributions', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->string('tax_country', 2)->default('SN'); // Code pays (SN, BF, CI, etc.)
            $table->year('tax_year');
            $table->json('calculation_details')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'period']);
            $table->index(['employee_id', 'period']);
            $table->index(['tax_country', 'tax_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_calculations');
    }
};
