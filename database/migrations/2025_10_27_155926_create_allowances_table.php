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
        Schema::create('allowances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->integer('code_compta')->nullable();
            $table->unsignedBigInteger('employee_id'); // Un employé peut avoir plusieurs allocations
            $table->unsignedBigInteger('allowance_option_id');
            $table->string('title');
            $table->string('trait_fisc')->nullable();
            $table->string('trait_cnps')->nullable();
            $table->boolean('base_heures')->default(false);
            $table->float('amount')->default(0);
            $table->float('amount_imp')->default(0);
            $table->float('montant')->default(0);
            $table->integer('jours_work')->default(0);
            $table->integer('jours_leave');
            $table->enum('type', ['fixed', 'variable'])->default('fixed');
            $table->boolean('type_amount')->default(false);
            $table->text('details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('company_id');
            $table->foreign('employee_id')->references('id')->on('employee_months')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('allowance_option_id')->references('id')->on('allowance_options')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowances');
    }
};
